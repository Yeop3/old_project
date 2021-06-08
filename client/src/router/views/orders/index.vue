<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <Table
        :orders="orders"
        :order-status="order_status"
        :spiner="spiner_table"
        :filter-fields="filterField"
        :filterParams="filterParams"
        :errors-modal="errorsModal"
        @set-give-status-order="setGiveStatusOrder"
        @set-transfer-status-order="setTransferStatusOrder"
        @set-cancel-status-operator-order="setCancelStatusOrder"
        @restor-canceled-order="restorationOrderCanceled"
        @restor-paid-order="restorationOrderPaid"
        @send-message-to-client="tryToSendMessageToClient"
        @paginate="pagination"
        @filters="filters"
        @sort="sort"
    >
    </Table>

  </Layout>
</template>

<script>
import {
  orderComputed,
  orderMethods,
  clientsMethods
} from "@state/helpers";
import appConfig from "@src/app.config.json";
import Layout from "@layouts/main";
import PageHeader from "@components/page-header";
import Table from "@views/orders/partials/table";
import queryString from 'query-string';
import { getFilters, saveFilters } from '@state/helpers'

export default {
  name: "index-orders",
  computed: {
    ...orderComputed,
  },
  page: {
    title: 'Все заказы',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader, Table},
  async created() {
    // this.filterParams = this.$route.query;

    this.filterParams = getFilters(this.filterStorageKey)

    this.currentPage = this.filterParams.page || 1;

    this.getIndex({params: this.filterParams});

    await this.setCountFilterStatus();

    // this.spinerCounter = false;
  },
  methods: {
    ...orderMethods,
    ...clientsMethods,
    reloadOrders() {
      const query = queryString.stringify({...this.filterParams, page: this.currentPage});
      this.$router.push(`/orders?${query}`);
    },
    async pagination(page) {
      this.currentPage = page;
      this.filterParams.page = page;

      saveFilters(this.filterStorageKey, this.filterParams)

      this.getIndex({params: this.filterParams});

      // this.reloadOrders();
    },
    async restorationOrderCanceled(number) {
      await this.restorationCanceledOrder(number)
        .then((res) => {
          this.getIndex({page: this.currentPage, params: this.filterParams});
          this.getCounter()
          this.$bvToast.toast('Товар успешно восстановлен.', {
            title: 'Success',
            variant: 'success',
            autoHideDelay: 5000,
          });
        })
        .catch((err) => {
          console.log(err.response);
          this.$bvToast.toast(err.response.data.message, {
            title: 'Errors',
            variant: 'danger',
            autoHideDelay: 5000,
          });
        });
    },
    async tryToSendMessageToClient(field) {
      await this.sendMessageToClient(field).then((res) => {
      }).catch((err) => {
        this.errorsModal = err.response.data.message;
        this.$bvToast.toast(this.errors, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        })
      });
    },
    async restorationOrderPaid(number) {
      await this.restorationPaidOrder(number).then((res) => {
        this.getIndex({page: this.currentPage, params: this.filterParams});
        this.getCounter()
        this.$bvToast.toast('Товар успешно восстановлен.', {
          title: 'Success',
          variant: 'success',
          autoHideDelay: 5000,
        });
      })
          .catch((err) => {
            this.$bvToast.toast(err.response.data.message, {
              title: 'Errors',
              variant: 'danger',
              autoHideDelay: 5000,
            });
          });
    },
    async setGiveStatusOrder(item) {
      try {
        await this.setStatusGive(item);
        await this.getIndex({page: this.currentPage, params: this.filterParams});
        await this.getCounter();
      } catch (e) {
        this.$bvToast.toast(e.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      }
    },
    async setTransferStatusOrder(item) {
      try {
        await this.setTransferStatus(item);
        await this.getIndex({page: this.currentPage, params: this.filterParams});
        await this.getCounter();
      } catch (e) {
        this.$bvToast.toast(e.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      }
    },
    async setCancelStatusOrder(id) {
      try {
        await this.setCancelOperatorStatus(id);
        await this.getIndex({page: this.currentPage, params: this.filterParams});
        await this.getCounter();
      } catch (e) {
        console.log(e);
        this.$bvToast.toast(e.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      }
    },
    async getCounter() {
      this.spinerCounter = true;
      await this.setCountFilter();
      await this.setCountFilterStatus(this.filterField.find((value) => value.name === 'order').value);
      this.spinerCounter = false;
    },
    async sort(sortProduct) {
      let params = {};

      params['sortDirection'] = sortProduct.sortDirection;
      params['sortField'] = sortProduct.sortField

      this.filterParams = {...this.filterParams, ...params, page: 1};
      this.currentPage = 1;

      saveFilters(this.filterStorageKey, this.filterParams)

      await this.getIndex({page: this.currentPage, params: this.filterParams});

    },
    async cancelAll() {
      try {
        await this.setCancelAll();
        await this.getIndex({page: this.currentPage, params: this.filterParams});
        await this.getCounter();
      } catch (e) {
        this.$bvToast.toast(e.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      }
    },
    async cancelAwaiting() {
      try {
        await this.setCancelAwaiting();
        await this.getIndex({page: this.currentPage, params: this.filterParams});
        await this.getCounter();
      } catch (e) {
        this.$bvToast.toast(e.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      }
    },
    async cancelPartially() {
      try {
        await this.setCancelPartially();
        await this.getIndex({page: this.currentPage, params: this.filterParams});
        await this.getCounter();
      } catch (e) {
        this.$bvToast.toast(e.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      }
    },
    async sort(sortProduct) {
      let params = {};

      params['sortDirection'] = sortProduct.sortDirection;
      params['sortField'] = sortProduct.sortField

      this.filterParams = {...this.filterParams, ...params, page: 1};
      this.currentPage = 1;

      saveFilters(this.filterStorageKey, this.filterParams)

      await this.getIndex({page: this.currentPage, params: this.filterParams});
      
    },
    async filters(fieldFilters) {
      // this.filterField = [...fieldFilters];
      let params = {};
      for (let value of fieldFilters) {
        params[value.name] = value.value || null;
      }

      this.filterParams = {...this.filterParams, ...params, page: 1};

      saveFilters(this.filterStorageKey, this.filterParams)

      this.getIndex({page: this.currentPage, params: this.filterParams});

      await this.getCounter();
    },
  },
  watch: {
    // $route: 'getIndex',
  },
  data() {
    return {
      spinerCounter: false,
      filterStorageKey: 'order_filters',
      title: 'Все заказы',
      currentPage: 1,
      errorsModal: {},
      filterField: [
        {
          name: "number",
          value: "",
          type: "number"

        },
        {
          name: "order_status",
          value: null,
          type: "select",
          options: [],
        },
        {
          name: "order",
          value: "all",
          type: "select",

        },
      ],
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Все заказы',
          active: true,
        },
      ],
      filterParams: {},
    };
  },
};
</script>
<style>

</style>
