import axios from "axios";
import {arrayIncludes} from "bootstrap-vue/esm/utils/array";

export const state = {
    statistic: null,
    statistic_status: {},
    statistic_sellers: [],
    spiner_table: false,
};

export const mutations = {
    setStatistic(state, newValue){
      state.statistic = newValue;
    },
    setStatisticStatus(state, newValue) {
        state.statistic_status = newValue;
    },
    setStatisticSellers(state, newValue) {
        state.statistic_sellers = newValue;
    },
    showSpinnerTable(state){
        state.spiner_table = true;
    },
    hideSpinnerTable(state){
        state.spiner_table = false;
    }
};

export const actions = {
    getIndex({commit}, {params}){
        return axios.get(`api/sellers/statistics`, {params})
            .then((response) => {
                commit('setStatistic', response.data);
                console.log(response.data);
                return response.data;
            });
    },
    async getStatus({commit}) {
        const statisticStatus = await axios.get(`api/order/status`);
        commit('setStatisticStatus', statisticStatus.data);
        return statisticStatus.data;
    },
    async getSellers({commit}) {
        const statisticSellers = await axios.get(`api/order/sellers`);
        commit('setStatisticSellers', statisticSellers.data);
        return statisticSellers.data;
    },
    showSpinnerTable({commit}){
        commit('showSpinnerTable');
    },
    hideSpinnerTable({commit}){
        commit('hideSpinnerTable');
    }
};

export const getters = {};