import axios from 'axios';
import queryString from 'query-string';

export const state = {
    proxy: null,
    proxies: [],
    spiner: false,
    proxySelect: [],
};

export const mutations = {
    SET_PROXY(state, newValue) {
        state.proxy = newValue;
    },
    SET_PROXIES(state, newValue) {
        state.proxies = newValue;
    },
    showSpinner(state) {
        state.spiner = true;
    },
    hideSpinner(state) {
        state.spiner = false;
    },
    setProxySelect(state, newValue) {
        state.proxySelect = newValue;
        state.proxySelect.unshift({text: "Не выбрано", value: null});

    }
};

export const getters = {
    proxyList: s => s.proxySelect
};

export const actions = {
    init({state, dispatch}) {
    },
    loadProxies({commit, dispatch}, params) {
        dispatch('showSpinner');
        return axios
            .get(`/api/proxies`, {params})
            .then((response) => {
                commit('SET_PROXIES', response.data.data);
                return response.data.data;
            }).finally(() => dispatch('hideSpinner'));
    },

    loadProxy({commit, dispatch}, number) {
        dispatch('showSpinner');
        return axios
            .get(`/api/proxies/${number}`)
            .then((response) => {
                commit('SET_PROXY', response.data);
                return response.data;
            }).finally(() => dispatch('hideSpinner'));
    },
    createProxy({commit}, {ip, port, username, password, proxy_type, note}) {
        return axios.post(`/api/proxies`, {ip, port, username, password, proxy_type, note});
    },
    deleteProxy({commit}, number) {
        return axios
            .delete(`/api/proxies/${number}`);
    },
    updateProxy({commit}, {number, ip, port, username, password, proxy_type, note}) {
        return axios
            .put(`/api/proxies/${number}`, {ip, port, username, password, proxy_type, note});
    },
    async getSelectProxy({commit}) {
        const select = await axios.get('/api/proxies/for-select');
        commit('setProxySelect', select.data);
        return select.data;
    },
    showSpinner({commit}) {
        commit('showSpinner');
    },
    hideSpinner({commit}) {
        commit('hideSpinner');
    }
};