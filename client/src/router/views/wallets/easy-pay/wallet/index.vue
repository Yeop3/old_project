<template>
  <layout>
    <page-header :title="title" :items="items"/>
    <Table
        :data="easyPayWallets"
        :loading="easy_pay_wallet_spinner"
        @filter="filter"
        @on-delete="onDelete"
        @restore-balance="restoreBalance"
    ></Table>
  </layout>
</template>

<script>
import appConfig from '@src/app.config.json'
import Layout from '@layouts/main'
import PageHeader from '@components/page-header'
import Table from '@views/wallets/easy-pay/wallet/partials/table'
import { EasyPayWalletMethods, EasyPayWalletComputed } from '@state/helpers'

export default {
  name: 'easy-pay-wallet-index',
  page: {
    title: 'EasyPay-аккаунты',
    meta: [{ name: 'description', content: appConfig.description }],
  },
  components: { Layout, PageHeader, Table },
  computed: {
    ...EasyPayWalletComputed,
  },
  data () {
    return {
      loading: true,
      data: null,
      title: 'EasyPay-аккаунты',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'EasyPay-аккаунты',
          active: true,
        },
      ],
    }
  },
  async mounted () {
    await this.loadEasyPayWallet({ page: 1, params: {} })
  },
  methods: {
    ...EasyPayWalletMethods,
    async onDelete (id) {
      await this.deleteEasyPayWallet(id)
      await this.loadEasyPayWallet({ page: 1, params: {} })
    },
    async filter(filterField){
      let params = {};
      for (let value of filterField) {
        if (value.value)
          params[value.name] = value.value;
      }

      await this.loadEasyPayWallet({ page: 1, params })
    },
    async restoreBalance(id){
      await this.restoreBalanceWallet(id)
      await this.loadEasyPayWallet({ page: 1, params: {} })
    }
  }
}
</script>

<style scoped>

</style>