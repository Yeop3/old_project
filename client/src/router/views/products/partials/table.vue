<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="card-box">
        <h4 class="header-title mb-0 fix-header">
          <router-link class="btn btn-primary mb-2 mr-4 btn-xs" tag="a" to="/products/create/">Добавить клад
          </router-link>
          <b-form-select
              class="mr-2 mb-2"
              :options="checkboxActionSelection"
              v-model="checkboxAction"
              size="sm"
          >
          </b-form-select>
          <b-button variant="primary" size="xs" class="mb-2" @click="checkboxActionMethod">Подтвердить</b-button>
          <router-link class="btn btn-primary float-right btn-xs mb-2" tag="a" to="/products/import">Импортировать списком
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
                :items="data.data"
                :fields="fields"
                small
                no-local-sorting
                @sort-changed="sorting"
                :sortDirection="filterParams.sortDirection || 'asc'"
                :sort-by="filterParams.sortField || null"
                :sortDesc="filterParams.sortDirection !== 'asc'"
                @row-clicked="onProductClick"
            >
              <template v-slot:top-row="columns">
                <td></td>
                <td :key="`filter_${key}`" v-for="(filter, key) in filterField">
                  <template v-if="filter.type === 'select'">
                    <b-select :options="filter.options"
                              @change="onFilterEnter"
                              v-model="filter.value"
                              size="sm"
                    >
                    </b-select>
                  </template>
                  <template v-else-if="filter.type === 'date'">
                    <b-input-group size="sm">
                      <b-form-input 
                        @change="onFilterEnter" 
                        type="date"
                        v-model="filter.value"
                      />
                      <b-button-close
                        v-b-tooltip.hover.right
                        title="Сбросить дату"
                        class="ml-1"
                        @click="resetDate"
                      />
                    </b-input-group>
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

              <template v-slot:head(checkbox)="state">
                <b-form-checkbox
                    name="all_products"
                    v-model="selectAll"
                >
                </b-form-checkbox>
              </template>

              <template v-slot:cell(checkbox)="data">
                <b-form-checkbox
                    :name="`product_${data.item.number}`"
                    :value="data.item.number"
                    v-model="checkboxesProductIds"
                >

                </b-form-checkbox>
              </template>

              <template v-slot:cell(product_type)="data">
                <router-link class="" tag="a"
                             :to="`/product-types/show/${data.item.product_type.number}`">
                  {{ data.item.product_type.name }}
                </router-link>
              </template>
              <template v-slot:cell(location)="data">
                <router-link class="" tag="a"
                             :to="`/locations/show/${data.item.location.number}`">
                  {{ data.item.location.name_chain }}
                </router-link>
              </template>
              <template v-slot:cell(driver)="data">
                <router-link class="" tag="a"
                             :to="`/drivers/edit/${data.item.driver.number}`">
                  {{ data.item.driver.name }}
                </router-link>
              </template>
              <template v-slot:cell(created)="data">
                {{
                  new Date(data.item.created_at).toLocaleString("ru-RU", {
                    year: 'numeric', month:
                        'long',
                    day: 'numeric', hour: 'numeric', minute: 'numeric'
                  })
                }}
              </template>
              <template v-slot:cell(status)="data">
                <b-form-select
                  :disabled="loading || isDisabledStatus(data.item.status)"
                  style="min-width:140px"
                  @change="onChangeProductStatus({number: data.item.number, status: data.item.status})"
                  v-model="data.item.status"
                  size="sm"
                >
                    <b-form-select-option
                      v-for="s in statusList"
                      :key="s.value"
                      :value="s.value"
                      :disabled="isDisabledStatus(s.value)"
                    >
                      {{ s.text }}
                    </b-form-select-option>
                </b-form-select>
              </template>
              <template v-slot:cell(actions)="data">
                <div class="btn-group">
                  <router-link class="btn btn-primary btn-xs" tag="a"
                               :to="`/products/show/${data.item.number}`">
                    <b-icon icon="eye"></b-icon>
                  </router-link>
                  <router-link class="btn btn-warning btn-xs" tag="a"
                               :to="`/products/edit/${data.item.number}`">
                    <b-icon icon="pencil"></b-icon>
                  </router-link>
                  <b-button
                      class="btn"
                      variant="danger"
                      size="xs"
                      @click="deleteProductTypes(data.item.number)"
                      :disabled="data.item.status === 3"
                  >
                    <b-icon icon="trash"></b-icon>
                  </b-button>
                </div>
              </template>
            </b-table>
            <b-pagination
                v-model="currentPage"
                :total-rows="products.total"
                :per-page="products.per_page"
                aria-controls="drivers-table"
                align="left"
                @change="paginate"
                v-if="products.last_page > 1"
            ></b-pagination>
          </div> <!-- end table-responsive-->
        </b-overlay>
        <b-form inline class="mt-2">
          <b-form-select
              :options="checkboxActionSelection"
              v-model="checkboxAction"
              size="sm"
          >
          </b-form-select>
          <b-button variant="primary" size="xs" class="ml-2" @click="checkboxActionMethod">Подтвердить</b-button>
        </b-form>
      </div> <!-- end card-box -->
    </div> <!-- end col -->
    <preview-carousel
      :photos="photos"
    />
  </div>
</template>

<script>
import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';
import {commissionComputed} from '@state/helpers';
import FormInput from "@components/ui/form/FormInput";
import FormSelect from "@components/ui/form/FormSelect";
import PreviewCarousel from "@views/components/FullScreenCarousel";
import axios from 'axios'

export default {
  name: "table-product",
  computed: {
    ...commissionComputed,
    selectAll: {
      get: function () {
        return this.data.data ? this.checkboxesProductIds.length === this.data.data.length : false;
      },
      set: function (value) {
        let selected = [];

        if (value) {
          this.data.data.forEach(function (product) {
            selected.push(product.number);
          });
        }

        this.checkboxesProductIds = selected;
      }
    },
    statusList() {
      return this.status
    }
  },
  props: {
    products: {
      type: Object,
      default: () => {
      }
    },
    filterParams: {
      type: Object,
    },
    status: {
      type: Array,
      default: () => [],
    },
    productTypesSelect: {
      type: Array,
      default: () => ([]),
    },
    locationsSelect: {
      type: Array,
      default: () => ([]),
    },
    driversSelect: {
      type: Array,
      default: () => ([]),
    },
    spiner: {
      type: Boolean
    },
    loading: {
    	type: Boolean,
      default: false
    }

  },
  components: {
    FormSelect,
    BIcon,
    BIconTrash,
    BIconPencil,
    BIconEye,
    FormInput,
    PreviewCarousel
  },
  data() {
    return {
      photos: [],
      data: null,
      changedProductStatus: null,
      page: 1,
      // sortBy: "priority",
      // sortDesc: true,
      fields: [
        {
          key: "checkbox",
          label: ""
        },
        {
          key: 'number',
          label: 'ID',
          sortable: true
        },
        {
          key: 'coordinates',
          label: 'Координаты',
          sortable: false
        },
        {
          key: 'product_type',
          label: 'Товар',
          sortable: true
        },
        {
          key: 'location',
          label: 'Локация',
          sortable: true
        },
        {
          key: 'driver',
          label: 'Курьер',
          sortable: true
        },
        {
          key: 'status',
          label: 'Статус',
          sortable: true
        },
        {
          key: 'created',
          label: 'Добавлен',
          sortable: true
        },
        {
          key: 'actions',
          label: 'Действия'
        },
      ],
      perPage: 3,
      currentPage: 1,
      totalRows: 54,
      filterField: [
        {
          name: "number",
          value: "",
          type: "number"
        },
        {
          name: "coordinates",
          value: null,
          type: "text"
        },
        {
          name: "product_type",
          value: null,
          type: "select"
        },
        {
          name: "location",
          value: null,
          type: "select"
        },
        {
          name: "driver",
          value: null,
          type: "select"
        },
        {
          name: "status",
          value: null,
          type: "select"
        },
        // {
        //   name: "comission",
        //   value: null,
        //   type: "number"
        // },
        {
          name: "created_at",
          value: null,
          type: "date"
        }
      ],
      checkboxesProductIds: [],
      checkboxActionSelection: [
        {
          text: "Действие с выбранными:",
          value: null,
        },
        {
          text: "Удалить выбранные",
          value: "delete_select",
        },
        {
          text: "Перевести в статус \"Продается\"",
          value: "change_sell",
        },
        {
          text: "Перевести в статус \"Не активен\"",
          value: "change_not_active",
        },

      ],
      checkboxAction: null,
      sortProduct: {
        sortField: '',
        sortDirection: '',
      },
      selectedStatus: null,
    };
  },
  watch: {
    'products.data': {
      handler() {
        this.updateData()
      },
      deep: true
    }
  },
  created() {
    this.updateData()
  },
  update() {
    this.updateData()
  },
  methods: {
    resetDate() {
      const date = this.filterField.find(f => f.name === 'created_at')
      if (date) {
        date.value = null
      }
      this.onFilterEnter()
    },
    updateData() {
      this.data = JSON.parse(JSON.stringify(this.products))
      this.currentPage = parseInt(this.filterParams.page) || 1;
      this.filterField = this.filterField.map((value) => {
        if (value.type === "select") {
          switch (value.name) {
            case "driver":
              value.options = [...this.driversSelect];
              value.options.shift();
              value.options.unshift({text: "Все", value: null});
              break;
            case "product_type":
              value.options = [...this.productTypesSelect];
              value.options.shift();
              value.options.unshift({text: "Все", value: null});
              break;
            case "location":
              value.options = [...this.locationsSelect];
              value.options.shift();
              value.options.unshift({text: "Все", value: null});
              break;
            case "status":
              value.options = [...this.status];
              value.options.unshift({text: "Все", value: null});
              break;
          }
        }
        const filterValue = this.filterParams[value.name]
        if (filterValue) {
          value.value = filterValue
        }
        return value;
      });
    },
    onProductClick(product) {
      this.photos = product.photos.map(image => ({url: image.url, number: image.number}))
      if (this.photos.length) {
        this.$bvModal.show("full-screen-modal");
      }
    },
    isDisabledStatus(status) {
      return status > 2
    },
    async onChangeProductStatus({number, status}) {
    	//console.log(number,status);
    	let params = {};
    	params.number = number;
    	params.status = status;
			this.$emit('change-status', params);
			this.selectedStatus = null
		},
    paginate(pageNum) {
      this.currentPage = pageNum;
      this.$emit('paginate', pageNum);
    },
    sorting(header) {
      if (!header.sortBy) return;
      this.sortProduct.sortField = header.sortBy;
      this.sortProduct.sortDirection = header.sortDesc === true ? 'desc' : 'asc';
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
    onFilterEnter() {
      this.currentPage = 1;
      this.$emit('filter', this.filterField);
    },
    onClickResetFiltersButton() {
      this.filterField.forEach(f => f.value = null)
      this.onFilterEnter()
    },
    async checkboxActionMethod() {
			try {
				const value = await this.$bvModal.msgBoxConfirm('Действительно ли вы хотите выполнить данное действие?', {
					title: 'Пожалуйста подтвердите',
					size: 'sm',
					buttonSize: 'sm',
					okVariant: 'warning',
					okTitle: 'Выполнить',
					cancelTitle: 'Отмена',
					footerClass: 'p-2',
					hideHeaderClose: false,
					centered: true
				});
				if (value) {
					if (this.checkboxAction) {
						this.$emit('checkbox-action', {type: this.checkboxAction, numbers: this.checkboxesProductIds});
						this.checkboxAction = [];
					}
				}
			} catch (e) {
			}
    },
  }
};
</script>

<style>
  .fix-header > select{
    max-width: 220px !important;
  }
</style>