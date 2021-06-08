import axios from 'axios';
import moment from "moment";

export const state = {
    shifts: [],
    shift: null,
    page: 1,
    shifts_spiner: false,
};

export const mutations = {
    SET_SHIFTS(state, newValue) {
        state.shifts = newValue;
    },
    ADD_SHIFTS(state, newValue) {
        // state.shifts[state.shifts.length - 1].ended_at = new Date().toISOString();
        // state.shifts.push(newValue);
    },
    setShift(state, newValue) {
        state.shift = newValue;
    },
    showSpinner(state) {
        state.shifts_spiner = true;
    },
    hideSpinner(state) {
        state.shifts_spiner = false;
    },
    clearShift(state) {
        state.shift = null;
    }
};

export const getters = {
    checkShift(state) {
        return Object.keys(state.product).length === 0;
    },
    getShifts(state) {
        return state.products;
    },

};

export const actions = {
    async getIndex({commit, dispatch}, {page, params}) {
        dispatch('showSpinner');
        const shifts = await axios.get('/api/shifts', {params});
        commit('SET_SHIFTS', shifts.data);
        dispatch('hideSpinner');
        return shifts.data;
    },
    async start_new({commit}, operatorNumber) {
        const shift = await axios.post(`/api/shifts/start_new/${operatorNumber}`);
        commit('ADD_SHIFTS', shift.data);
        commit('clearShift');
        return shift.data;
    },
    async getCurrent({commit, state}) {
        if (state.shift) {
            return state.shift;
        }
        const shift = await axios.get(`/api/shifts/current`);
        commit('setShift', shift.data);
        return shift.data;
    },
    loadShift({commit}, number) {
        return axios
            .get(`/api/shifts/${number}`)
            .then((response) => {
                commit('setShift', response.data);
                return response.data;
            });
    },
    async exportExcelByID({commit}, id) {
        try {
            const response = await axios.get(`api/shifts/export/${id}`, {responseType: 'blob'});
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `shift_export_${moment()
                .locale('ru').format('MM-DD-YYYY',)}.xlsx`);
            document.body.appendChild(link);
            link.click();
            return response.data;
        } catch (e) {
            console.error(e);
        }
    },
    showSpinner({commit}) {
        commit('showSpinner');
    },
    hideSpinner({commit}) {
        commit('hideSpinner');
    }
};
