import axios from 'axios'

export const state = {
    globalMoneyTransactions: {},
    globalMoneyTransaction: {
        name: null,
        type: 'text',
        login: null,
        password: null,
        proxy_id: null,

    },
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
}

export const mutations = {
    setGlobalMoneyTransactions(state, newValue) {
        state.globalMoneyTransactions = newValue
    },
    setGlobalMoneyTransaction(state, newValue) {
        state.globalMoneyTransaction = newValue
    },
    showSpinner(state) {
        state.spiner = true
    },
    hideSpinner(state) {
        state.spiner = false
    },
    setSelect(state, newValue) {
        state.select = newValue
        state.select.unshift({text: 'Все', value: null})
    }
}

export const getters = {}

export const actions = {
    async loadGlobalMoneyTransaction({commit, dispatch}, {page, params}) {
        dispatch('showSpinner')
        try {
            const globalMoneyTransactions = await axios.get(`api/global-money/transaction?page=${page}`, {params})
            commit('setGlobalMoneyTransactions', globalMoneyTransactions.data)
            return globalMoneyTransactions.data
        } finally {
            dispatch('hideSpinner')
        }
    },
    async getGlobalMoneyTransaction({commit, dispatch}, id) {
        dispatch('showSpinner')
        try {
            const globalMoneyTransaction = await axios.get(`api/global-money/transaction/${id}`)
            commit('setGlobalMoneyTransaction', globalMoneyTransaction.data)
            return globalMoneyTransaction.data
        } finally {
            dispatch('hideSpinner')
        }
    },

    showSpinner({commit}) {
        commit('showSpinner')
    },
    hideSpinner({commit}) {
        commit('hideSpinner')
    }
}