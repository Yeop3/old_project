import axios from 'axios';
import queryString from 'query-string';

export const state = {
    sections: {},
    spiner: false,
};

export const mutations = {
    SET_SECTIONS(state, sections){
        state.sections = sections;
    },
    showSpinner(state){
        state.spiner = true;
    },
    hideSpinner(state){
        state.spiner = false;
    }
};

export const getters = {
    settings(state) {
        return state.sections;
    },
};
export const actions = {
    loadSettings({commit, state, dispatch}, sections = []) {
       // console.log(sections);
        dispatch('showSpinner');
        const query = queryString.stringify({'sections': sections}, {arrayFormat: 'index'});
        return axios.get('/api/settings?' + query)
            .then(res => {
                commit('SET_SECTIONS', {...state.sections, ...res.data});
            }).finally(() => dispatch('hideSpinner'));
    },
    updateSettings({commit}, settings) {
        return axios.put('/api/settings', settings);
    },
    showSpinner({commit}){
        commit('showSpinner');
    },
    hideSpinner({commit}){
        commit('hideSpinner');
    }
};
