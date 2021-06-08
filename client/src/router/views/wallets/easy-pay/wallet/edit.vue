<template>
  <layout>
    <page-header :title="title" :items="items"/>
    <Form
        :data="easyPayWallet"
        :proxy-select="proxySelect"
        type="Обновить"
        @on-submit="tryUpdate"
        :errors="errors"
        v-if="!easy_pay_wallet_spinner"
    />
    <div class="centered" v-else>
      <b-spinner></b-spinner>
    </div>
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
  name: 'edit-money-create',
  components: {Layout, PageHeader, Form},
  computed: {
    ...EasyPayWalletComputed,
    ...mapState('proxy', {
      proxySelect: (state) => state.proxySelect,
    })
  },
  page: {
    title: 'Обновление EasyPay-аккаунта',
    meta: [{name: 'description', content: appConfig.description}],
  },
  data() {
    return {
      title: 'Обновление EasyPay-аккаунта',
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
          text: 'Обновление EasyPay-аккаунта',
          active: true,
        },
      ],
      errors: {}
    }
  },
  methods: {
    ...EasyPayWalletMethods,
    ...mapActions('proxy', ['getSelectProxy']),
    async tryUpdate(field) {
      try {
        await this.update({id: this.$route.params.id, field})
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
    await this.getEasyPayWallet(this.$route.params.id);
  }
}
</script>

<style scoped>

</style>