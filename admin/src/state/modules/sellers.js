import axios from 'axios';

export const state = {
    seller: null,
    sellers: null
};

export const mutations = {
    SET_SELLER(state, newValue) {
        state.seller = newValue;
    },

    SET_SELLERS(state, newValue) {
        state.sellers = newValue;
    }

};

export const getters = {
    getSellers(state) {
        return state.sellers;
    },
    getOneSeller(state){
        return state.seller;
    }
};

export const actions = {
    init({ state, dispatch }) {

    },
    loadSellersList({commit}){
        return axios
            .get('/api/sellers')
            .then((response) =>{
                const sellers = response.data;
                commit('SET_SELLERS', sellers);
                return sellers;
            })
            .catch((error) =>{
            });
    },
    createSeller({ commit }, {name, domain, password, password_confirmation} = {}) {
        return axios
            .post('/api/sellers', {name, domain, password, password_confirmation})
            .then((response) => {
                const seller = response.data;
                commit('SET_SELLER', seller);
                return seller;
            });
    },
    getCardSeller({commit}, {id} = {}) {
      return axios
          .get('/api/sellers/'+id)
          .then((response) =>{
              const seller = response.data;
              commit('SET_SELLER', seller);
              return seller;
          }).catch((error) =>{
          });
    },
    updateSeller({commit}, {id, name, domain, password, password_confirmation} = {}){
        return axios
            .put('/api/sellers/'+id, {id, name, domain, password, password_confirmation})
            .then((response) => {
            });
    },
    banSeller({commit}, id){
        return axios
            .put(`/api/sellers/ban/${id}`)
            .then((response) => {
            });
    },
    unBanSeller({commit}, id){
        return axios
            .put(`/api/sellers/unban/${id}`)
            .then((response) => {
            });
    }
};