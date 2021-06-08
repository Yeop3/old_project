import axios from "axios";

if (process.env.VUE_APP_API_URL) {
    axios.defaults.baseURL = process.env.VUE_APP_API_URL;
} else {
    axios.defaults.baseURL = `//${window.location.host}:${process.env.VUE_APP_API_PORT || 8080}`;
}

import {Settings} from 'luxon';

Settings.defaultLocale = 'rus';

let user = null;

try {
    user = JSON.parse(window.localStorage.getItem('auth.currentUser'));
} catch (e) {
    user = null;
}
if (!user) {
} else {
    axios.defaults.headers.common.Authorization = `bearer ${user.token}`;
}

import Vue from 'vue';
import App from './app';
import router from '@router';
import store from '@state/store';
import '@components/_globals';

import BootstrapVue from 'bootstrap-vue';
import VueClipboard from 'vue-clipboard2';


// Don't warn about using the dev version of Vue in development.
Vue.config.productionTip = process.env.NODE_ENV === 'production';


import moment from 'moment-timezone';

Vue.prototype.moment = moment;

axios.interceptors.response.use((response) => {
    return response;

}, function (error) {
    if (!error.response) {
        return Promise.reject(error);
    }

    const originalRequest = error.config;
    const apiRefreshTokenPath = '/api/refresh';
    const apiUserPath = '/api/user';

    if (originalRequest.url.includes(apiRefreshTokenPath) || originalRequest._retry) {
        store.dispatch('auth/logOut');
        router.push('/login');
        return Promise.reject(error);
    }

    if (
      error.response.status === 401
      && originalRequest.headers.Authorization
      && !originalRequest.url.includes(apiUserPath)
    ) {
        originalRequest._retry = true;
        return axios.get(apiRefreshTokenPath)
            .then(res => {
                if (res.status === 200) {
                    const newToken = res.data.token;
                    store.commit('auth/SET_TOKEN', newToken);
                    originalRequest.headers.Authorization = 'bearer ' + newToken;
                    return axios(originalRequest);
                }

                return Promise.reject(error);
            });
    }

    if (error.response.status === 403){
        router.push('/403');
        return Promise.reject(error);
    }

    return Promise.reject(error);
});

// If running inside Cypress...
if (process.env.VUE_APP_TEST === 'e2e') {
    // Ensure tests fail when Vue emits an error.
    Vue.config.errorHandler = window.Cypress.cy.onUncaughtException;
}
// axios.defaults.baseURL='https://dejavugromrock.tk';
Vue.use(BootstrapVue);

Vue.use(VueClipboard);

const app = new Vue({
    router,
    store,
    render: (h) => h(App),
}).$mount('#app');

// If running e2e tests...
if (process.env.VUE_APP_TEST === 'e2e') {
    // Attach the app to the window, which can be useful
    // for manually setting state in Cypress commands
    // such as `cy.logIn()`.
    window.__app__ = app;
}
