import axios from 'axios';

export const state = {
    product_types: {},
    product_type: {},
    page: 1,
    product_types_select: [],
    spiner: false,
};

export const mutations = {
    SET_PRODUCT_TYPE(state, newValue) {
        state.product_type = newValue;
    },
    SET_PRODUCT_TYPES(state, newValue) {
        state.product_types = newValue;
    },
    SET_PAGE_PRODUCT_TYPES(state, newPage) {
        state.page = newPage;
    },
    DELETE_PRODUCT_TYPES(state, deleteId) {
        state.product_types.data = state.product_types.data.filter((value) => value.number !== deleteId);
    },
    SET_PRODUCT_TYPES_SELECT(state, newValue) {
        state.product_types_select = newValue;
        state.product_types_select.unshift({value: null, text: "Выберите тип товара..."});
    },
    PRODUCT_TYPES_SELECT_SHIFT(state) {
        state.product_types_select.shift();
    },
    showSpinner(state) {
        state.spiner = true;
    },
    hideSpinner(state) {
        state.spiner = false;
    },
    clearCache(state) {
        state.product_types_select = [];
    }
};

export const getters = {
    checkProductTypes(state) {
        return Object.keys(state.product_type).length === 0;
    },
    getProductTypes(state) {
        return state.product_types;
    },
    getCommissionType(state) {
        return state.commission_type;
    }
};

export const actions = {
    init({state, dispatch}) {
    },
    create({commit}, field = {}) {
        return axios
            .post('/api/product_types', field)
            .then((response) => {
                const product_type = response.data;
                commit('SET_PRODUCT_TYPE', product_type);
                commit('clearCache');
                return product_type;
            });
    },
    edit({commit}, {field, id} = {}) {
        return axios
            .put(`/api/product_types/${id}`, field)
            .then((response) => {
                const product_type = response.data;
                commit('clearCache');

                commit('SET_PRODUCT_TYPE', product_type);
                return product_type;
            });
    },
    deleteById({commit, state}, id = null) {
        return axios
            .delete(`/api/product_types/${id}`)
            .then((response) => {
                commit('clearCache');

                return response.data;
            });
    },
    getIndex({commit, dispatch}, {page, params}) {
        dispatch('showSpinner');
        return axios
            .get(`/api/product_types?page=${page || params.page}`, {params})
            .then((response) => {
                const product_types = response.data;
                commit('SET_PRODUCT_TYPES', product_types);
                return product_types;
            }).finally(() => dispatch('hideSpinner'));
    },
    getById({commit, dispatch}, id) {
        dispatch('showSpinner');
        return axios
            .get(`/api/product_types/${id}`)
            .then((response) => {
                const product_type = response.data.data;
                commit('SET_PRODUCT_TYPE', product_type);
                return product_type;
            }).finally(() => dispatch('hideSpinner'));
    },
    getSelectProductType({commit, state}) {
        if (state.product_types_select.length) {
            return state.product_types_select;
        }

        return axios
            .get(`/api/product_types/for_select`)
            .then((response) => {
                const product_types_select = response.data;
                commit('SET_PRODUCT_TYPES_SELECT', product_types_select);
                return product_types_select;
            });
    },
    showSpinner({commit}) {
        commit('showSpinner');
    },
    hideSpinner({commit}) {
        commit('hideSpinner');
    }
};

