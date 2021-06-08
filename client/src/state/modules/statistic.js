import axios from 'axios';

export const state = {
    statistic: {},
    statusStatistic: null,
    spiner: false,
    chart: []
};

export const mutations = {
    SET_STATISTIC(state, newValue) {
        state.statistic = newValue;
    },
    SET_STATUS_STATISTIC(state, newValue) {
        state.statusStatistic = newValue;
    },
    SET_CHART_STATISTIC(state, newValue) {
        state.chart = newValue;
    },
    showSpinner(state){
        state.spiner = true;
    },
    hideSpinner(state){
        state.spiner = false;
    }
};

export const getters = {

};

export const actions = {
    init({state, dispatch}) {
    },
    loadStatistic({commit,dispatch}){
        dispatch('showSpinner');
        return axios
            .get(`/api/statistic`)
            .then((response) => {
                commit('SET_STATISTIC', response.data);
                return response.data;
            }).finally(() => dispatch('hideSpinner'));
    },
    loadStatusStatistic({commit, dispatch}, status){
        dispatch('showSpinner');
        return axios
            .get(`/api/statistic/${status}`)
            .then((response) => {
                commit('SET_STATUS_STATISTIC', response.data);
                return response.data;
            }).finally(() => dispatch('hideSpinner'));
    },
    chartStatistic({commit,dispatch}, {params}){
        dispatch('showSpinner');
        return axios
            .get('api/statistic/chart', {params})
            .then((res) => {
                commit('SET_CHART_STATISTIC', res.data);
            }).finally(() => dispatch('hideSpinner'));
    },
    showSpinner({commit}){
        commit('showSpinner');
    },
    hideSpinner({commit}){
        commit('hideSpinner');
    }
};