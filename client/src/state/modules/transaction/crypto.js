import axios from 'axios';

export const state = {
    crypto: {},
    crypt: {
        name: "",
        proxy_id: null,
        currency: null,
        comment: "",
        address: "",
    },
    spiner: false,
};

export const mutations = {
    setCrypto(state, newValue) {
        state.crypto = newValue;
    },
    showSpinner(state) {
        state.spiner = true;
    },
    hideSpinner(state) {
        state.spiner = false;
    }
};

export const getters = {};


export const actions = {
    async getIndex({commit, dispatch}, {page, params}) {
        dispatch('showSpinner');
        try {
            const crypto = await axios.get(`api/transaction/crypto?page=${page}`, {params});
            commit('setCrypto', crypto.data);
            return crypto.data;
        } finally {
            dispatch('hideSpinner');
        }
    },

    showSpinner({commit}) {
        commit('showSpinner');
    },
    hideSpinner({commit}) {
        commit('hideSpinner');
    }
};