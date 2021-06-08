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
              <template v-slot:cell(type)="data">
                {{ type.find((el) => el.value === data.item.type).text }}
              </template>
              <template v-slot:cell(wrong_credentials)="data">
                <span v-if="data.item.wrong_credentials === 1">
                  Неверный логин/пароль
                </span>
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
                <div class="btn-group">
                  <router-link :to="`/global-money/wallet/show/${data.item.number}`" class="btn btn-primary btn-xs">
                    <b-icon icon="eye"></b-icon>
                  </router-link>
                  <router-link :to="`/global-money/wallet/edit/${data.item.number}`" class="btn btn-warning btn-xs">
                    <b-icon icon="pencil"></b-icon>
                  </router-link>
                  <b-button variant="danger" size="xs"  @click="onDelete(data.item.number)">
                    <b-icon icon="trash"></b-icon>
                  </b-button>
                  <b-button @click="check(data.item.login, data.item.password, data.item.type, data.item.index)" v-b-tooltip="'Проверить логин/пароль'" variant="warning" size="xs">
                    <b-icon icon="lock"></b-icon>
                  </b-button>
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
import { BIcon, BIconTrash, BIconPencil, BIconEye, BIconLock } from 'bootstrap-vue'
import { getHumanDate } from '@state/helpers'

export default {
  name: 'global-money-wallet-table',
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
  components: { BIcon, BIconTrash, BIconPencil, BIconEye, BIconLock },
  watch: {
    filterField: {
      handler () {
        this.$emit('filter', this.filterField)
      },
      deep: true
    },
  },
  methods: {
    onDelete (id) {
      this.$emit('on-delete', id)
    },
    check (login,password,type,id){
      this.$emit('access', {login,password,type,id})
    },

  },
  created() {
    //console.log(this.data.data);
  },
  data () {
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
          key: 'created_at',
          label: 'Создан',
          sortable: true
        },
        {
          key: 'proxy',
          label: 'Прокси'
        },
        {
          key: 'wrong_credentials',
          label: 'Предупреждения'
        },
        {
          key: 'actions',
          label: 'Действие',
        },
      ],
      filterField: [
        {
          name: "name",
          value: null,
        },
      ]
    }
  }
}
</script>

<style scoped>

</style>