<template>
  <layout>
    <page-header :title="title" :items="items"/>
    <Table
        :data="easyPayTransactions"
        :loading="easyPayTransactionSpinner"
        :easy-pay-wallet-select="easyPayWalletSelect"
        :order-select="order_select"
        @filter="filter"
    ></Table>
  </layout>
</template>

<script>
import appConfig from '@src/app.config.json'
import Layout from '@layouts/main'
import PageHeader from '@components/page-header'
import Table from '@views/wallets/easy-pay/transaction/partials/table'
import {
  EasyPayTransactionComputed,
  EasyPayTransactionMethods,
  EasyPayWalletComputed,
  EasyPayWalletMethods
} from '@state/helpers'
import {mapActions, mapState} from "vuex";

export default {
  name: 'easy-pay-transaction-index',
  page: {
    title: 'EasyPay-транзакции',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader, Table},
  computed: {
    ...EasyPayTransactionComputed,
    ...EasyPayWalletComputed,
    ...mapState('order', {
      order_select: (state) => state.order_select
    }),
  },
  data() {
    return {
      loading: true,
      data: null,
      title: 'EasyPay-транзакции',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'EasyPay-транзакции',
          active: true,
        },
      ],
    }
  },
  async mounted() {
    try {
      await this.getEasyPayWalletSelect();
      await this.getSelect(true);
    }finally {
      await this.loadEasyPayTransaction({page: 1, params: {}})
    }
  },
  methods: {
    ...EasyPayTransactionMethods,
    ...EasyPayWalletMethods,
    ...mapActions('order', ['getSelect']),
    async filter(filterField) {
      let params = {};
      for (let value of filterField) {
        if (value.value)
          params[value.name] = value.value;
      }

      await this.loadEasyPayTransaction({page: 1, params})
    }
  }
}
</script>

<style scoped>

</style>