<template>
  <layout>
    <page-header :title="title" :items="items"/>
    <Form
        :data="easyPayWallet"
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
import Form from '@views/wallets/easy-pay/wallet/partials/form'
import {EasyPayWalletComputed, EasyPayWalletMethods} from '@state/helpers'
import appConfig from '@src/app.config.json'
import {mapActions, mapState} from 'vuex'

export default {
  name: 'easy-pay-create',
  components: {Layout, PageHeader, Form},
  computed: {
    ...EasyPayWalletComputed,
    ...mapState('proxy', {
      proxySelect: (state) => state.proxySelect,
    })
  },
  page: {
    title: 'Создание EasyPay-аккаунта',
    meta: [{name: 'description', content: appConfig.description}],
  },
  data() {
    return {
      title: 'Создание EasyPay-аккаунта',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'EasyPay-аккаунты',
          to: '/easy-pay/wallet',
        },
        {
          text: 'Создание EasyPay-аккаунта',
          active: true,
        },
      ],
      errors: {}
    }
  },
  methods: {
    ...EasyPayWalletMethods,
    ...mapActions('proxy', ['getSelectProxy']),
    async tryCreate(field) {
      //console.log(field);
      try {
        await this.create(field)
        this.$router.push(this.$route.query.redirectFrom || {name: 'easy-pay.wallet.index'})
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