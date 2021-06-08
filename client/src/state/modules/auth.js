import axios from 'axios';

export const state = {
    currentUser: getSavedState('auth.currentUser'),
    type: [{text: 'Админ', value: null}],
};

export const mutations = {
    SET_CURRENT_USER(state, newValue) {
        state.currentUser = newValue;
        saveState('auth.currentUser', newValue);
        setDefaultAuthHeaders(state);
    },
    SET_TOKEN(state, token) {
        const user = {...state.currentUser}
        user.token = token
        state.currentUser = user
        saveState('auth.currentUser', user);
        setDefaultAuthHeaders(state);
    },
    setType(state, newValue) {
        state.type = newValue;
        state.type.unshift({text: 'Админ', value: null});
    }
};

export const getters = {
    // Whether the user is currently logged in.
    loggedIn(state) {
        return !!state.currentUser;
    },
};

export const actions = {
    // This is automatically run in `src/state/store.js` when the app
    // starts, along with any other actions named `init` in other modules.
    init({state, dispatch, commit}) {
        setDefaultAuthHeaders(state);
    },

    // Logs in the current user.
    logIn({commit, dispatch, getters}, {password, type} = {}) {
        if (getters.loggedIn) return dispatch('validate');

        return axios
            .post('/api/login', {password, type})
            .then((response) => {
                const user = response.data;
                commit('SET_CURRENT_USER', user);
                return user;
            });
    },

    // Logs out the current user.
    logOut({commit}) {
        commit('SET_CURRENT_USER', null);
    },

    // register the user
    register({commit, dispatch, getters}, {fullname, email, password} = {}) {
        if (getters.loggedIn) return dispatch('validate');

        return axios
            .post('/api/register', {fullname, email, password})
            .then((response) => {
                return response.data;
            });
    },

    // register the user
    resetPassword({commit, dispatch, getters}, {email} = {}) {
        if (getters.loggedIn) return dispatch('validate');

        return axios
            .post('/api/reset', {email})
            .then((response) => {
                const message = response.data;
                return message;
            });
    },

    // Validates the current user's token and refreshes it
    // with new data from the API.
    validate({commit, state}) {
        if (!state.currentUser) return Promise.resolve(null);

        return axios
            .get('/api/user')
            .then((response) => {
                const user = response.data;
                commit('SET_CURRENT_USER', user);
                return user;
            })
            .catch((error) => {
                if (error.response && error.response.status === 401) {
                    commit('SET_CURRENT_USER', null);
                }
                return null;
            });
    },


    async getSelectType({commit}) {
        const types = await axios.get('/api/auth/select');
        commit('setType', types.data);
    }
};

// ===
// Private helpers
// ===

function getSavedState(key) {
    try {
        return JSON.parse(window.localStorage.getItem(key));
    } catch (e) {
        return null;
    }
}

function saveState(key, state) {
    window.localStorage.setItem(key, JSON.stringify(state));
}

function setDefaultAuthHeaders(state) {
    axios.defaults.headers.common.Authorization = state.currentUser
        ? `bearer ${state.currentUser.token}`
        : '';
}
