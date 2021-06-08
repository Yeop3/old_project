<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="card-box">
        <h4 class="header-title">
          <router-link class="btn btn-primary mb-2 btn-xs" tag="a" to="/easy-pay/wallet/create">Добавить
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
                <td>
                  <b-form-input
                      :name="filterField.find((value) => value.name === 'name').name"
                      v-model="filterField.find((value) => value.name === 'name').value"
                      type="text"
                      trim
                      :debounce="1000"
                      size="sm"
                  ></b-form-input>
                </td>
              </template>
              <template v-slot:cell(created_at)="data">
                {{ getHumanDate(data.item.created_at) }}
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

              <template v-slot:cell(actions)="data">
                <div class="btn-group mb-2">
                  <router-link :to="`/easy-pay/wallet/show/${data.item.number}`" class="btn btn-primary btn-xs">
                    <b-icon icon="eye"></b-icon>
                  </router-link>
                  <router-link :to="`/easy-pay/wallet/edit/${data.item.number}`" class="btn btn-warning btn-xs">
                    <b-icon icon="pencil"></b-icon>
                  </router-link>

                  <b-button variant="info " size="xs" @click="restoreBalance(data.item.number)">
                    <b-icon icon="arrow-repeat"></b-icon>
                    Сбросить баланс
                  </b-button>

                  <b-button variant="danger" size="xs"  @click="onDelete(data.item.number)">
                    <b-icon icon="trash"></b-icon>
                  </b-button>
                </div>
              </template>

              <template v-slot:cell(wrong_creadentials)="data">
                <div v-if="data.item.wrong_creadentials">
                  Не верные логин или пароль
                </div>
              </template>

              <template v-slot:cell(limit)="data">
                Лимит: {{ data.item.limit }}
                <div v-if="data.item.is_limit">
                  Лимит превышен
                </div>
              </template>
            </b-table>
          </div>
        </b-overlay>
      </div>
    </div>
  </div>
</template>

<script>
import {BIcon, BIconArrowRepeat, BIconEye, BIconPencil, BIconTrash} from 'bootstrap-vue'
import {getHumanDate} from '@state/helpers'

export default {
  name: 'easy-pay-wallet-table',
  props: {
    data: {
      type: Object,
    },
    loading: {
      type: Boolean,
    },
    type: {
      type: Array
    }
  },
  components: {BIcon, BIconTrash, BIconPencil, BIconEye, BIconArrowRepeat},
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
    async restoreBalance(id) {

      const value = await this.$bvModal.msgBoxConfirm('Действительно ли вы хотите сбросить баланс?', {
        title: 'Пожалуйста подтвердите',
        size: 'sm',
        buttonSize: 'sm',
        okVariant: 'warning',
        okTitle: 'Сбросить баланс',
        cancelTitle: 'Отмена',
        footerClass: 'p-2',
        hideHeaderClose: false,
        centered: true
      });
      if (value) {
        this.$emit('restore-balance', id)
      }
    }
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
      filterField: [
        {
          name: 'name',
          value: null,
        },
      ]
    }
  }
}
</script>

<style scoped>

</style>