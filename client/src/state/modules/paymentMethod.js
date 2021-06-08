import axios from "axios";

export const state = {
  paymentMethods: [],
};

export const mutations = {
  SET_PAYMENT_METHODS(state, methods) {
    state.paymentMethods = methods;
  },
};

export const actions = {
  loadPaymentMethods({commit, state}) {
    if (state.paymentMethods.length) {
      return state.paymentMethods;
    }
    return axios.get('/api/payment_methods')
      .then(res => {
        commit('SET_PAYMENT_METHODS', res.data);

        return res.data;
      });
  },
};