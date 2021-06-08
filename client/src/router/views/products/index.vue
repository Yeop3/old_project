<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>
    <b-alert :show="isError"
             v-for="(error, i) in actionsError"
             :key="i"
             @dismissed="isError=0"
             variant="danger"
             dismissible
             class="text-center"
    >
      {{ error }}
    </b-alert>
    <Table
        :products="products"
        :status="status_list"
        :page-current="currentPage"
        :locations-select="locations_select"
        :drivers-select="drivers_select"
        :product-types-select="product_types_select"
        :status-list="status_list"
        :spiner="spiner_product"
        :filterParams="filterParams"
        @paginate="pagination"
        @delete-by-id="deleteProduct"
        @filter="filter"
        @checkbox-action="checkboxAction"
        @sort="sort"
        v-if="!spinerAll"
        @change-status="changeStatus"
        :loading="loading"
    />
    <div class="centered" v-else>
      <b-spinner class=""></b-spinner>
    </div>
  </Layout>
</template>

<script>
import {
  botsComputed,
  driversComputed,
  locationsComputed,
  productComputed,
  productMethods,
  productTypesComputed
} from "@state/helpers";
import appConfig from "@src/app.config.json";
import Layout from "@layouts/main";
import PageHeader from "@components/page-header";
import Table from "@views/products/partials/table";
import queryString from "query-string";
import { getFilters, saveFilters } from '@state/helpers'
import axios from "axios";

export default {
  name: "index-product",
  computed: {
    ...productComputed,
    ...locationsComputed,
    ...driversComputed,
    ...productTypesComputed,
  },
  page: {
    title: 'Клады',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader, Table},
  created() {
    this.filterParams = getFilters(this.filterStorageKey)
    this.currentPage = this.filterParams.page || 1;
    this.spinerAll = true;

    Promise.all([
      this.getIndex({params: this.filterParams}),
      this.$store.dispatch('location/getSelect'),
      this.$store.dispatch('drivers/getSelectDriver'),
      this.$store.dispatch('productTypes/getSelectProductType'),
      this.$store.dispatch('product/getStatusList'),
    ]).then().finally(() => this.spinerAll = false);
  },
  methods: {
    ...productMethods,
    async pagination(page) {
      this.currentPage = page;
      this.filterParams.page = page;
      saveFilters(this.filterStorageKey, this.filterParams)
      this.getIndex({params: this.filterParams});
    },
    deleteProduct(id) {
      this.deleteById(id)
          .then((res) => {
            this.getIndex({page: this.page});
          }).catch((res) => {
        this.$bvToast.toast(res.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      });
    },
    changeStatus(params){
    	this.loading = true;
			axios.get(`api/products/change-status`, {params})
				.then(()=> this.getIndex({params: this.filterParams})).finally(()=>this.loading = false);
    },
    filter(filterField) {

      let params = {};
      for (let value of filterField) {
        params[value.name] = value.value;
      }

      this.filterParams = {...this.filterParams, ...params, page: 1};
      this.currentPage = 1;

      saveFilters(this.filterStorageKey, this.filterParams)

      this.getIndex({page: this.currentPage, params: this.filterParams});
    },
    async sort(sortProduct) {

      let params = {};
      params['sortField'] = sortProduct.sortField;
      params['sortDirection'] = sortProduct.sortDirection;

      if (sortProduct.sortField === 'address' || sortProduct.sortField === 'status' || sortProduct.sortField === 'number') {
        params['sortField'] = sortProduct.sortField;
      } else if (sortProduct.sortField === 'created') {
        params['sortField'] = sortProduct.sortField + '_at';
      } else {
        params['sortField'] = sortProduct.sortField + '_id';
      }

      this.filterParams = {...this.filterParams, ...params, page: 1};
      this.currentPage = 1;

      saveFilters(this.filterStorageKey, this.filterParams)

      await this.getIndex({page: this.currentPage, params: this.filterParams});
      
    },
    async checkboxAction({type, numbers}) {
      try {
        await this.actionsSelect({type, numbers}).then((res) => {
          this.actionsError = res.errors;
          this.isError = 5;
        });
        await this.getIndex({page: this.page});
      } catch (res) {
        this.$bvToast.toast(res.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      }
    },
  },
  watch: {
    // $route: 'getIndex'
  },
  data() {
    return {
      filterStorageKey: 'product_filters',
      title: 'Клады',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Клады',
          active: true,
        },
      ],
      spinerAll: false,
      isError: 0,
      actionsError: null,
      currentPage: 1,
      filterParams: {},
      loading: false,
      selectedStatus: null,
    };
  },
};
</script>

<style scoped>

</style>