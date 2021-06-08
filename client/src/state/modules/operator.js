import axios from 'axios';

export const state = {
    operators: {},
    operators_select: [],
    operators_for_bots: [],
    operator: {
        name: "",
        password: "",
    },
    spiner: false,
};

export const mutations = {
    SET_OPERATORS(state, newValue) {
        state.operators = newValue;
    },
    SET_OPERATOR(state, newValue) {
        state.operator = newValue;
    },
    SET_OPERATORS_SELECT(state, newValue) {
        state.operators_select = newValue;
    },
    SET_OPERATORS_FOR_BOTS(state, newValue) {
        state.operators_for_bots = newValue;
    },
    showSpinner(state) {
        state.spiner = true;
    },
    hideSpinner(state) {
        state.spiner = false;
    },
    clearOperatorSelect(state) {
        state.operators_select = [];
    },
    CLEAR_OPERATORS_FOR_BOTS(state) {
        state.operators_for_bots = [];
    },
};

export const getters = {};

export const actions = {
    async init({dispatch}) {
    },
    async getIndex({commit, dispatch}, {page, params}) {
        dispatch('showSpinner');
        const operators = await axios.get('/api/operators', {params});
        dispatch('hideSpinner');
        commit('SET_OPERATORS', operators.data);
        return operators.data;
    },
    async getSelect({commit, state}) {
        if (state.operators_select.length) {
            return state.operators_select;
        }
        const operators = await axios.get('/api/operators/for-select');
        commit('SET_OPERATORS_SELECT', operators.data);
        return operators.data;
    },
    async loadForBots({commit, state}) {
        if (state.operators_for_bots.length) {
            return state.operators_for_bots;
        }
        const operators = await axios.get('/api/operators/for-bots');
        commit('SET_OPERATORS_FOR_BOTS', operators.data);
        return operators.data;
    },
    async create({commit}, field) {
        const operator = await axios.post('/api/operators', field);
        commit('clearOperatorSelect');
        commit('CLEAR_OPERATORS_FOR_BOTS');

        return operator.data;
    },
    async deleteById({commit}, id) {
        const operator = await axios.delete(`/api/operators/${id}`);
        commit('clearOperatorSelect');
        commit('CLEAR_OPERATORS_FOR_BOTS');

        return operator.data;
    },
    async getById({commit, dispatch}, id) {
        dispatch('showSpinner');
        const operator = await axios.get(`/api/operators/${id}`);
        commit('SET_OPERATOR', operator.data);
        dispatch('hideSpinner');
        return operator.data;
    },
    async edit({commit}, field) {
        const operator = await axios.put(`/api/operators/${field.number}`, field);
        commit('SET_OPERATOR', operator.data);
        commit('clearOperatorSelect');
        commit('CLEAR_OPERATORS_FOR_BOTS');
        return operator.data;
    },
    showSpinner({commit}) {
        commit('showSpinner');
    },
    hideSpinner({commit}) {
        commit('hideSpinner');
    }
};

