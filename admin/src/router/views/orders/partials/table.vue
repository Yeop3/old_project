<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="card-box">
        <b-overlay :show="spiner" rounded="sm" center>
          <template v-slot:overlay>
            <div class="centered">
              <b-spinner variant="secondary"></b-spinner>
            </div>
          </template>
          <div class="table-responsive">
            <b-table
                id="drivers-table"
                :items="orders.data"
                :fields="fields"
                small
                sortDirection="desc"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
            >
              <template v-slot:top-row="columns">
                <td>
                  <b-form-input
                      :name="filterField.find((value) => value.name === 'number').name"
                      :type="filterField.find((value) => value.name === 'number').type"
                      v-model="filterField.find((value) => value.name === 'number').value"
                      @blur="filters"
                      size="sm"
                  >
                  </b-form-input>
                </td>
                <td>
                  <b-select
                      :options="filterField.find((value) => value.name === 'order_sellers').options"
                      v-model="filterField.find((value) => value.name === 'order_sellers').value"
                      @change="filters"
                      size="sm"
                  >
                  </b-select>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                  <b-select
                      :options="filterField.find((value) => value.name === 'order_status').options"
                      v-model="filterField.find((value) => value.name === 'order_status').value"
                      @change="filters"
                      size="sm"
                  >
                  </b-select>
                </td>
              </template>
              <template v-slot:cell(seller)="data">
                {{ data.item.seller.domain }}
              </template>
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
                  <div class="small text-center">

                    <div>QIWI</div>
                    <div class="text-muted">
                      ручной
                    </div>
                    <div>
                      <router-link
                          :to="`wallets/qiwi_manual?phone=${data.item.wallet.number}`">
                        {{ data.item.wallet.phone }}
                      </router-link>
                      <b-link
                          class="ml-1"
                          v-b-tooltip.hover="copyText"
                          v-clipboard:copy="data.item.wallet.phone"
                          v-clipboard:success="onCopyText"
                      >
                        <b-icon-clipboard/>
                      </b-link>
                    </div>
                    <div class="text-center">
                      оплаты
                    </div>
                    <div v-for="manualPayment in data.item.transactions" :key="manualPayment.number">
                      {{ `${manualPayment.amount.amount / 100} ${manualPayment.amount.currency}` }}
                    </div>

                  </div>

                </template>

                <template v-if="data.item.wallet_type === 'global_money'">
                  <div class="small text-center">

                    <div>Globalmoney</div>
                    <div>
                        {{ data.item.wallet.name }}
                      <b-link
                          class="ml-1"
                          v-b-tooltip.hover="copyText"
                          v-clipboard:copy="data.item.wallet.name"
                          v-clipboard:success="onCopyText"
                      >
                        <b-icon-clipboard/>
                      </b-link>
                    </div>
                    <div class="text-center">
                      оплаты
                    </div>
                    <div v-for="manualPayment in data.item.transactions" :key="manualPayment.number">

                        {{ `${manualPayment.amount.amount / 100} ${manualPayment.amount.currency}` }}
                    </div>

                  </div>
                </template>

                <template v-if="data.item.wallet_type === 'wallet_crypto'">
                  <div class="text-center">
                    <div>
                      {{ data.item.wallet.currency.toUpperCase() }}
                    </div>
                    <router-link
                        :to="`/crypto-wallet/show/${data.item.wallet.number}`">
                      {{ data.item.wallet.name }}
                    </router-link>
                    <b-link
                        v-b-tooltip.hover="copyText + ' кошелек'"
                        v-clipboard:copy="data.item.wallet.address"
                        v-clipboard:success="onCopyText"
                    >
                      <b-icon-clipboard/>
                    </b-link>
                  </div>
                  <div class="small" style="max-width:150px">
                    <div class="text-center">
                      оплаты
                    </div>
                    <div v-for="transaction in data.item.transactions" :key="transaction.number">
                      <b-row align-h="center">
                        <b-col :cols="transaction.address ? '5' : '12'" class="pr-0">
                          {{ `${transaction.amount.amount / 100} ${transaction.amount.currency}` }}
                        </b-col>
                        <template v-if="transaction.address">
                          <b-col cols="6" class="text-truncate">
                            <router-link
                                :to="`crypto-transaction/show/${transaction.number}`">
                              {{ transaction.address }}
                            </router-link>
                          </b-col>
                          <b-col cols="1">
                            <b-link
                                v-b-tooltip.hover="copyText"
                                v-clipboard:copy="transaction.address"
                                v-clipboard:success="onCopyText"
                            >
                              <b-icon-clipboard/>
                            </b-link>
                          </b-col>
                        </template>
                      </b-row>
                    </div>
                  </div>
                </template>


                <template v-if="data.item.wallet_type === 'kuna_account'">
                  <div class="text-center">
                    Kuna-аккаунт
                    <router-link
                        class="d-block"
                        :to="`kuna-accounts/show/${data.item.wallet.number}`">
                      {{ data.item.wallet.name }}
                    </router-link>

                  </div>
                  <div class="small" style="max-width:150px">
                    <div class="text-center">
                      kuna-коды
                    </div>
                    <div v-for="kunaCode in data.item.transactions" :key="kunaCode.number">
                      <b-row align-h="center">
                        <b-col :cols="transaction.code ? '5' : '12'" class="pr-0">
                          {{ `${kunaCode.amount.amount / 100} ${kunaCode.amount.currency}` }}
                        </b-col>
                        <template v-if="kunaCode.code">
                          <b-col cols="6" class="text-truncate">
                            <router-link
                                :to="`kuna-codes/show/${kunaCode.number}`">
                              {{ kunaCode.code }}
                            </router-link>
                          </b-col>
                          <b-col cols="1">
                            <b-link
                                v-b-tooltip.hover="copyText"
                                v-clipboard:copy="kunaCode.code"
                                v-clipboard:success="onCopyText"
                            >
                              <b-icon-clipboard/>
                            </b-link>
                          </b-col>
                        </template>
                      </b-row>
                    </div>
                  </div>

                </template>

                <div v-if="data.item.status === 8" class="text-center text-success">
                  Отдан за {{
                    data.item.transactions.map(t => parseInt(t.amount.amount)).reduce((first, next) => first + next, 0) / 100
                  }} {{ data.item.transactions[0].amount.currency }}
                </div>

              </template>

              <template v-slot:cell(source)="data">
                <template v-if="data.item.source_type === 'bot'">
                  <div class="text-center">Бот</div>
                  <div class="text-center small">
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
                <div class="text-center">
                  {{ orderStatus[data.item.status] }}

                  <div>
                    <br>{{
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

              <template v-slot:cell(actions)="data">
                <router-link class="btn btn-primary btn-xs mr-2" tag="a"
                             :to="`/orders/show/${data.item.number}`">
                  <b-icon icon="eye"></b-icon>
                </router-link>
              </template>
            </b-table>
          </div> <!-- end table-responsive-->
        </b-overlay>
      </div> <!-- end card-box -->
    </div> <!-- end col -->
  </div>
</template>

<script>
import FormInput from "@components/ui/form/FormInput";
import {BIcon, BIconEye, BIconClipboard} from 'bootstrap-vue';

export default {
  name: "table-orders",
  components: {FormInput, BIcon, BIconEye, BIconClipboard},
  props: {
    orders: {
      type: Object
    },
    spiner: {
      type: Boolean
    },
    orderStatus: {
      type: [Object],
      default: () => ({})
    },
    orderSellers: {
      type: Array
    },
    filterFields: {
      type: Array,
      default: () => ([])
    }
  },
  mounted() {
    this.filterField = [...this.filterFields];
    this.filterField = this.filterField.map((value) => {
      switch (value.name) {
        case "order_status":
          value.options = [];
          for (let item in this.orderStatus) {
            if (this.orderStatus.hasOwnProperty(item))
              value.options.push({text: this.orderStatus[item], value: item});
          }
          value.options.unshift({text: "Все", value: null});
          value.value = this.$route.query.order_status || null;
          break;
        case "number":
          value.value = this.$route.query.number || null;
          break;
        case "order_sellers":
          value.options = [];
          this.orderSellers.forEach((item) => {
            value.options.push({text: item.domain, value: item.id});
          });
          value.options.unshift({text: "Все", value: null});
          value.value = this.$route.query.order_sellers || null;
          break;
      }
      return value;
    });
    // console.log(this.filterField);
  },
  data() {
    return {
      copyText: 'Скопировать',
      sortBy: "number",
      titleModal: "Отдать заказ #",
      sortDesc: true,
      price: 0,
      fields: [
        {
          key: 'number',
          label: 'ID',
          sortable: true
        },
        {
          key: 'seller',
          label: 'Продавец',
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
        {
          key: 'actions',
          label: 'Просмотр',
        },

      ],
      perPage: 3,
      currentPage: 1,
      totalRows: 54,
      item: null,
      filterField: [
        {
          name: "number",
          value: "",
          type: "number"

        },
        {
          name: 'order_sellers',
          value: null,
          type: 'select',
        },
        {
          name: "order_status",
          value: null,
          type: "select"
        }
      ],
    };
  },
  methods: {
    onCopyText(e) {
      this.$bvToast.toast('Текст скопирован', {
        title: 'Уведомление',
        variant: 'success',
        autoHideDelay: 3000,
      });
    },
    async filters() {
      this.$emit('filters', this.filterField);
    }
  }
};
</script>

<style scoped>

</style>