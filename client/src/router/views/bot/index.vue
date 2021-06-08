<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box">
          <div class="card">
            <div class="card-body">
              <b-overlay :show="spiner" rounded="sm" no-center>
                <b-button class="btn mb-1" size="xs" :to="'/bots/create'" variant="primary">
                  Добавить бота
                </b-button>
                <template v-slot:overlay>
                  <div class="centered">
                    <b-spinner variant="secondary"></b-spinner>
                  </div>
                </template>
                <template v-if="data.data">
                  <b-table
                      id="bots-table"
                      :items="data.data"
                      :fields="fields"
                      small
                      responsive
                      no-local-sorting
                      @sort-changed="onSorting"
                      :sortDirection="filterParams.sortDirection || 'desc'"
                      :sort-by="filterParams.sortField || null"
                  >
                    <template v-slot:top-row="columns">
                      <td :key="`filter_${field.key}`" v-for="field in fields">
                        <filter-field v-if="field.filter" :field="field" @on-input="onFilterInput" @on-click-reset-button="onClickResetFiltersButton" />
                      </td>
                    </template>

                    <template v-slot:cell(operator)="row">
                      <template v-if="row.item.operator">
                        {{row.item.operator.name}}({{row.item.operator.client ? row.item.operator.client.telegram_id :  'клиент не назначен' }})
                      </template>
                      <template v-else>
                        Не указан
                      </template>
                    </template>

                    <template v-slot:cell(drivers)="row">
                      <b-badge
                        v-for="driver in row.item.drivers"
                        :key="driver.number"
                        variant="info"
                        class="mr-1 text-dark"
                      >
                        {{driver.name}}
                      </b-badge>
                    </template>

                    <template v-slot:cell(type)="row">
                      Стандартный
                      <br>
                      Telegram
                    </template>

                    <template v-slot:cell(logic)="row">
                      {{ row.item.logic.name }}
                    </template>

                    <template v-slot:cell(actions)="row">
                      <div class="btn-group">
                        <b-button v-b-tooltip="'Посмотреть'"
                                  @click="tryToShowCardBot(data.data[row.index].number)" size="xs"
                                  variant="primary">
                          <b-icon icon="eye"></b-icon>
                        </b-button>

                        <b-button v-b-tooltip="'Изменить'"
                                  @click="tryToChangeInfoBot(data.data[row.index].number)" size="xs"
                                  class="btn-warning">
                          <b-icon icon="pencil"></b-icon>
                        </b-button>

                        <b-button v-b-tooltip="'Удалить'"
                                  @click="tryToDeleteBot(data.data[row.index].number)" size="xs"
                                  class="btn-danger">
                          <b-icon icon="trash"></b-icon>
                        </b-button>
                      </div>
                    </template>
                  </b-table>

                  <b-pagination
                      v-if="data.last_page > 1"
                      v-model="currentPage"
                      :total-rows="data.total"
                      :per-page="data.per_page"
                      aria-controls="bots-table"
                      class="float-right"
                  ></b-pagination>
                </template>
              </b-overlay>
            </div>
          </div>
        </div>
      </div>

    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {botsMethods, botsComputed, modalConfirm, getFilters, saveFilters } from "@state/helpers";
import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';
import FilterField from '@views/components/FilterField';
import { mapActions, mapState } from 'vuex';

export default {
  name: "IndexSellers",
  page: {
    title: 'Dashboard',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {
    Layout,
    PageHeader, 
    BIcon, BIconTrash, BIconPencil, BIconEye, FilterField},
  data() {
    return {
      currentPage: 1,
      data: {},
      filterParams: {},
      filterStorageKey: 'bot_filters',
      title: 'Боты',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Боты',
          active: true,
        },
      ],
      fields: [
        {
          key: 'number',
          label: 'ID',
          sortable: true,
          filter: {
            value: null,
            type: 'number'
          }
        },
        {
          key: 'name',
          label: 'Название',
          sortable: true,
          filter: {
            value: null,
            type: 'text'
          }
        },
        {
          key: 'operator',
          label: 'Ответственный оператор',
          filter: {
            value: null,
            type: 'select',
            options: []
          }
        },
        {
          key: 'drivers',
          label: 'Курьеры',
          filter: {
            value: null,
            type: 'select',
            options: []
          }
        },
        {
          key: 'type',
          label: 'Тип'
        },
        {
          key: 'logic',
          label: 'Логика'
        },
        {
          key: 'actions',
          label: 'Действия',
          filter: {
            type: 'reset-button'
          }
        },
      ],
    };
  },
  computed: {
    ...botsComputed,
    ...mapState('operator', {operatorsList: 'operators_select'}),
    ...mapState('drivers', {driversList: 'drivers_select'})
  },
  async created() {
    this.filterParams = getFilters(this.filterStorageKey)
    this.currentPage = this.filterParams.page || 1
    await this.fetchData()
    await this.getOperatorsList()
    await this.getDriversList()
    this.fields.find(f => f.key == 'operator').filter.options = this.operatorsList
    this.fields.find(f => f.key == 'drivers').filter.options = this.driversList
    this.initValueFilters()
  },
  methods: {
    ...mapActions('operator', {getOperatorsList: 'getSelect'}),
    ...mapActions('drivers', {getDriversList: 'getSelectDriver'}),
    ...botsMethods,
    modalConfirm,
    async fetchData() {
      this.data = await this.loadAll({params: this.filterParams});
    },
    tryToShowCardBot(number) {
      return this.$router.push({name: 'bots.show', params: {number}});
    },
    tryToChangeInfoBot(number) {
      return this.$router.push({name: 'bots.edit', params: {number}});
    },
    async tryToDeleteBot(number) {
      try {
        const value = this.modalConfirm('Действительны ли вы хотите это удалить?')
        if (value) {
          await this.delete(number)
          await this.fetchData()
        }
      } catch (e) {}
    },
    async onFilterInput() {

      let params = {};
      for (let field of this.fields) {
        if (field.filter)
          params[field.key] = field.filter.value;
      }

      this.filterParams = {...this.filterParams, ...params, page: 1};
      this.currentPage = 1;

      saveFilters(this.filterStorageKey, this.filterParams)

      await this.fetchData()

    },
    async onSorting(header) {
      if (!header.sortBy) return;

      const params = {};
      params['sortField'] = header.sortBy;
      params['sortDirection'] = header.sortDesc === true ? 'desc' : 'asc';

      this.filterParams = {...this.filterParams, ...params, page: 1};
      this.currentPage = 1;

      saveFilters(this.filterStorageKey, this.filterParams)

      await this.fetchData()

    },
    clearFilters() {
      this.fields.forEach(f => {
        if (f.filter)
          f.filter.value = null
      })
    },
    onClickResetFiltersButton() {
      this.clearFilters()
      this.onFilterInput()
    },
    initValueFilters() {
      this.fields.forEach(field => {
        if (!field.filter) return
        const filterValue = this.filterParams[field.key]
        if (filterValue) {
          field.filter.value = filterValue
        }
      })
    },
  }
};
</script>
