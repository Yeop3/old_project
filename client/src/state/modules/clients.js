import axios from 'axios';
import moment from "moment";

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
  client: null,
  clients: [],
  clients_for_select: [],
  spiner: false,
  clientActuals: [],
  client_spam_reserve: [],
};

export const mutations = {
  SET_CLIENT(state, newValue) {
    newValue.created_at = moment(newValue.created_at).locale('ru').format('LLL');
    newValue.visited_at = moment(newValue.visited_at).locale('ru').fromNow();
    state.client = newValue;
  },
  SET_CLIENTS(state, newValue) {
    newValue.data.forEach((item) => {
      item.created_at = moment(item.created_at).locale('ru').format('LLL');
      item.visited_at = moment(item.visited_at).locale('ru').fromNow();
    });
    state.clients = newValue;
  },
  SET_CLIENTS_FOR_SELECT(state, clients) {
    state.clients_for_select = clients;
  },
  // SET_PAGINATION_DATA(state, {pagesCount, total, perPage}) {
  //   state.pagesCount = pagesCount;
  //   state.total = total;
  //   state.perPage = perPage;
  // },
  // SET_PAGE(state, newPage) {
  //   state.page = newPage;
  // }
  showSpinner(state) {
    state.spiner = true;
  },
  hideSpinner(state) {
    state.spiner = false;
  },
  SET_CLIENTS_ACTUAL(state, newValue) {
    state.clientActuals = newValue;
  },
  SET_CLIENTS_SPAM_reserve(state, newValue) {
    state.client_spam_reserve = newValue;
  },

};

export const getters = {};

export const actions = {
  init({state, dispatch}) {
  },
  loadClients({commit, dispatch}, {page, params = {}}) {
    dispatch('showSpinner');
    return axios
      .get(`/api/clients?page=${page || params.page}`, {params})
      .then((response) => {
        commit('SET_CLIENTS', response.data);
        return response.data;
      }).catch((err) => {
      }).finally(() => dispatch('hideSpinner'));
  },
  loadClientsForSelect({commit}) {
      return axios
        .get(`/api/clients/for_select`)
        .then((response) => {
            commit('SET_CLIENTS_FOR_SELECT', response.data);
            return response.data;
        });
  },
  loadClient({commit, dispatch}, number) {
    dispatch('showSpinner');
    return axios
      .get(`/api/clients/${number}`)
      .then((response) => {
        commit('SET_CLIENT', response.data);
        return response.data;
      }).catch((err) => {
      }).finally(() => dispatch('hideSpinner'));
  },
  updateClient({commit}, {number, note, discount_value, discount_priority}) {
    console.log(number, note, discount_value, discount_priority);
    return axios
      .put(`/api/clients/${number}`, {note, discount_value, discount_priority})
      .then((response) => {
      }).catch((err) => {
      });
  },
  deleteClient({commit}, number) {
    return axios
      .delete(`/api/clients/${number}`)
      .then((response) => {
      });
  },
  async banClient({commit}, {number, days}) {
    const clientBan = await axios.post(`/api/clients/${number}/ban`, {
      days: days
    });
  },
  async unBanClient({commit}, number) {
    const clientBan = await axios.post(`/api/clients/${number}/un_ban`);
  },
  async blackListClient({commit}, number) {
    const clientBan = await axios.post(`/api/clients/${number}/black_list`);
  },
  async unBlackListClient({commit}, number) {
    const clientBan = await axios.post(`/api/clients/${number}/un_black_list`);
  },
  async sendMessageToClient({commit}, field = {}) {
    const formData = makeFormData(field);
    console.log(field);
    console.log(formData);
    return axios
      .post(`api/clients/send_message`, formData, {headers: {'Content-Type': 'multipart/form-data'}})
      .then((res) => {
      });
  },
  showSpinner({commit, state}) {
    commit('showSpinner');
  },
  hideSpinner({commit, state}) {
    commit('hideSpinner');
  },
  async exportTelegramCsv() {
    try {
      const response = await axios.get('api/clients/export_csv_telegram', {responseType: 'blob'});
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `clients_telegram_${moment()
        .locale('ru').format('MM-DD-YYYY',)}.csv`);
      document.body.appendChild(link);
      link.click();
      return response.data;
    } catch (e) {
      console.error(e);
    }
  },
  async unbanAll() {
    const unBanAll = axios.post('api/clients/un_ban_all');
    return unBanAll.data;
  },
  async loadHandDispatchActualTelegram({dispatch, commit}, params = {}) {
    dispatch('showSpinner');
    try {
      const response = await axios.get(`/api/clients/hand-dispatch-actual-telegram`, {params});
      commit('SET_CLIENTS_ACTUAL', response.data.data);
      return response.data.data;
    } finally {
      dispatch('hideSpinner');
    }
  },
  async downloadUsernameActual() {
    try {
      const response = await axios.get('api/clients/export_csv_telegram_actual_username', {responseType: 'blob'});
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', `clients_telegram_actual_${moment()
        .locale('ru').format('MM-DD-YYYY',)}.csv`);
      document.body.appendChild(link);
      link.click();
      return response.data;
    } catch (e) {
      console.error(e);
    }
  },
  async loadSpamReserve({dispatch, commit}, params = {}) {
    dispatch('showSpinner');
    try {
      const response = await axios.get(`/api/clients/spam-reserve`, {params});
      commit('SET_CLIENTS_SPAM_reserve', response.data.data);
      return response.data.data;
    } finally {
      dispatch('hideSpinner');
    }
  },
  async multiBan({commit}, params = {}) {
    return axios.post('/api/clients/multi-ban/', params);
  },
  async multiBlackList({commit}, params = {}) {
    return axios.post('/api/clients/multi-black-list/', params);
  },
  async multiDelete({commit}, params = {}) {
    return axios.post('/api/clients/multi-delete/', params);
  },
};