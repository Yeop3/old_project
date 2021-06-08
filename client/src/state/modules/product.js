import axios from 'axios';

const makeFormData = data => {
    let formData = new FormData();

    for (let i in data) {
        if (Array.isArray(data[i])) {
            for (let k in data[i]) {
                formData.append(`${i}[${k}]`, data[i][k] || '');
            }

            continue;
        }
        formData.append(i, data[i] || '');
    }

    return formData;
};

export const state = {
    products: {},
    product: {
        driver_id: null,
        product_type_id: null,
        location_id: null,
        commission_type: 1,
        status: 1,
        address: "",
        commission_value: 0,
        photos: [],
        images: [],
        video: null,
        coordinates: null,
    },
    page: 1,
    status_list: [],
    spiner_product: false,
};

export const mutations = {
    RESET_PRODUCT(state) {
        state.product = {
            driver_id: null,
            product_type_id: null,
            location_id: null,
            commission_type: 1,
            status: 1,
            address: "",
            commission_value: 0,
            photos: [],
            images: [],
            video: null,
            coordinates: null,
        };
    },
    SET_PRODUCT(state, newValue) {
        state.product = newValue;
        state.product.driver_id = newValue.driver.number;
        state.product.product_type_id = newValue.product_type.number;
        state.product.location_id = newValue.location.number;
    },
    SET_PRODUCTS(state, newValue) {
        state.products = newValue;
        state.products.data = state.products.data.map((value) => {
            switch (value.status) {
                case 1:
                    break;
                case 2:
                    value._rowVariant = 'secondary';
                    break;
                case 3:
                    value._rowVariant = 'danger';
                    break;
                case 4:
                    value._rowVariant = 'success';
                    break;
                case 5:
                    value._rowVariant = 'warning';
                    break;
            }
            return value;
        });
    },
    SET_PAGE_PRODUCTS(state, newPage) {
        state.page = newPage;
    },
    setStatusList(state, newStatusList) {
        state.status_list = [];
        for (let item in newStatusList) {
            if (newStatusList.hasOwnProperty(item))
                state.status_list.push({
                    value: item,
                    text: newStatusList[item]
                });
        }
    },
    showSpinner(state){
        state.spiner_product = true;
    },
    hideSpinner(state){
        state.spiner_product = false;
    }
};

export const getters = {
    checkProduct(state) {
        return Object.keys(state.product).length === 0;
    },
    getProducts(state) {
        return state.products;
    },
};

export const actions = {
    create({commit}, field = {}) {
        const formData = makeFormData(field);

        return axios
            .post('/api/products', formData, {
                headers: {'Content-Type': 'multipart/form-data' }
            })
            .then((response) => {
                commit('RESET_PRODUCT');
                return response;
            });
    },
    createMany({commit}, field = {}) {
        const formData = makeFormData(field);

        return axios
            .post('/api/products/create_mass', formData, {
                headers: {'Content-Type': 'multipart/form-data' }
            })
            .then((response) => {
                return response;
            });
    },
    edit({commit}, field = {}) {
        const formData = makeFormData(field);

        formData.append('_method', 'put');

        return axios
            .post(`/api/products/${field.number}`, formData, {
                headers: {'Content-Type': 'multipart/form-data' }
            })
            .then((response) => {
                return response.data;
            });
    },
    deleteById({commit, state}, id = null) {
        return axios
            .delete(`/api/products/${id}`)
            .then((response) => {
                return response.data;
            });
    },
    getIndex({commit, dispatch, state}, {page, params}) {
        dispatch('showSpinner');
        return axios
            .get(`/api/products?page=${page || params.page}`, {params})
            .then((response) => {
                const products = response.data;
                commit('SET_PRODUCTS', products);
                return products;
            }).finally(() => dispatch('hideSpinner'));
    },
    getById({commit}, id) {
        return axios
            .get(`/api/products/${id}`)
            .then((response) => {
                const product = response.data;
                commit('SET_PRODUCT', product);
                return product;
            });
    },
    async getStatusList({commit, state}) {
        if (state.status_list.length){
            return state.status_list;
        }
        const statusList = await axios.get(`/api/products/status`);
        commit('setStatusList', statusList.data);
        return statusList.data;
    },
    async actionsSelect({commit}, field) {
        const statusList = await axios.post(`/api/products/actions-select`, field);
        return statusList.data;
    },
    showSpinner({commit}){
        commit('showSpinner');
    },
    hideSpinner({commit}){
        commit('hideSpinner');
    }
};

