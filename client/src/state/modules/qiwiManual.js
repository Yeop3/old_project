import axios from 'axios';

export const state = {
    qiwiManual: {},
    qiwiManuals: {},
    qiwiManualsDeleted: {
        data: [],
    },
    qiwiManualDeleted: {},
    spiner: false,
};

export const mutations = {
    setQiwiManuals(state, newValue) {
        state.qiwiManuals = newValue;
    },
    setQiwiManualsDeleted(state, newValue) {
        state.qiwiManualsDeleted = newValue;
    },
    setQiwiManualDeleted(state, newValue) {
        state.qiwiManualDeleted = newValue;
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
    async getIndex({commit, dispatch}, page=1) {
        dispatch('showSpinner');
        const qiwi_manual = await axios.get(`/api/wallets/qiwi_manual?page=${page}`);
        commit('setQiwiManuals', qiwi_manual.data);
        dispatch('hideSpinner');
        return qiwi_manual.data;
    },
    async edit({commit}, field) {
        const qiwi_manual = await axios.put(`/api/wallets/qiwi_manual/${field.number}`, field);
        return qiwi_manual.data;
    },
    async deleteById({commit}, id) {
        const qiwi_manual = await axios.delete(`/api/wallets/qiwi_manual/${id}`);
    },
    async deleteForeverById({commit}, id) {
        const qiwi_manual = await axios.delete(`/api/wallets/qiwi_manual/${id}/forever`);
    },
    async create({commit}, field) {
        const qiwi_manual = await axios.post(`/api/wallets/qiwi_manual/`, field);
        return qiwi_manual.data;
    },
    //Deleted
    async getIndexDeleted({commit, dispatch}, page=1) {
        dispatch('showSpinner');
        const qiwi_manual_deleted = await axios.get(`api/wallets/qiwi_manual/deleted?page=${page}`);
        dispatch('hideSpinner');
        commit('setQiwiManualsDeleted', qiwi_manual_deleted.data);
        return qiwi_manual_deleted.data;
    },
    async restoreQiwiById({commit}, id){
        const qiwi_manual_deleted = await axios.post(`api/wallets/qiwi_manual/deleted/${id}/restore`);
        return qiwi_manual_deleted.data;
    },
    async deleteByIdDeleted({commit}, id) {
        const qiwi_manual = await axios.delete(`/api/wallets/qiwi_manual/${id}/forever`);
    },
    async qiwiDeletedClear({commit}) {
        const qiwi_manual = await axios.post(`/api/wallets/qiwi_manual/deleted/clear`);
    },
    showSpinner({commit}){
        commit('showSpinner');
    },
    hideSpinner({commit}){
        commit('hideSpinner');
    }
};
