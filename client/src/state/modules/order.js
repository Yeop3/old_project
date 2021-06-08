import axios from "axios";

export const state = {
    order_select: [],
    order: null,
    orders: {
        data: [],
    },
    order_status: null,
    orderCounterFilterStatus: null,
    calc_coming: {},
    spiner_table: false,
};

export const mutations = {
    setOrderSelect(state, newValue) {
        state.order_select = newValue;
        state.order_select.unshift({
            value: null,
            text: "Не выбрано"
        });
    },
    setOrders(state, newValue) {
        state.orders = newValue;
    },
    setOrder(state, newValue) {
        state.order = newValue;
    },
    setOrdersStatus(state, newValue) {
        state.order_status = newValue;
    },
    setCountFilterStatus(state, newValue) {
        state.orderCounterFilterStatus = newValue;
    },
    setCalcComing(state, newValue) {
        state.calc_coming = newValue;
    },
    setCrypto(state, newValue) {
        state.crypto = newValue;
    },
    showSpinnerTable(state) {
        state.spiner_table = true;
    },
    hideSpinnerTable(state) {
        state.spiner_table = false;
    },
    clearCache(state) {
        // state.order_status = null;
        state.crypto = null;
        state.orderCounterFilter = null;
    }
};

export const actions = {
    async getSelect({commit}, is_all = false) {
        let params = {};
        if (is_all) {
            params.is_all = true;
        }
        const orderSelect = await axios.get(`api/order/for_select`, {params});
        commit('setOrderSelect', orderSelect.data);
        return orderSelect.data;
    },
    async getIndex({commit, dispatch}, {page, params}) {
        dispatch('showSpinnerTable');
        try {
            const orders = await axios.get(`api/order?page=${page || params.page}`, {
                params
            });
            commit('setOrders', orders.data);
            return orders;
        } finally {
            dispatch('hideSpinnerTable');
        }

    },
    async getStatus({commit, state}) {
        if (state.order_status) {
            return state.order_status;
        }

        const ordersStatus = await axios.get(`api/order/status`);
        commit('setOrdersStatus', ordersStatus.data);
        return ordersStatus.data;
    },
    async setStatusGive({commit}, item) {
        const order = await axios.post(`api/order/set_give_status/${item.number}`, item);
        commit('clearCache');
        return order.data;
    },
    async setTransferStatus({commit}, item) {
        const order = await axios.post(`api/order/set_transfer_status/${item.number}`);
        commit('clearCache');
        return order.data;
    },
    async setCancelOperatorStatus({commit}, id) {
        const order = await axios.post(`api/order/set_canceled_by_operator_status/${id}`);
        commit('clearCache');

        return order.data;
    },
    async setCountFilterStatus({commit, state}, order) {
        if (state.orderCounterFilterStatus) {
            return state.orderCounterFilterStatus;
        }

        const order_filter = await axios.get(`api/order/count_to_filter_status?order=${order}`);
        commit('setCountFilterStatus', order_filter.data);
        return order_filter.data;
    },
    async getCalc({commit}) {
        const order = await axios.get(`api/order/calc_coming`);

        commit('setCalcComing');
        return order.data;
    },
    getCrypto({commit, state}) {
       // console.log(state.crypto);
        if (state.crypto) {
            return state.crypto;
        }

        return axios
            .get(`/api/order/crypto`)
            .then((response) => {
                // console.log(response);
                commit('setCrypto', response.data);
                return response.data;
            });
    },
    async getById({dispatch, commit}, id) {
        dispatch('showSpinnerTable');
        try {
            const orders = await axios.get(`api/orders/${id}`);
            commit('setOrder', orders.data);
            return orders;
        } finally {
            dispatch('hideSpinnerTable');
        }
    },
    restorationCanceledOrder({commit}, number) {
        return axios
            .post(`api/order/restoration_canceled_order`,{number})
            .then((res) => {
                commit('clearCache');
            });
    },
    restorationPaidOrder({commit}, number) {
        return axios
            .post(`api/order/restoration_paid_order`,{number})
            .then((res) => {
                commit('clearCache');
            });
    },
    showSpinnerTable({commit}) {
        commit('showSpinnerTable');
    },
    hideSpinnerTable({commit}) {
        commit('hideSpinnerTable');
    }
};

export const getters = {};