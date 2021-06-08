import axios from 'axios';

export const state = {
    crypto: {},
    crypt: {
        name: "",
        proxy_id: null,
        currency: null,
        comment: "",
        address: "",
        confirmations: 3
    },
    spiner: false,
    select: [{text: "Все", value: null}],
};

export const mutations = {
    setCrypto(state, newValue) {
        state.crypto = newValue;
    },
    setCrypt(state, newValue) {
        state.crypt = newValue;
    },
    showSpinner(state) {
        state.spiner = true;
    },
    hideSpinner(state) {
        state.spiner = false;
    },
    setSelect(state, newValue){
        state.select = newValue;
        state.select.unshift({text: "Все", value: null});
    }
};

export const getters = {};


export const actions = {
    async getIndex({commit, dispatch}, {page, params}) {
        dispatch('showSpinner');
        try {
            const crypto = await axios.get(`api/wallet/crypto?page=${page}`, {params});
            commit('setCrypto', crypto.data);
            return crypto.data;
        }finally {
            dispatch('hideSpinner');
        }
    },
    async create({commit}, field) {
        const crypto = await axios.post(`api/wallet/crypto`, field);
        return crypto.data;
    },
    async deleteCrypto({commit}, id) {
        const crypto = await axios.delete(`api/wallet/crypto/${id}`);
        return crypto.data;
    },
    async getCrypt({commit, dispatch}, id) {
        dispatch('showSpinner');
        try {
           const crypto = await axios.get(`api/wallet/crypto/${id}`);
           commit('setCrypt', crypto.data);
           return crypto.data;
        } finally {
           dispatch('hideSpinner');
        }
    },
    async update({commit, dispatch}, {id, field}) {
        const crypto = await axios.put(`api/wallet/crypto/${id}`, field);
        commit('setCrypt', crypto.data);
        return crypto.data;
    },
    async getSelect({commit, dispatch}) {
        const crypto = await axios.get(`api/wallet/crypto/select/`);
        commit('setSelect', crypto.data);
        return crypto.data;
    },


    showSpinner({commit}) {
        commit('showSpinner');
    },
    hideSpinner({commit}) {
        commit('hideSpinner');
    }
};