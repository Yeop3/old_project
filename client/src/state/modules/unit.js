export const state = {
    unit_types: [
        {value: 1, text: 'шт'},
        {value: 2, text: 'г'},
    ],
};

export const getters = {
    getUnitTypes(state){
        return state.unit_types;
    }
};