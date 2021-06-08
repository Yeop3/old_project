import axios from 'axios';

export const state = {
	stokers: [],
	stoker: {},
	spiner_stoker: false,
};

export const mutations = {
	SET_STOKERS(state, newValue){
		state.stokers = newValue;
	},
	SET_STOKER(state, newValue){
		state.stoker = newValue;
	},
	showSpinner(state){
		state.spiner_stoker = true;
	},
	hideSpinner(state){
		state.spiner_stoker = false;
	}
};

export const getters = {

};

export const actions = {
	loadStokers({commit,dispatch}){
		dispatch('showSpinner');
		return axios
			.get(`api/stokers`)
			.then((res) => {
				commit('SET_STOKERS', res.data);
				return res.data;
			}).finally(()=>dispatch('hideSpinner'));
	},
	createStoker({commit}, field = {}) {
		return axios
			.post('/api/stokers', field)
			.then((response) => {
				return response;
			});
	},
	loadStoker({commit}, number){
		return axios
			.get(`api/stokers/${number}`)
			.then((res)=> {
				commit('SET_STOKER', res.data);
				return res.data;
			});
	},
	updateStoker({commit}, field = {}){
		return axios
			.put(`api/stokers/${field.number}`, field)
			.then((res)=> {
				commit('SET_STOKER', res.data);
				return res.data;
			});
	},
	deleteStoker({commit}, number) {
		return axios
			.delete(`/api/stokers/${number}`);
	},

	showSpinner({commit}){
		commit('showSpinner');
	},
	hideSpinner({commit}){
		commit('hideSpinner');
	}
};