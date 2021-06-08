<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>
    <div class="row">
      <div class="col-lg-12">
        <div class="card-box" v-if="!easyPayTransactionSpinner">
          <div class="table-responsive">
            <b-table
                id="drivers-table"
                stacked=""
                :items="[easyPayTransaction]"
                :fields="fields"
                small
            >
              <template v-slot:cell(amount)="data">
                {{ easyPayTransaction.amount.amount / 100 }} {{ easyPayTransaction.amount.currency }}
              </template>

              <template v-slot:cell(global_money_wallet)="data">
                <router-link :to="`/global-money/wallet/show/${easyPayTransaction.order.number}`">
                  {{ (easyPayTransaction.easy_pay_wallet.name) }}
                </router-link>
              </template>

              <template v-slot:cell(order)="data">
                <router-link :to="`/orders/show/${easyPayTransaction.order.number}`">
                  #{{ (easyPayTransaction.order.number) }}
                </router-link>
              </template>

              <template v-slot:cell(created_at)="data">
                {{ (easyPayTransaction.created_at) }}
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
  EasyPayTransactionComputed,
  EasyPayTransactionMethods,
} from '@state/helpers';
import {BIcon, BIconTrash, BIconPencil} from 'bootstrap-vue'

export default {
  name: "easy-pay-transaction-show",
  components: {PageHeader, Layout, BIcon, BIconTrash, BIconPencil},
  computed: {
    ...EasyPayTransactionComputed,
  },
  page: {
    title: 'EasyPay-транзакциия',
    meta: [{name: 'description', content: appConfig.description}],
  },
  async created() {
    await this.getEasyPayTransaction(this.$route.params.id);
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
      title: 'EasyPay-транзакциия',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'EasyPay-транзакциии',
          to: '/easy-pay/transaction',
        },
        {
          text: 'EasyPay-транзакциия',
          active: true,
        },
      ],
    };
  },
  methods: {
    ...EasyPayTransactionMethods,
  }
}
</script>

<style scoped>

</style>