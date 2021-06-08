<template>
  <layout>
    <page-header :title="title" :items="items"/>
    <Form
            :data="globalMoneyWallet"
            :type-select="type_globalMoney"
            :proxy-select="proxySelect"
            type="Обновить"
            @on-submit="tryUpdate"
            :errors="errors"
            v-if="!global_money_wallet_spinner"
    />
    <div class="centered" v-else>
      <b-spinner></b-spinner>
    </div>
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
  name: 'edit-money-create',
  components: {Layout, PageHeader, Form},
  computed: {
    ...GlobalMoneyWalletComputed,
    ...mapState('proxy', {
      proxySelect: (state) => state.proxySelect,
    })
  },
  page: {
    title: 'Обновление GlobalMoney-аккаунта',
    meta: [{name: 'description', content: appConfig.description}],
  },
  data() {
    return {
      title: 'Обновление GlobalMoney-аккаунта',
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
          text: 'Обновление GlobalMoney-аккаунта',
          active: true,
        },
      ],
      errors: {}
    }
  },
  methods: {
    ...GlobalMoneyWalletMethods,
    ...mapActions('proxy', ['getSelectProxy']),
    async tryUpdate(field) {
      try {
        await this.update({id: this.$route.params.id, field})
        this.$router.push(this.$route.query.redirectFrom || {name: 'global-money.wallet.index'})
      } catch (res) {
        this.errors = res.response.data.errors
        this.$bvToast.toast(res.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        })
      }
    },
  },

  async created() {
    await this.getSelectProxy()
    await this.getGlobalMoneyWallet(this.$route.params.id);
  }
}
</script>

<style scoped>

</style>