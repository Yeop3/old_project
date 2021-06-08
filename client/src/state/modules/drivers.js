import axios from 'axios';

export const state = {
    driver: {},
    drivers: {},
    page: 1,
    driver_permissions: [],
    drivers_select: [],
    spiner: false,
};

export const mutations = {
    SET_DRIVER(state, newValue) {
        state.driver = newValue;
    },
    SET_DRIVERS(state, newValue) {
        state.drivers = newValue;
    },
    SET_PAGE_DRIVER(state, newPage) {
        state.page = newPage;
    },
    DELETE_DRIVER(state, deleteId) {
        state.drivers.data = state.drivers.data.filter((value) => value.number !== deleteId);
    },
    SET_DRIVERS_SELECT(state, newValue) {
        state.drivers_select = newValue;
        state.drivers_select.unshift({value: null, text: "Выберите курьера..."});
    },
    SET_DRIVER_PERMISSIONS(state, newValue) {
        state.driver_permissions = newValue;
    },
    showSpinner(state) {
        state.spiner = true;
    },
    hideSpinner(state) {
        state.spiner = false;
    },
    clearCache(state){
        state.drivers_select = [];
    }
};

export const getters = {
    checkDriver(state) {
        return !!state.driver;
    },
    getDrivers(state) {
        return state.drivers;
    },
};

export const actions = {
    init({state, dispatch}) {
    },
    create({commit}, data) {
        return axios
            .post('/api/drivers', data)
            .then((response) => {
                const driver = response.data;
                commit('SET_DRIVER', driver);
                commit('clearCache');
                return driver;
            });
    },
    edit({commit}, {id, ...data} = {}) {
        return axios
            .put(`/api/drivers/${id}`, data)
            .then((response) => {
                const driver = response.data;
                commit('SET_DRIVER', driver);
                commit('clearCache');
                return driver;
            });
    },
    deleteById({commit, state}, id = null) {
        return axios
            .delete(`/api/drivers/${id}`)
            .then((response) => {
                commit('clearCache');
                return response.data;
            });
    },
    getIndex({commit, dispatch}, {page, params}) {
        dispatch('showSpinner');
        return axios
            .get(`/api/drivers?page=${page}`, {
                params
            })
            .then((response) => {
                const drivers = response.data;
                commit('SET_DRIVERS', drivers);
                return drivers;
            }).finally(() => dispatch('hideSpinner'));
    },
    getById({commit, dispatch}, id) {
        dispatch('showSpinner');
        return axios
            .get(`/api/drivers/${id}`)
            .then((response) => {
                const driver = response.data;
                commit('SET_DRIVER', driver);
                return driver;
            })
            .catch((err) => {
                commit('SET_DRIVER', {});
            }).finally(() => dispatch('hideSpinner'));
    },
    getSelectDriver({commit, state}) {
        if (state.drivers_select.length >1) {
            return state.drivers_select;
        }

        return axios
            .get(`/api/drivers/for_select`)
            .then((response) => {
                const driversSelect = response.data;
                commit('SET_DRIVERS_SELECT', driversSelect);
                return driversSelect;
            })
            .catch((err) => {
                commit('SET_DRIVERS_SELECT', []);
            });
    },
    loadPermissions({commit}){
        if (state.driver_permissions.length) {
            return state.driver_permissions;
        }
        return axios
          .get(`api/drivers/permissions`)
          .then((res) => {
              commit('SET_DRIVER_PERMISSIONS', res.data);
              return res.data;
          });
    },
    showSpinner({commit}) {
        commit('showSpinner');
    },
    hideSpinner({commit}) {
        commit('hideSpinner');
    }
};

