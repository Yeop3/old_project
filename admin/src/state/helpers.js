import { mapState, mapGetters, mapActions } from 'vuex'

export const authComputed = {
  ...mapState('auth', {
    currentUser: (state) => state.currentUser,
  }),
  ...mapGetters('auth', ['loggedIn']),
}

export const sellerComputed ={
  ...mapState('sellers',{
    seller:(state) => state.seller,
    sellers:(state) => state.sellers
  }),
  ...mapGetters('sellers',['getSellers', 'getOneSeller'])
}

export const authMethods = mapActions('auth', ['logIn', 'logOut', 'register', 'resetPassword'],)

export const sellerMethods = mapActions('sellers', ['createSeller', 'updateSeller', 'loadSellersList', 'getCardSeller','banSeller','unBanSeller'])

export const orderMethods = mapActions('order', [
  'getIndex',
  'getStatus',
  'setCountFilterStatus',
  'getSellers',
  'getById',
]);

export const orderComputed = {
  ...mapState('order', {
    order_select: (state) => state.order_select,
    orders: (state) => state.orders,
    order_status: (state) => state.order_status,
    order_sellers: (state) => state.order_sellers,
    orderCounterFilterStatus: (state) => state.orderCounterFilterStatus,
    spiner_table: (state) => state.spiner_table,
    order: (state) => state.order
  }),
  ...mapGetters('order', []),
};

export const statisticMethods = mapActions('statistic',['getStatus', 'getSellers', 'getIndex']);

export const statisticComputed = {
  ...mapState('statistic', {
    statistic_status: (state) => state.statistic_status,
    statistic_sellers: (state) => state.statistic_sellers,
    statistic: (state) => state.statistic,
    spiner_table: (state) => state.spiner_table,
  })
}