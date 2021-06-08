import axios from "axios";

axios.defaults.baseURL = process.env.VUE_APP_API_URL;

import Vue from 'vue';
import App from './app';
import router from '@router';
import store from '@state/store';
import '@components/_globals';

import BootstrapVue from 'bootstrap-vue';

import VueClipboard from 'vue-clipboard2'

// Don't warn about using the dev version of Vue in development.
Vue.config.productionTip = process.env.NODE_ENV === 'production';

// If running inside Cypress...
if (process.env.VUE_APP_TEST === 'e2e') {
  // Ensure tests fail when Vue emits an error.
  Vue.config.errorHandler = window.Cypress.cy.onUncaughtException;
}


Vue.use(VueClipboard);

Vue.use(BootstrapVue);

import moment from 'moment';

Vue.prototype.moment = moment;

axios.interceptors.response.use((response) => {
  return response

}, function (error) {
  const originalRequest = error.config;
  const apiRefreshTokenPath = '/api/refresh'

  if (originalRequest.url === (axios.defaults.baseURL + apiRefreshTokenPath) && (error.response.status === 401 || error.response.status === 500)) {
    store.dispatch('auth/logOut')
    router.push('/login');
    return Promise.reject(error);
  }

  if (error.response.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;
      return axios.get(apiRefreshTokenPath)
          .then(res => {
              if (res.status === 200) {
                const newToken = res.data.token
                store.commit('auth/SET_TOKEN', newToken)
                originalRequest.headers.Authorization = 'bearer ' + newToken
                return axios(originalRequest);
              }
          })
  }
  return Promise.reject(error);
});

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
