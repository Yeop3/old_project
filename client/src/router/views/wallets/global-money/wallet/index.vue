<template>
  <layout>
    <page-header :title="title" :items="items"/>
    <Table
        :data="globalMoneyWallets"
        :loading="global_money_wallet_spinner"
        :type="type_globalMoney"
        @access="checkAccess"
        @filter="filter"
        @on-delete="onDelete"
    ></Table>
  </layout>
</template>

<script>
import appConfig from '@src/app.config.json'
import Layout from '@layouts/main'
import PageHeader from '@components/page-header'
import Table from '@views/wallets/global-money/wallet/partials/table'
import { GlobalMoneyWalletMethods, GlobalMoneyWalletComputed } from '@state/helpers'
import {checkAccessGlobalMoneyWallet} from '../../../../../assets/js/checkAccessGlobalMoneyWallet.js'

export default {
  name: 'global-money-wallet-index',
  page: {
    title: 'GlobalMoney-аккаунты',
    meta: [{ name: 'description', content: appConfig.description }],
  },
  components: { Layout, PageHeader, Table },
  computed: {
    ...GlobalMoneyWalletComputed,
  },
  data () {
    return {
      loading: true,
      data: null,
      title: 'GlobalMoney-аккаунты',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'GlobalMoney-аккаунты',
          active: true,
        },
      ],
    }
  },
  async mounted () {
    await this.loadGlobalMoneyWallet({ page: 1, params: {} })
  },
  methods: {
    ...GlobalMoneyWalletMethods,
    async onDelete (id) {
      await this.deleteGlobalMoneyWallet(id)
      await this.loadGlobalMoneyWallet({ page: 1, params: {} })
    },
    async filter(filterField){
      let params = {};
      for (let value of filterField) {
        if (value.value)
          params[value.name] = value.value;
      }

      await this.loadGlobalMoneyWallet({ page: 1, params })
    },
    async checkAccess(accessData) {
      try {
        await checkAccessGlobalMoneyWallet({
          login: accessData.login,
          password: accessData.password,
          type: accessData.type
        });
        this.$bvToast.toast('Логин/пароль верные', {
          title: 'Success',
          variant: 'success',
          autoHideDelay: 5000,
        });
        await this.loadGlobalMoneyWallet({ page: 1, params: {} });
      } catch (res) {
        this.$bvToast.toast(res.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
        await this.loadGlobalMoneyWallet({ page: 1, params: {} });
      }
    }
  }
}
</script>

<style scoped>

</style>