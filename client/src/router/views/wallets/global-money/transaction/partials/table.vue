<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="card-box">
        <h4 class="header-title">
          <router-link class="btn btn-primary mb-2 btn-xs" tag="a" to="/global-money/wallet/create">Добавить
          </router-link>
        </h4>
        <b-overlay :show="loading" rounded="sm" no-center>
          <template v-slot:overlay>
            <div class="centered">
              <b-spinner variant="secondary"></b-spinner>
            </div>
          </template>
          <div class="table-responsive">
            <b-table
                :items="data.data"
                :fields="fields"
                small
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                sortDirection="desc"
            >
              <template v-slot:top-row="columns">
                <td></td>
                <td></td>
                <td></td>
                <td>
                  <b-form-select
                      v-model="filterField.find((value) => value.name === 'wallet').value"
                      :options="globalMoneyWalletSelect"
                      size="sm"
                  >
                  </b-form-select>
                </td>
                <td>

                  <b-form-select
                      v-model="filterField.find((value) => value.name === 'order').value"
                      :options="orderSelect"
                      size="sm"
                  >
                  </b-form-select>
                </td>
              </template>

              <template v-slot:cell(transaction_id)="data">
                <div v-if="data.item.transaction_id">
                  {{data.item.transaction_id}}
                </div>
                <div v-else>
                  Создана вручную
                </div>
              </template>

              <template v-slot:cell(amount)="data">
                {{ data.item.amount.amount / 100 }} {{ data.item.amount.currency }}
              </template>

              <template v-slot:cell(global_money_wallet)="data">
                <router-link :to="`/global-money/wallet/show/${data.item.order.number}`">
                  {{ (data.item.global_money_wallet.name) }}
                </router-link>
              </template>

              <template v-slot:cell(order)="data">
                <router-link :to="`/orders/show/${data.item.order.number}`">
                  #{{ (data.item.order.number) }}
                </router-link>
              </template>

              <template v-slot:cell(created_at)="data">
                {{ getHumanDate(data.item.created_at) }}
              </template>

              <template v-slot:cell(actions)="data">
                <router-link :to="`/global-money/transaction/show/${data.item.number}`" class="btn btn-primary btn-xs">
                  <b-icon icon="eye"></b-icon>
                </router-link>
              </template>
            </b-table>
          </div>
        </b-overlay>
      </div>
    </div>
  </div>
</template>

<script>
import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue'
import {getHumanDate} from '@state/helpers'

export default {
  name: 'global-money-wallet-table',
  props: {
    data: {
      type: Object,
    },
    loading: {
      type: Boolean,
    },
    globalMoneyWalletSelect: {
      type: Array
    },
    orderSelect: {
      type: Array
    }
  },
  components: {BIcon, BIconTrash, BIconPencil, BIconEye},
  watch: {
    filterField: {
      handler() {
        this.$emit('filter', this.filterField)
      },
      deep: true
    },
  },
  methods: {
    onDelete(id) {
      this.$emit('on-delete', id)
    },
  },
  data() {
    return {
      getHumanDate,
      sortBy: 'number',
      sortDesc: true,
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

        {
          key: 'actions',
          label: 'Действие',
        },
      ],
      filterField: [
        {
          name: "wallet",
          value: null,
        },
        {
          name: "order",
          value: null,
        },

      ]
    }
  }
}
</script>

<style scoped>

</style>