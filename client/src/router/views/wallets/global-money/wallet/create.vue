<template>
  <layout>
    <page-header :title="title" :items="items"/>
    <Form
        :data="globalMoneyWallet"
        :type-select="type_globalMoney"
        :proxy-select="proxySelect"
        type="Создать"
        :errors="errors"
        @on-submit="tryCreate"
    />
  </layout>
</template>

<script>
import Layout from '@layouts/main'
import PageHeader from '@components/page-header'
import Form from '@views/wallets/global-money/wallet/partials/form'
import {GlobalMoneyWalletComputed, GlobalMoneyWalletMethods} from '@state/helpers'
import appConfig from '@src/app.config.json'
import {mapActions, mapState} from 'vuex'

export default {
  name: 'global-money-create',
  components: {Layout, PageHeader, Form},
  computed: {
    ...GlobalMoneyWalletComputed,
    ...mapState('proxy', {
      proxySelect: (state) => state.proxySelect,
    })
  },
  page: {
    title: 'Создание GlobalMoney-аккаунта',
    meta: [{name: 'description', content: appConfig.description}],
  },
  data() {
    return {
      title: 'Создание GlobalMoney-аккаунта',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'GlobalMoney-аккаунты',
          to: 'global-money/wallet',
        },
        {
          text: 'Создание GlobalMoney-аккаунта',
          active: true,
        },
      ],
      errors: {}
    }
  },
  methods: {
    ...GlobalMoneyWalletMethods,
    ...mapActions('proxy', ['getSelectProxy']),
    async tryCreate(field) {
      //console.log(field);
      try {
        await this.create(field)
        this.$router.push(this.$route.query.redirectFrom || {name: 'global-money.wallet.index'})
      } catch (res) {
        this.errors = res.response.data.errors
        this.$bvToast.toast(res.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        })
      }
    }
  },
  async created() {
    await this.getSelectProxy()
    //console.log(this.proxySelect)
  }
}
</script>

<style scoped>

</style>