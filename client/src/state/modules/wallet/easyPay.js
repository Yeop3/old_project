import axios from 'axios';

export const state = {
    easyPayWallets: {},
    easyPayWallet: {
        name: null,
        wallet_number: null,
        phone: null,
        password: null,
        proxy_number: null,

    },
    easyPayWalletSelect: [
        {value: null, text: "Все кошельки"}
    ],
    spiner: false,
    select: [{text: 'Все', value: null}],
    type: [
        {
            text: 'Email',
            value: 'text'
        },
        {
            text: 'Телефон',
            value: 'phone'
        },
    ]
};

export const mutations = {
    setEasyPayWallets(state, newValue) {
        state.easyPayWallets = newValue;
        state.easyPayWallets.data.map((value) => {
            value.balance = value.balance.amount / 100;
            value.limit = value.limit.amount / 100;
            if (value.wrong_creadentials) {
                value._rowVariant = 'danger';
                return value;
            }

            if (value.is_limit) {
                value._rowVariant = 'danger';
                return value;
            }

            if (value){

            }

            return value;
        });
       // console.log(state.easyPayWallets);
    },
    setEasyPayWallet(state, newValue) {
        state.easyPayWallet = newValue;
        state.easyPayWallet.password = "";
        state.easyPayWallet.proxy_number = newValue.proxy.number;
        state.easyPayWallet.limit = state.easyPayWallet.limit.amount / 100;
        state.easyPayWallet.balance = state.easyPayWallet.balance.amount / 100;

        if (state.easyPayWallet.wrong_creadentials) {
            value._rowVariant = 'danger';
        }
    },
    setEasyPayWalletSelect(state, newValue) {
        state.easyPayWalletSelect = newValue;
        state.easyPayWalletSelect.unshift({value: null, text: "Все кошельки"});

    },
    showSpinner(state) {
        state.spiner = true;
    },
    hideSpinner(state) {
        state.spiner = false;
    },
    setSelect(state, newValue) {
        state.select = newValue;
        state.select.unshift({text: 'Все', value: null});
    }
};

export const getters = {};

export const actions = {
    async loadEasyPayWallet({commit, dispatch}, {page, params}) {
        dispatch('showSpinner');
        try {
            const easyPayWallets = await axios.get(`api/easy-pay/wallet?page=${page}`, {params});
            commit('setEasyPayWallets', easyPayWallets.data);
            return easyPayWallets.data;
        } finally {
            dispatch('hideSpinner');
        }
    },
    async create({commit}, field) {
        const easyPayWallet = await axios.post(`api/easy-pay/wallet`, field);
        return easyPayWallet.data;
    },
    async deleteEasyPayWallet({commit}, id) {
        const easyPayWallet = await axios.delete(`api/easy-pay/wallet/${id}`);
        return easyPayWallet.data;
    },
    async getEasyPayWallet({commit, dispatch}, id) {
        dispatch('showSpinner');
        try {
            const easyPayWallet = await axios.get(`api/easy-pay/wallet/${id}`);
            commit('setEasyPayWallet', easyPayWallet.data);
            return easyPayWallet.data;
        } finally {
            dispatch('hideSpinner');
        }
    },
    async getEasyPayWalletSelect({commit}) {

        try {
            const easyPayWallet = await axios.get(`api/easy-pay/wallet/get-select`);
            commit('setEasyPayWalletSelect', easyPayWallet.data);
            return easyPayWallet.data;
        } finally {

        }
    },
    async update({commit, dispatch}, {id, field}) {
        const easyPayWalleto = await axios.put(`api/easy-pay/wallet/${id}`, field);
        return easyPayWalleto.data;
    },

    showSpinner({commit}) {
        commit('showSpinner');
    },
    hideSpinner({commit}) {
        commit('hideSpinner');
    },
    async checkAccount({commit}, id) {
        const data = await axios.post(`api/easy-pay/wallet/check/${id}`);
        commit('setEasyPayWallet', data.data);
        return data.data;
    },
    async restoreBalanceWallet({commit}, number){
        const data = await axios.post(`api/easy-pay/wallet/restore-balance/${number}`);
    }
};