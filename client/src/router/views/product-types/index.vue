<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>
    <Table
        :product-types="product_types"
        :page-current="page"
        :commission-type="commission_types"
        :spiner="spiner"
        :filterParams="filterParams"
        @paginate="pagination"
        @delete-by-id="deleteProductTypes"
        @filters="filters"
        @sort="sort"
    />
  </Layout>
</template>

<script>
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {productTypesComputed, productTypesMethods, commissionComputed} from "@state/helpers";
import Table from '@views/product-types/table';
import queryString from "query-string";
import { getFilters, saveFilters } from '@state/helpers'

export default {
  computed: {
    ...productTypesComputed,
    ...commissionComputed,
  },
  page: {
    title: 'Товары',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader, Table},
  name: "index-product-types",
  data() {
    return {
      filterStorageKey: 'product_type_filters',
      title: 'Товары',
      filterParams: {},
      page: 1,
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Товары',
          active: true,
        },
      ],
    };
  },
  async mounted() {
    this.filterParams = getFilters(this.filterStorageKey)
    this.page = this.filterParams.page || 1;

    await this.getIndex({params: this.filterParams});
  },
  methods: {
    ...productTypesMethods,
    pagination(page) {
      this.page = page;
      this.filterParams.page = page;
      saveFilters(this.filterStorageKey, this.filterParams)
      this.getIndex({params: this.filterParams});
    },
    deleteProductTypes(id) {
      this.deleteById(id)
          .then(() => {
            this.getIndex({page: this.page});
          }).catch((err) => {
        this.$bvToast.toast(err.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      });
    },
    async filters(filterField) {
      let params = {};
      for (let value of filterField) {
        params[value.name] = value.value;
      }
      this.filterParams = {...this.filterParams, ...params, page: 1};
      this.page = 1;

      saveFilters(this.filterStorageKey, this.filterParams)

      await this.getIndex({params: this.filterParams});
    },
    async sort(sortProduct) {
      this.filterParams = {...this.filterParams, ...sortProduct, page: 1};
      this.page = 1;

      saveFilters(this.filterStorageKey, this.filterParams)

      await this.getIndex({params: this.filterParams});
    },
  },
  watch: {
    // $route: 'getIndex'
  },
};
</script>

<style lang="scss" module></style>