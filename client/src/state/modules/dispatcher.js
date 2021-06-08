import axios from 'axios';

export const state = {
    dispatcher: {
        bot_id: null,
        messages: "",
    },
    dispatchers: {},
    spiner: false,
};

export const mutations = {
    setDispatchText(state, value){
        state.dispatcher.messages = value.messages;
    },
    setDispatchers(state, value){
        state.dispatchers = value;
    },
    showSpinner(state){
        state.spiner = true;
    },
    hideSpinner(state){
        state.spiner = false;
    }
};

export const getters = {};

export const actions = {
    async init({dispatch}) {
    },
    async getIndex({commit, dispatch}, {page, params}) {
        page = page || 1;
        dispatch('showSpinner');
        const dispatches = await axios.get(`/api/dispatches?page=${page}`, {params});
        dispatch('hideSpinner')
        commit('setDispatchers', dispatches.data);
        return dispatches.data;
    },
    async getSelect({commit}) {
        const operators = await axios.get('/api/operators/for-select');
        commit('SET_OPERATORS_SELECT', operators.data);
        return operators.data;
    },
    async create({commit}, fields) {
        const operator = await axios.post('/api/dispatches', fields);
        return operator.data;
    },
    async deleteById({commit}, id) {
        const operator = await axios.delete(`/api/operators/${id}`);
        return operator.data;
    },
    async getById({commit}, id){
        const operator = await axios.get(`/api/operators/${id}`);
        commit('SET_OPERATOR', operator.data);
        return operator.data;
    },
    async getTextProductExist({commit}, number){
        const text = await axios.get(`/api/dispatch/get-product-exist-text/${number}`);
        commit('setDispatchText', text.data);
        return text.data;
    },
    showSpinner({commit}){
        commit('showSpinner');
    },
    hideSpinner({commit}){
        commit('hideSpinner');
    }

};

