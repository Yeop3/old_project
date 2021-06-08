<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>
    <div class="row">

      <div class="col-lg-12">
        <div class="card-box" v-if="!easy_pay_wallet_spinner">
          <b-alert
            :show="easyPayWallet.is_limit"
            variant="danger"
          >
            Лимит превышен
          </b-alert>
          <b-alert show variant="danger" dismissible v-if="easyPayWallet.proxy.is_working === 0">Выбранный проски не работает</b-alert>
          <h4 class="header-title">
            <b-button variant="info" @click="check">Проверить
            </b-button>
          </h4>
          <div class="table-responsive">
            <b-table
                id="drivers-table"
                stacked=""
                :items="[easyPayWallet]"
                :fields="fields"
                small
            >
              <template v-slot:cell(created_at)="data">
                {{ (data.item.created_at) }}
              </template>
              <template v-slot:cell(actions)="data">
                <router-link :to="`/easy-pay/wallet/edit/${data.item.number}`" class="btn btn-info btn-sm">
                  <b-icon icon="pencil"></b-icon>
                </router-link>
                <b-button variant="danger" size="sm" class="ml-2" @click="onDelete(data.item.number)">
                  <b-icon icon="trash"></b-icon>
                </b-button>

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

              <template v-slot:cell(wrong_creadentials)="data">
                <div v-if="data.item.wrong_creadentials">
                  Не верные логин или пароль
                </div>
              </template>

              <template v-slot:cell(limit)="data">
                Лимит: {{data.item.limit}}
                <div v-if="data.item.is_limit">
                  Лимит превышен
                </div>
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
import {EasyPayWalletComputed, EasyPayWalletMethods} from '@state/helpers';
import {BIcon, BIconTrash, BIconPencil} from 'bootstrap-vue'

export default {
  name: "easy-pay-wallet-show",
  components: {PageHeader, Layout, BIcon, BIconTrash, BIconPencil},
  computed: {
    ...EasyPayWalletComputed,
  },
  page: {
    title: 'Просмотр ',
    meta: [{name: 'description', content: appConfig.description}],
  },
  async created() {
    await this.getEasyPayWallet(this.$route.params.id);
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
          key: 'phone',
          label: 'Логин',
          sortable: true
        },
        {
          key: 'balance',
          label: 'Баланс',
          sortable: true
        },
        {
          key: 'limit',
          label: 'Лимит',
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
        {
          key: 'wrong_creadentials',
          label: 'Статус',
        },
      ],
      title: 'EasyPay-аккаунты',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'EasyPay-аккаунты',
          to: '/easy-pay/wallet/',
        },
        {
          text: 'EasyPay-аккаунт',
          active: true,
        },
      ],
    };
  },
  methods: {
    ...EasyPayWalletMethods,
    async onDelete() {
      try {
        await this.deleteEasyPayWallet(this.easyPayWallet.number);
        this.$router.push({name: 'global-money/wallet'});
      } catch (e) {
        this.$bvToast.toast(e.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        })
      }
    },
    async check() {
      try {
        await this.checkAccount(this.easyPayWallet.number);
        this.$bvToast.toast('Успешно', {
          variant: 'success',
          autoHideDelay: 5000,
        })
      } catch (e) {
        this.$bvToast.toast(e.response.data.message, {
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