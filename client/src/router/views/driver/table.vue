<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="card-box">
        <h4 class="header-title">
          <router-link class="btn btn-xs btn-primary" tag="a" to="/drivers/create">Добавить курьера</router-link>
        </h4>
        <b-overlay :show="spiner" rounded="sm" no-center>
          <template v-slot:overlay>
            <div class="centered">
              <b-spinner variant="secondary"></b-spinner>
            </div>
          </template>
          <div class="table-responsive">
            <b-table
                id="drivers-table"
                :items="drivers.data"
                :fields="fields"
                small
                no-local-sorting
                @sort-changed="onSorting"
                :sortDirection="filterParams.sortDirection || 'desc'"
                :sort-by="filterParams.sortField || null"
            >

              <template v-slot:cell(client)="data">
                <router-link v-if="data.item.client" tag="a" :to="`/clients/${data.item.client.number}`">
                  {{data.item.client.label}}
                </router-link>
                <b-badge variant="warning" v-else>
                  Не выбран
                </b-badge>
              </template>

              <template v-slot:top-row="columns">
                <td :key="`filter_${field.key}`" v-for="field in fields">
                    <filter-field v-if="field.filter" :field="field" @on-input="onFilterInput" @on-click-reset-button="onClickResetFiltersButton" />
                </td>
              </template>

              <template v-slot:cell(actions)="data">
                <div class="btn-group">
                  <router-link class="btn btn-warning btn-xs" tag="a"
                               :to="`/drivers/edit/${data.item.number}`">
                    <b-icon icon="pencil"></b-icon>
                  </router-link>
                  <b-button class="btn" variant="danger" size="xs"
                            @click="deleteDriver(data.item.number)">
                    <b-icon icon="trash"></b-icon>
                  </b-button>
                </div>
              </template>
            </b-table>
            <b-pagination
                v-if="drivers.last_page > 1"
                v-model="pageCurrent"
                :total-rows="drivers.total"
                :per-page="drivers.per_page"
                aria-controls="drivers-table"
                align="left"
                @change="paginate"
            ></b-pagination>
          </div> <!-- end table-responsive-->
        </b-overlay>
      </div> <!-- end card-box -->
    </div> <!-- end col -->
  </div>
</template>

<script>

import {BIcon, BIconTrash, BIconPencil} from 'bootstrap-vue';
import FilterField from '@views/components/FilterField';
import {modalConfirm} from "@state/helpers";

export default {
  name: "table-driver",
  props: ['drivers', 'pageCurrent', 'spiner', 'filterParams'],
  components: {
    BIcon,
    BIconTrash,
    BIconPencil,
    FilterField
  },
  data() {
    return {
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
          label: 'Имя',
          sortable: true,
          filter: {
            value: null,
            type: 'text'
          }
        },
        {
          key: 'client',
          label: 'Клиент',
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
  created() {
    this.initValueFilters()
  },
  methods: {
    modalConfirm,
    paginate(pageNum) {
      this.$emit('paginate', pageNum);
    },
    async deleteDriver(id) {
      try {
        const value = await this.modalConfirm('Действительны ли вы хотите это удалить?')
        if (value) {
          this.$emit('delete-by-id', id);
        }
      } catch (e) {
      }
    },
    onSorting(header) {
        if (!header.sortBy) return;

        const params = {};
        params['sortField'] = header.sortBy;
        params['sortDirection'] = header.sortDesc ? 'desc' : 'asc';
        
        this.$emit('sort', params);
    },
    onFilterInput() {
        let params = {};
        for (let field of this.fields) {
            if (field.filter)
                params[field.key] = field.filter.value;
        }
        this.$emit('filters', params)
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

<style lang="scss" module></style>