export const state = {
    commission_types: [
        {value: 1, text: 'грн'},
        {value: 2, text: '%'},
    ],
};

export const getters = {
    getCommissionTypes(state){
        return state.commission_types;
    }
};