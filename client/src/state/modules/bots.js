import axios from 'axios';
import queryString from 'query-string';

export const state = {
    bot: null,
    bot_webhookinfo: null,
    bots: null,
    page: 1,
    pagesCount: null,
    total: null,
    perPage: null,
    select_bots: [],
    select_bots_for_client: [],
    spiner: false,
};

export const mutations = {
    SET_BOT(state, newValue) {
        state.bot = newValue;
    },
    SET_BOT_WEBHOOK(state, newValue) {
        state.bot_webhookinfo = newValue;
    },
    SET_BOTS(state, newValue) {
        state.bots = newValue;
    },
    SET_PAGINATION_DATA(state, {pagesCount, total, perPage}) {
        state.pagesCount = pagesCount;
        state.total = total;
        state.perPage = perPage;
    },
    SET_PAGE(state, newPage) {
        state.page = newPage;
    },
    SET_SELECT(state, newValue) {
        state.select_bots = newValue;
        state.select_bots.unshift({text: "Все", value: null});
    },
    SET_SELECT_FOR_CLIENT(state, newValue) {
        state.select_bots_for_client = newValue;
        state.select_bots_for_client.unshift({text: "Не выбран", value: null});
    },
    showSpinner(state){
        state.spiner = true;
    },
    hideSpinner(state){
        state.spiner = false;
    },
    clearCache(state) {
        state.select_bots = [];
    },
};

export const getters = {};

export const actions = {
    init({state, dispatch}) {
    },
    create({commit, actions}, data) {
        return axios
            .post('/api/bots', {
                ...data,
                type: 1,
                messenger: 1,
            })
            .then((response) => {
                commit('clearCache');
            });
    },
    // edit({commit}, {name, id} = {}) {
    //   return axios
    //     .put(`/api/drivers/${id}`, {name})
    //     .then((response) => {
    //       const driver = response.data;
    //       commit('SET_DRIVER', driver);
    //       return driver;
    //     });
    // },
    loadAll({commit, state, dispatch}, {page, params}) {
        dispatch('showSpinner');
        return axios
            .get(`/api/bots?${page || params.page}`, {params})
            .then((response) => {
                commit('SET_BOTS', response.data.data);
                commit('SET_PAGINATION_DATA', {
                    pagesCount: response.data.last_page,
                    total: response.data.total,
                    perPage: response.data.per_page,
                });
                return response.data;
            }).catch(()=>{}).finally(() => dispatch('hideSpinner'));
    },
    load({commit, dispatch}, number) {
        dispatch('showSpinner');
        return axios
            .get(`/api/bots/${number}`)
            .then((response) => {
                commit('SET_BOT', response.data);
                return response.data;
            }).finally(() => dispatch('hideSpinner'));
    },
    getWebhookInfo({commit}, token) {
        return axios
            .get('https://api.telegram.org/bot' + token + '/getWebhookInfo')
            .then((response) => {
                commit('SET_BOT_WEBHOOK', response.data.result);
                return response.data.result;
            });
    },
    update({commit}, data) {
        return axios
            .put('/api/bots/' + data.number, data)
            .then((response) => {
                commit('clearCache');
            });
    },
    delete({commit}, number) {
        return axios
            .delete('/api/bots/' + number)
            .then((response) => {
                commit('clearCache');
            });
    },
    reinstallWebhook({commit}, number) {
        return axios
            .get(`/api/bots/${number}/reinstall_webhook`)
            .then((response) => {
                return response.data;
            });
    },
    async getSelect({commit, state}) {
        if (state.select_bots.length) {
            return state.select_bots;
        }
        const select = await axios.get(`api/bots/select`);
        commit('SET_SELECT', select.data);
        return select.data;
    },
    async getSelectByClient({commit}, client_number) {
        const select = await axios.get(`api/bots/select`, {
            params: {
                client_number
            }
        });
        commit('SET_SELECT_FOR_CLIENT', select.data);
        return select.data;
    },
    showSpinner({commit}){
        commit('showSpinner');
    },
    hideSpinner({commit}){
        commit('hideSpinner');
    }
};

