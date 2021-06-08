<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>
    <div class="row">
      <div class="col-lg-12">
        <b-alert show variant="danger" dismissible v-if="globalMoneyWallet.proxy.is_working === 0">Выбранный проски не работает</b-alert>
        <div class="card-box" v-if="!global_money_wallet_spinner">
          <div class="table-responsive">
            <b-table
                id="drivers-table"
                stacked=""
                :items="[globalMoneyWallet]"
                :fields="fields"
                small
            >
              <template v-slot:cell(created_at)="data">
                {{ (data.item.created_at) }}
              </template>
              <template v-slot:cell(proxy)="data">
                <div>
                  <template v-if="data.item.proxy">
                    {{data.item.proxy.ip}}:{{data.item.proxy.port}} - {{data.item.proxy.username}}
                  </template>
                </div>
                <template v-if="data.item.proxy">
                  <b-badge v-if="data.item.proxy.is_working === 0" variant="danger">
                    Не работает
                  </b-badge>
                  <b-badge v-else variant="success">
                    Работает
                  </b-badge>
                </template>
              </template>
              <template v-slot:cell(type)="data">
                {{ type_globalMoney.find((el) => el.value === data.item.type).text }}
              </template>
              <template v-slot:cell(actions)="data">
                <router-link :to="`/global-money/wallet/edit/${data.item.number}`" class="btn btn-info btn-sm">
                  <b-icon icon="pencil"></b-icon>
                </router-link>
                <b-button variant="danger" size="sm" class="ml-2" @click="onDelete(data.item.number)">
                  <b-icon icon="trash"></b-icon>
                </b-button>
                <b-button @click="checkAccess" v-b-tooltip="'Проверить логин/пароль'" variant="warning" size="sm" class="ml-2">
                  <b-icon icon="lock"></b-icon>
                </b-button>

              </template>
            </b-table>
          </div> <!-- end table-responsive-->
        </div> <!-- end card-box -->
        <div class="d-flex justify-content-center mb-3" v-else>
          <b-spinner></b-spinner>
        </div>
      </div> <!-- end col -->
    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config.json';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {GlobalMoneyWalletComputed, GlobalMoneyWalletMethods} from '@state/helpers';
import {BIcon, BIconTrash, BIconPencil,BIconLock} from 'bootstrap-vue'
import {checkAccessGlobalMoneyWallet} from '../../../../../assets/js/checkAccessGlobalMoneyWallet.js'


export default {
  name: "global-money-wallet-show",
  components: {PageHeader, Layout, BIcon, BIconTrash, BIconPencil, BIconLock},
  computed: {
    ...GlobalMoneyWalletComputed,
  },
  page: {
    title: 'Просмотр ',
    meta: [{name: 'description', content: appConfig.description}],
  },
  async created() {
    await this.getGlobalMoneyWallet(this.$route.params.id);
  },
  data() {
    return {
      fields: [
        {
          key: 'number',
          label: 'ID',
          sortable: true
        },
        {
          key: 'name',
          label: 'Название',
          sortable: true
        },
        {
          key: 'wallet_number',
          label: 'Номер кошелька',
          sortable: true
        },
        {
          key: 'login',
          label: 'Логин',
          sortable: true
        },
        {
          key: 'type',
          label: 'Тип',
          sortable: true
        },
        {
          key: 'proxy',
          label: 'Прокси'
        },
        {
          key: 'created_at',
          label: 'Создан',
          sortable: true
        },
        {
          key: 'actions',
          label: 'Действие',
        },
      ],
      title: 'GlobalMoney-аккаунты',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'GlobalMoney-аккаунты',
          to: '/global-money/wallet',
        },
        {
          text: 'GlobalMoney-аккаунт',
          active: true,
        },
      ],
    };
  },
  methods: {
    ...GlobalMoneyWalletMethods,
    async onDelete() {
      try {
        await this.deleteGlobalMoneyWallet(this.globalMoneyWallet.number);
        this.$router.push({name: 'global-money/wallet'});
      } catch (e) {
        this.$bvToast.toast(e.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        })
      }
    },
    async checkAccess() {
      try {
        await checkAccessGlobalMoneyWallet({
          login: this.accessGlobalMoneyWallet.login,
          password: this.accessGlobalMoneyWallet.password,
          type: this.accessGlobalMoneyWallet.type
        });
        this.$bvToast.toast('Логин/пароль верные', {
          title: 'Success',
          variant: 'success',
          autoHideDelay: 5000,
        })
      } catch (res) {
        this.$bvToast.toast(res.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        })
      }
    }
  }
}
</script>

<style scoped>

</style>