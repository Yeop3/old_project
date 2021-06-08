<template>
  <layout>
    <page-header :title="title" :items="items"/>
    <Table
        :data="globalMoneyTransactions"
        :loading="globalMoneyTransactionSpinner"
        :global-money-wallet-select="globalMoneyWalletSelect"
        :order-select="order_select"
        @filter="filter"
    ></Table>
  </layout>
</template>

<script>
import appConfig from '@src/app.config.json'
import Layout from '@layouts/main'
import PageHeader from '@components/page-header'
import Table from '@views/wallets/global-money/transaction/partials/table'
import {
  GlobalMoneyTransactionComputed,
  GlobalMoneyTransactionMethods,
  GlobalMoneyWalletComputed,
  GlobalMoneyWalletMethods
} from '@state/helpers'
import {mapActions, mapState} from "vuex";

export default {
  name: 'global-money-wallet-index',
  page: {
    title: 'GlobalMoney-транзакции',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader, Table},
  computed: {
    ...GlobalMoneyTransactionComputed,
    ...GlobalMoneyWalletComputed,
    ...mapState('order', {
      order_select: (state) => state.order_select
    }),
  },
  data() {
    return {
      loading: true,
      data: null,
      title: 'GlobalMoney-транзакции',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'GlobalMoney-транзакции',
          active: true,
        },
      ],
    }
  },
  async mounted() {
    await this.getGlobalMoneyWalletSelect();
    await this.getSelect(true);
    await this.loadGlobalMoneyTransaction({page: 1, params: {}})
  },
  methods: {
    ...GlobalMoneyTransactionMethods,
    ...GlobalMoneyWalletMethods,
    ...mapActions('order', ['getSelect']),
    async filter(filterField) {
      let params = {};
      for (let value of filterField) {
        if (value.value)
          params[value.name] = value.value;
      }

      await this.loadGlobalMoneyTransaction({page: 1, params})
    }
  }
}
</script>

<style scoped>

</style>