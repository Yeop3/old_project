import axios from 'axios';
import queryString from 'query-string';

export const state = {
  botLogic: null,
  botLogics: [],
  page: 1,
  pagesCount: null,
  total: null,
  perPage: null,
  spiner: false,
};

export const mutations = {
  SET_BOT_LOGIC(state, newValue) {
    state.botLogic = newValue;
  },
  SET_BOT_LOGICS(state, newValue) {
    state.botLogics = newValue;
  },
  // SET_PAGINATION_DATA(state, {pagesCount, total, perPage}) {
  //   state.pagesCount = pagesCount;
  //   state.total = total;
  //   state.perPage = perPage;
  // },
  // SET_PAGE(state, newPage) {
  //   state.page = newPage;
  // },
  showSpinner(state){
    state.spiner = true;
  },
  hideSpinner(state){
    state.spiner = false;
  }
};

export const getters = {
  botLogicsForSelect(state) {
    // console.log(state);
    return state.botLogics.map(botLogic => {
      return {
        value: {
          logic_number: botLogic.number,
          logic_type: botLogic.type,
        },
        text: botLogic.name,
      };
    });
  },
};

export const actions = {
  init({state, dispatch}) {
  },
  // create({commit, actions}, {name, token, active, allow_create_clients, logic_number, logic_type} = {}) {
  //   return axios
  //     .post('/api/bot_logics', {
  //       name, token, active, allow_create_clients, logic_number, logic_type,
  //       type: 1,
  //       messenger: 1,
  //     })
  //     .then((response) => {
  //     });
  // },
  // edit({commit}, {name, id} = {}) {
  //   return axios
  //     .put(`/api/drivers/${id}`, {name})
  //     .then((response) => {
  //       const driver = response.data;
  //       commit('SET_DRIVER', driver);
  //       return driver;
  //     });
  // },
  loadBotLogics({commit, state, dispatch}) {
    const query = queryString.stringify({
      page: state.page
    });
      dispatch('showSpinner');
    return axios
      .get(`/api/bot_logics?${query}`)
      .then((response) => {
        commit('SET_BOT_LOGICS', response.data);
        // commit('SET_PAGINATION_DATA', {
        //   pagesCount: response.data.last_page,
        //   total: response.data.total,
        //   perPage: response.data.per_page,
        // });
        return response.data;
      }).finally(() => dispatch('hideSpinner'));
  },
  loadBotLogic({commit, dispatch}, {type, number}) {
    dispatch('showSpinner');
    return axios
      .get(`/api/bot_logics/${type}/${number}`)
      .then((response) => {
        commit('SET_BOT_LOGIC', response.data);
        return response.data;
      })
        .catch((err) => {
        }).finally(() => dispatch('hideSpinner'));
  },
  cloneBotLogic({commit}, {type, number}){
    return axios
        .post(`/api/bot_logics/${type}/${number}/clone`)
        .then((response) => {
        })
        .catch((err) => {
        });
  },
  deleteBotLogic({commit}, number){
    return axios
        .delete(`/api/bot_logics/client/${number}`)
        .then((response) => {
        })
        .catch((err) => {
        });
  },
  updateBotLogic({commit}, {name, number, description, commands, antispams, distributions, events, operator_notifications, reminders} = {}){
      return axios
          .put(`/api/bot_logics/client/${number}`,{name, description, commands, antispams, distributions, events, operator_notifications, reminders})
          .then((response) => {
          })
          .catch((err) => {
          });
  },
  showSpinner({commit}){
      commit('showSpinner');
  },
  hideSpinner({commit}){
      commit('hideSpinner');
  }
};

