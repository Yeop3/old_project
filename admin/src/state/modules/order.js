import axios from "axios";

export const state = {
    order_select: [],
    order: {},
    orders: {
        data: [],
    },
    order_status: {},
    order_sellers: [],
    orderCounterFilterStatus: {order_status: {}},
    calc_coming:{},
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
    setSellers(state, newValue) {
        state.order_sellers = newValue;
    },
    setCalcComing(state, newValue){
        state.calc_coming = newValue;
    },
    setCountFilterStatus(state, newValue) {
        state.orderCounterFilterStatus = newValue;
    },
    showSpinnerTable(state){
        state.spiner_table = true;
    },
    hideSpinnerTable(state){
        state.spiner_table = false;
    }
};

export const actions = {
    async getSelect({commit}) {
        const orderSelect = await axios.get(`api/order/for_select`);
        commit('setOrderSelect', orderSelect.data);
        return orderSelect.data;
    },
    async getIndex({commit,dispatch}, {page, params}) {
        dispatch('showSpinnerTable');
        try {
            const orders = await axios.get(`api/order?page=${page}`, {
                params
            });
            // console.log(orders.data.data);
            commit('setOrders', orders.data);
            return orders;
        }finally {
            dispatch('hideSpinnerTable');
        }

    },
    async getStatus({commit}) {
        const ordersStatus = await axios.get(`api/order/status`);
        commit('setOrdersStatus', ordersStatus.data);
        return ordersStatus.data;
    },
    async getSellers({commit}) {
        const orderSellers = await axios.get(`api/order/sellers`);
        commit('setSellers', orderSellers.data);
        return orderSellers.data;
    },
    async getCalc({commit}){
        const order = await axios.get(`api/order/calc_coming`);
        commit('setCalcComing');
        return order.data;
    },
    async setCountFilterStatus({commit}, order){
        const order_filter = await axios.get(`api/order/count_to_filter_status?order=${order}`);
        commit('setCountFilterStatus', order_filter.data);
        console.log(order_filter);

        return order_filter.data;
    },
    async getById({dispatch, commit}, id){
        dispatch('showSpinnerTable');
        try {
            const orders = await axios.get(`api/orders/${id}`);
            commit('setOrder', orders.data);
            return orders;
        }finally {
            dispatch('hideSpinnerTable');
        }
    },
    showSpinnerTable({commit}){
        commit('showSpinnerTable');
    },
    hideSpinnerTable({commit}){
        commit('hideSpinnerTable');
    }
};

export const getters = {};