import axios from 'axios';

export const state = {
    discount: {
        name: "",
        discount_value: 0.1,
        discount_priority: 100,
        active: false,
        location_numbers: [],
        product_type_numbers: [],
        date_start: null,
        date_end: null,
        client_min_paid_orders_count: 0,
        client_min_income: 0,
        description: "",

    },
    discounts: {
        per_page: 1,
        last_page: 1,
        data:[],
    },
    page: 1,
    status_list: [
        {value: null, text: 'Все'},
        {value: 1, text: 'Продается'},
        {value: 2, text: 'Не активен'},
    ],
    spiner: false,
};

export const mutations = {
    CLEAR_DISCOUNT(state){
        state.discount = {
            name: "",
            discount_value: 0.1,
            discount_priority: 100,
            active: false,
            location_numbers: [],
            product_type_numbers: [],
            date_start: null,
            date_end: null,
            client_min_paid_orders_count: 0,
            client_min_income: 0,
            description: "",

        };
    },
    SET_DISCOUNT(state, newValue) {
        state.discount = newValue;
    },
    SET_DISCOUNTS(state, newValue) {
        state.discounts = newValue;
    },
    SET_PAGE_DISCOUNTS(state, newPage) {
        state.page = newPage;
    },
    showSpinner(state){
        state.spiner = true;
    },
    hideSpinner(state){
        state.spiner = false;
    }
};

export const getters = {
    checkDiscount(state) {
        return Object.keys(state.discount).length === 0;
    },
    getDiscounts(state) {
        return state.discounts;
    },
    getCountProducts(state) {
        let count = 0;
        state.discount.product_types.map((value) => {
            count += value.products.filter(value => (value.status === 1)).length;
        });
        return count;
    },
};

export const actions = {
    create({commit}, field = {}) {
        return axios
            .post('/api/discounts', field)
            .then((response) => {
                const product = response.data;
                commit('SET_DISCOUNT', product);
                return product;
            });
    },
    edit({commit}, field = {}) {
        return axios
            .put(`/api/discounts/${field.number}`, field)
            .then((response) => {
                const product = response.data;
                commit('SET_DISCOUNT', product);
                return product;
            });
    },
    deleteById({commit, state}, id = null) {
        return axios
            .delete(`/api/discounts/${id}`)
            .then((response) => {
                return response.data;
            });
    },
    getIndex({commit, dispatch}, {page = 1, params}) {
        dispatch('showSpinner');
        page = page || params.page;
        return axios
            .get(`/api/discounts?page=${page}`, {params})
            .then((response) => {
                const products = response.data;
                commit('SET_DISCOUNTS', products);
                return products;
            }).finally(() => dispatch('hideSpinner'));
    },
    getById({commit, dispatch}, id) {
        dispatch('showSpinner');
        return axios
            .get(`/api/discounts/${id}`)
            .then((response) => {
                const product = response.data;
                commit('SET_DISCOUNT', product);
                return product;
            }).finally(() => dispatch('hideSpinner'));
    },
    showSpinner({commit}){
        commit('showSpinner');
    },
    hideSpinner({commit}){
        commit('hideSpinner');
    }
};

