<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>
    <div class="row">
      <div class="col-lg-12">
        <div class="card-box" v-if="!globalMoneyTransactionSpinner">
          <div class="table-responsive">
            <b-table
                id="drivers-table"
                stacked=""
                :items="[globalMoneyTransaction]"
                :fields="fields"
                small
            >
              <template v-slot:cell(amount)="data">
                {{ globalMoneyTransaction.amount.amount / 100 }} {{ globalMoneyTransaction.amount.currency }}
              </template>

              <template v-slot:cell(global_money_wallet)="data">
                <router-link :to="`/global-money/wallet/show/${globalMoneyTransaction.order.number}`">
                  {{ (globalMoneyTransaction.global_money_wallet.name) }}
                </router-link>
              </template>

              <template v-slot:cell(order)="data">
                <router-link :to="`/orders/show/${globalMoneyTransaction.order.number}`">
                  #{{ (globalMoneyTransaction.order.number) }}
                </router-link>
              </template>

              <template v-slot:cell(created_at)="data">
                {{ (globalMoneyTransaction.created_at) }}
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
import {
  GlobalMoneyTransactionComputed,
  GlobalMoneyTransactionMethods,
} from '@state/helpers';
import {BIcon, BIconTrash, BIconPencil} from 'bootstrap-vue'

export default {
  name: "global-money-wallet-show",
  components: {PageHeader, Layout, BIcon, BIconTrash, BIconPencil},
  computed: {
    ...GlobalMoneyTransactionComputed,
  },
  page: {
    title: 'GlobalMoney-транзакциия',
    meta: [{name: 'description', content: appConfig.description}],
  },
  async created() {
    await this.getGlobalMoneyTransaction(this.$route.params.id);
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
          key: 'transaction_id',
          label: 'ID транзакции',
          sortable: true
        },
        {
          key: 'amount',
          label: 'Сумма',
          sortable: true
        },
        {
          key: 'global_money_wallet',
          label: 'Кошелек',
          sortable: true
        },
        {
          key: 'order',
          label: 'Заказ',
          sortable: true
        },
        {
          key: 'created_at',
          label: 'Создан',
          sortable: true
        },

      ],
      title: 'GlobalMoney-транзакциия',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'GlobalMoney-транзакциии',
          to: '/global-money/transaction',
        },
        {
          text: 'GlobalMoney-транзакциия',
          active: true,
        },
      ],
    };
  },
  methods: {
    ...GlobalMoneyTransactionMethods,

  }
}
</script>

<style scoped>

</style>