import axios from 'axios';

export const state = {
    globalMoneyWallets: {},
    accessGlobalMoneyWallet: {
        login: null,
        password: null,
        type: 'text',
    },
    globalMoneyWallet: {
        name: null,
        wallet_number: null,
        type: 'text',
        login: null,
        password: null,
        proxy_id: null,

    },
    globalMoneyWalletSelect: [
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
    setGlobalMoneyWallets(state, newValue) {
        state.globalMoneyWallets = newValue;
        state.globalMoneyWallets.data = state.globalMoneyWallets.data.map((value) => {
            switch (value.wrong_credentials) {
                case 0:
                    break;
                case 1:
                    value._rowVariant = 'danger';
                    break;
            }
            return value;
        });
    },
    setAccessGlobalMoneyWallet(state, newValue) {
        state.accessGlobalMoneyWallet.login = newValue.login;
        state.accessGlobalMoneyWallet.password = newValue.password;
        state.accessGlobalMoneyWallet.type = newValue.type;
    },
    setGlobalMoneyWallet(state, newValue) {
        state.globalMoneyWallet = newValue;
        state.globalMoneyWallet.password = "";
        state.globalMoneyWallet.proxy_id = newValue.proxy.number;
    },
    setGlobalMoneyWalletSelect(state, newValue) {
        state.globalMoneyWalletSelect = newValue;
        state.globalMoneyWalletSelect.unshift({value: null, text: "Все кошельки"});

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
    async loadGlobalMoneyWallet({commit, dispatch}, {page, params}) {
        dispatch('showSpinner');
        try {
            const globalMoneyWallets = await axios.get(`api/global-money/wallet?page=${page}`, {params});
            commit('setGlobalMoneyWallets', globalMoneyWallets.data);
            return globalMoneyWallets.data;
        } finally {
            dispatch('hideSpinner');
        }
    },
    async create({commit}, field) {
        const globalMoneyWallet = await axios.post(`api/global-money/wallet`, field);
        return globalMoneyWallet.data;
    },
    async deleteGlobalMoneyWallet({commit}, id) {
        const globalMoneyWallet = await axios.delete(`api/global-money/wallet/${id}`);
        return globalMoneyWallet.data;
    },
    async getGlobalMoneyWallet({commit, dispatch}, id) {
        dispatch('showSpinner');
        try {
            const globalMoneyWallet = await axios.get(`api/global-money/wallet/${id}`);
            // console.log(globalMoneyWallet.data);
            commit('setAccessGlobalMoneyWallet', globalMoneyWallet.data);
            commit('setGlobalMoneyWallet', globalMoneyWallet.data);
            return globalMoneyWallet.data;
        } finally {
            dispatch('hideSpinner');
        }
    },
    async getGlobalMoneyWalletSelect({commit}) {

        try {
            const globalMoneyWallet = await axios.get(`api/global-money/wallet/get-select`);
            commit('setGlobalMoneyWalletSelect', globalMoneyWallet.data);
            return globalMoneyWallet.data;
        } finally {

        }
    },
    async update({commit, dispatch}, {id, field}) {
        //console.log(field);
        const globalMoneyWalleto = await axios.put(`api/global-money/wallet/${id}`, field);
        return globalMoneyWalleto.data;
    },

    showSpinner({commit}) {
        commit('showSpinner');
    },
    hideSpinner({commit}) {
        commit('hideSpinner');
    }
};