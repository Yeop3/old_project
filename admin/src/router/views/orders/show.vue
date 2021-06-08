<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>
    <div class="row">
      <div class="col-lg-12">
        <div class="card-box" v-if="!spiner_table">
          <div class="table-responsive">
            <b-table
                id="drivers-table"
                stacked=""
                :items="[order]"
                :fields="fields"
                small
            >
              <template v-slot:cell(created_at)="data">
                {{
                  new Date(data.item.created_at).toLocaleString("ru-RU", {
                    year: 'numeric', month:
                        'numeric',
                    day: 'numeric'
                  })
                }}
                <div class="text-muted small">
                  {{
                    new Date(data.item.created_at).toLocaleString("ru-RU", {
                      hour: 'numeric', minute:
                          'numeric'
                    })
                  }}
                </div>
                <div class="text-muted small">
                  {{ moment(data.item.created_at).locale('ru').startOf('hour').fromNow() }}
                </div>
              </template>
              <template v-slot:cell(client)="data">
                {{ data.item.client.name }}
              </template>
              <template v-slot:cell(product)="data">
                {{ data.item.product.product_type.name }}
                <div class="nowrap">
                  {{ data.item.price.amount / 100 }}
                  <span class="small text-danger" v-if="data.item.discount">
                                         - {{ data.item.discount.discount_value }}%
                                    </span>
                  <span class="small text-danger">
                                         + {{ data.item.commission.amount / 100 }}
                                    </span>
                  = {{ data.item.total_price.amount / 100 }} грн
                </div>
                <div class="small text-warning" v-if="data.item.discount">
                  Скидка: {{ data.item.discount.name }}
                </div>
                <div class="small text-warning">
                  Комиссия: {{ data.item.commission.amount / 100 }} грн
                </div>
                <div class="small">
                  {{ data.item.product.location.name_chain }}
                </div>
              </template>

              <template v-slot:cell(account)="data">
                <template v-if="data.item.wallet_type === 'wallet_qiwi_manual'">
                  <div class="">QIWI</div>
                  <div class=" small text-muted">
                    ручной
                  </div>
                  <div class=" small">
                    {{ data.item.wallet.phone }}
                  </div>
                </template>

                <template v-if="data.item.wallet_type === 'global_money'">
                  <div class="small">

                    <div>Globalmoney</div>
                    <div>
                      <router-link
                          :to="`/global-money/show/${data.item.wallet.number}`">
                        {{ data.item.wallet.name }}
                      </router-link>
                    </div>
                    <div v-for="manualPayment in data.item.transactions" :key="manualPayment.number">
                      <router-link
                          :to="`global-money/transaction/show/${manualPayment.number}`"
                      >
                        {{ `${manualPayment.amount.amount / 100} ${manualPayment.amount.currency}` }}
                      </router-link>
                    </div>
                  </div>
                </template>


              </template>

              <template v-slot:cell(source)="data">
                <template v-if="data.item.source_type === 'bot'">
                  <div class="">Бот</div>
                  <div class=" small">
                    {{ data.item.source.name }}
                  </div>
                </template>
              </template>

              <template v-slot:cell(shift)="data">
                <template v-if="data.item.shift.operator">
                  {{ data.item.shift.operator.name }}
                </template>
                <template v-else>
                  Удален
                </template>
              </template>

              <template v-slot:cell(status)="data">
                <div class="">

                  {{ order_status[data.item.status] }}
                  <div>
                    {{
                      new Date(data.item.updated_at).toLocaleString("ru-RU", {
                        year: 'numeric',
                        month:
                            'numeric',
                        day: 'numeric'
                      })
                    }}
                  </div>
                  <div class="text-muted small">
                    {{
                      new Date(data.item.updated_at).toLocaleString("ru-RU", {
                        hour: 'numeric',
                        minute:
                            'numeric'
                      })
                    }}
                  </div>
                  <div class="text-muted small">
                    {{ moment(data.item.updated_at).locale('ru').startOf('hour').fromNow() }}
                  </div>
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
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {commissionComputed, orderComputed, orderMethods, productComputed, productMethods} from '@state/helpers';

export default {
  name: "show-product",
  components: {PageHeader, Layout},
  computed: {
    ...orderComputed,
  },
  page: {
    title: 'Просмотр',
    meta: [{name: 'description', content: appConfig.description}],
  },
  async created() {
    await this.getById(this.$route.params.id);
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
          key: 'created_at',
          label: 'Создан',

        },
        {
          key: 'client',
          label: 'Клиент',
        },
        {
          key: 'product',
          label: 'Клад',
        },
        {
          key: 'account',
          label: 'Аккаунт',
        },
        {
          key: 'source',
          label: 'Источник',
        },
        {
          key: 'shift',
          label: 'Смена',
        },
        {
          key: 'status',
          label: 'Статус изменен',
        },

      ],
      title: 'Просмотр ',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Заказы',
          to: '/orders',
        },
        {
          text: 'Просмотр',
          active: true,
        },
      ],
    };
  },
  methods: {
    ...orderMethods
  }
};
</script>

<style lang="scss" module></style>