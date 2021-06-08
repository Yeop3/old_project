import axios from "axios";

axios.defaults.baseURL = process.env.VUE_APP_API_URL;

import Vue from 'vue';
import Vuex from 'vuex';
import dispatchActionForAllModules from '@utils/dispatch-action-for-all-modules';
import modules from './modules';

Vue.use(Vuex);

const store = new Vuex.Store({
  modules,
  // Enable strict mode in development to get a warning
  // when mutating state outside of a mutation.
  // https://vuex.vuejs.org/guide/strict.html
  strict: process.env.NODE_ENV !== 'production',
});

export default store;

// Automatically run the `init` action for every module,
// if one exists.
dispatchActionForAllModules('init');
