import axios from 'axios'

export const state = {
    easyPayTransactions: {},
    easyPayTransaction: {
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
    setEasyPayTransactions(state, newValue) {
        state.easyPayTransactions = newValue
    },
    setEasyPayTransaction(state, newValue) {
        state.easyPayTransaction = newValue
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
    async loadEasyPayTransaction({commit, dispatch}, {page, params}) {
        dispatch('showSpinner')
        try {
            const easyPayTransactions = await axios.get(`api/easy-pay/transaction?page=${page}`, {params})
            commit('setEasyPayTransactions', easyPayTransactions.data)
            return easyPayTransactions.data
        } finally {
            dispatch('hideSpinner')
        }
    },
    async getEasyPayTransaction({commit, dispatch}, id) {
        dispatch('showSpinner')
        try {
            const easyPayTransaction = await axios.get(`api/easy-pay/transaction/${id}`)
            commit('setEasyPayTransaction', easyPayTransaction.data)
            return easyPayTransaction.data
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