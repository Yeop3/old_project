<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="card-box">
        <h4 class="header-title">
          <router-link class="btn btn-primary btn-xs" tag="a" to="/product-types/create/">Добавить товар
          </router-link>
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
                :items="productTypes.data"
                :fields="fields"
                small
                no-local-sorting
                @sort-changed="sorting"
                @row-clicked="rowClick"
                :sortDirection="filterParams.sortDirection || 'asc'"
                :sort-by="filterParams.sortField || null"
                :sortDesc="filterParams.sortDirection !== 'asc'"
            >

              <template v-slot:top-row="columns">
                <td :key="`filter_${key}`" v-for="(filter, key) in filterField">
                  <template v-if="filter.type === 'select'">
                    <b-select :options="filter.options"
                              @change="onFilterEnter"
                              v-model="filter.value"
                              size="sm"
                    >
                    </b-select>
                  </template>
                  <template v-else-if="filter.type === 'skip'">

                  </template>
                  <template v-else>
                    <b-form-input
                        :name="filter.name"
                        :type="filter.type"
                        v-model="filter.value"
                        @keyup.enter="onFilterEnter"
                        size="sm"
                    ></b-form-input>
                  </template>
                </td>
                <td><b-button size="sm" @click="onClickResetFiltersButton">Сбросить</b-button></td>
              </template>


              <template v-slot:cell(commission)="data">
                {{ data.item.commission_value }} {{
                  commission_types.find((value) =>
                      data.item.commission_type
                      === value.value).text
                }}
              </template>
              <template v-slot:cell(actions)="data">
                <div class="btn-group">
                  <router-link class="btn btn-primary btn-xs" tag="a"
                               :to="`/product-types/show/${data.item.number}`">
                    <b-icon icon="eye"></b-icon>
                  </router-link>
                  <router-link class="btn btn-warning btn-xs" tag="a"
                               :to="`/product-types/edit/${data.item.number}`">
                    <b-icon icon="pencil"></b-icon>
                  </router-link>
                  <b-button class="btn" variant="danger" size="xs"
                            @click="deleteProductTypes(data.item.number)">
                    <b-icon icon="trash"></b-icon>
                  </b-button>
                </div>
              </template>
            </b-table>
            <b-pagination
                v-if="productTypes.last_page > 1"
                :total-rows="productTypes.total"
                :per-page="productTypes.per_page"
                v-model="currentPage"
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
import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';
import {commissionComputed} from '@state/helpers';

export default {
  name: "table-product-types",
  computed: {
    ...commissionComputed,
  },
  props: ['productTypes', 'pageCurrent', 'commissionType', 'spiner', 'filterParams'],
  components: {
    BIcon,
    BIconTrash,
    BIconPencil,
    BIconEye,
  },
  data() {
    return {
      page: 1,
      sortBy: "priority",
      sortDesc: true,
      filterField: [
        {
          name: "number",
          value: null,
          type: "number"
        },
        {
          name: "name",
          value: null,
          type: "text"
        },
        {
          name: "price",
          value: null,
          type: "number"
        },
        {
          name: "comission",
          value: null,
          type: "number"
        },
      ],
      fields: [
        {
          key: 'number',
          label: 'ID',
          sortable: true
        },
        {
          key: 'name',
          label: 'Имя',
          sortable: true
        },
        {
          key: 'price',
          label: 'Цена',
          sortable: true
        },
        {
          key: 'commission',
          label: 'Комиссия',
          sortable: true
        },
        {
          key: 'actions',
          label: 'Действия'
        },
      ],
      perPage: 20,
      currentPage: 1,
      totalRows: 20,
      sortProduct: {
        sortField: '',
        sortDirection: '',
      }
    };
  },
  created() {
    this.updateData()
  },
  update() {
    this.updateData()
  },
  methods: {
    updateData() {
      this.currentPage = this.filterParams.page || 1;

      this.filterField = this.filterField.map((value) => {
        if (this.filterParams[value.name]) {
          value.value = this.filterParams[value.name];
        }
        return value;
      });
    },
    onFilterEnter() {
      this.currentPage = 1;
      this.$emit('filters', this.filterField);
    },
    paginate(pageNum) {
      this.$emit('paginate', pageNum);
    },
    sorting(header) {
      if (!header.sortBy) return;
      this.sortProduct.sortField = header.sortBy;
      this.sortProduct.sortDirection = header.sortDesc === true ? 'desc' : 'asc';
      if (header.sortBy === 'commission') {
        this.sortProduct.sortField = 'commission_value'
      }
      this.$emit('sort', this.sortProduct);
    },
    async deleteProductTypes(id) {
      try {
        const value = await this.$bvModal.msgBoxConfirm('Действительно ли вы хотите это удалить?', {
          title: 'Пожалуйста подтвердите',
          size: 'sm',
          buttonSize: 'sm',
          okVariant: 'danger',
          okTitle: 'Удалить',
          cancelTitle: 'Отмена',
          footerClass: 'p-2',
          hideHeaderClose: false,
          centered: true
        });
        if (value) {
          this.$emit('delete-by-id', id);
        }
      } catch (e) {
      }
    },
    onClickResetFiltersButton() {
      this.filterField.forEach(f => f.value = null)
      this.onFilterEnter()
    },
    filters() {
      this.filterField.find((value) => value.name === 'name').value = this.filterField.find(
          (value) => value.name === 'name').value.trim();
      this.$emit('filters', this.filterField);
    },
    rowClick(item, index) {
      //console.log(item, index);
    }
  }

};
</script>

<style lang="scss" module></style>