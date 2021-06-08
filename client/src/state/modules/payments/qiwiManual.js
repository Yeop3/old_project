import axios from 'axios';

export const state = {
    qiwiManual: {
        amount: 0,
        client_wallet: "",
        comment: "",
        order_number: null,
    },
    qiwiManuals: {},
    qiwiManualPhone: [],
    spiner: false,
};

export const mutations = {
    setQiwiManuals(state, newValue) {
        state.qiwiManuals = newValue;
    },
    setQiwiManualPhone(state, newValue) {
        state.qiwiManualPhone = newValue;
    },
    setQiwiManual(state, newValue) {
        state.qiwiManual = newValue;
        state.qiwiManual.amount = state.qiwiManual.sum;
        state.qiwiManual.order_number = state.qiwiManual.order.number;
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
    async getIndex({commit,dispatch}, {page, params}) {
        dispatch('showSpinner');
        page = page || 1;
        const qiwi_manual = await axios.get(`api/payments/qiwi_manual?page=${page}`, {params});
        commit('setQiwiManuals', qiwi_manual.data);
        dispatch('hideSpinner');
        return qiwi_manual.data;
    },
    async edit({commit}, field) {
        const qiwi_manual = await axios.put(`/api/payments/qiwi_manual/${field.number}`, field);
        return qiwi_manual.data;
    },
    async create({commit}, field) {
        const qiwi_manual = await axios.post(`/api/payments/qiwi_manual/`, field);
        return qiwi_manual.data;
    },
    async getById({commit}, id) {
        const qiwi_manual = await axios.get(`/api/payments/qiwi_manual/${id}`);
        commit('setQiwiManual', qiwi_manual.data);
        return qiwi_manual.data;
    },
    async getSelectPhone({commit}) {
        const qiwi_manual = await axios.get(`/api/payments/qiwi_manual/phones-select`);
        commit('setQiwiManualPhone', qiwi_manual.data);
        return qiwi_manual.data;
    },
    showSpinner({commit}){
        commit('showSpinner');
    },
    hideSpinner({commit}){
        commit('hideSpinner');
    }
};
