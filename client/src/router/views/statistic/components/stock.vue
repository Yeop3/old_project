<template>
  <div>
    <b-tabs v-if="statistic">
      <b-tab @click="loadData(0)" title="Все товары">
        <b-table-simple v-if="!spiner" small responsive>
          <b-thead>
            <b-tr>
              <b-th :colspan="4">Все товары</b-th>
              <b-th class="text-right" v-for="productType in statistic.headers">
                {{productType.name}}<br>
                {{productType.packing}}<br>
                {{productType.products_count}}x{{productType.price}}={{productType.total}}
              </b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <b-tr v-for="location in statistic.rows">
              <b-th :colspan="4">
                {{location[0].location_name}}<br>
                {{location[0].total_count}} товаров на сумму
                {{location[0].total_sum}}
              </b-th>
              <b-td class="text-right" v-for="count in getCount(location)">{{count}}
              </b-td>
            </b-tr>
          </b-tbody>
        </b-table-simple>
        <div class="centered" v-else>
          <b-spinner></b-spinner>
        </div>
      </b-tab>
      <b-tab @click="loadData(1)" title="Продается">
        <template v-if="!spiner">
          <b-table-simple v-if="statusStatistic" small responsive>
            <b-thead>
              <b-tr>
                <b-th :colspan="4">Все товары</b-th>
                <b-th class="text-right"
                      v-for="productType in statusStatistic.headers">
                  {{productType.name}}<br>
                  {{productType.packing}}<br>
                  {{productType.products_count}}x{{productType.price}}={{productType.total}}
                </b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <b-tr v-for="location in statusStatistic.rows">
                <b-th :colspan="4">
                  {{location[0].location_name}}<br>
                  {{location[0].total_count}} товаров на сумму
                  {{location[0].total_sum}}
                </b-th>
                <b-td class="text-right" v-for="count in getCount(location)">
                  {{count}}
                </b-td>
              </b-tr>
            </b-tbody>
          </b-table-simple>
        </template>
        <div class="centered" v-else>
          <b-spinner></b-spinner>
        </div>
      </b-tab>
      <b-tab @click="loadData(2)" title="Не активен">
        <template v-if="!spiner">
          <b-table-simple v-if="statusStatistic" small responsive>
            <b-thead>
              <b-tr>
                <b-th :colspan="4">Все товары</b-th>
                <b-th class="text-right"
                      v-for="productType in statusStatistic.headers">
                  {{productType.name}}<br>
                  {{productType.packing}}<br>
                  {{productType.products_count}}x{{productType.price}}={{productType.total}}
                </b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <b-tr v-for="location in statusStatistic.rows">
                <b-th :colspan="4">
                  {{location[0].location_name}}<br>
                  {{location[0].total_count}} товаров на сумму
                  {{location[0].total_sum}}
                </b-th>
                <b-td class="text-right" v-for="count in getCount(location)">
                  {{count}}
                </b-td>
              </b-tr>
            </b-tbody>
          </b-table-simple>
        </template>
        <div class="centered" v-else>
          <b-spinner></b-spinner>
        </div>
      </b-tab>
      <b-tab @click="loadData(3)" title="Забронирован">
        <template v-if="!spiner">
          <b-table-simple v-if="statusStatistic" small responsive>
            <b-thead>
              <b-tr>
                <b-th :colspan="4">Все товары</b-th>
                <b-th class="text-right"
                      v-for="productType in statusStatistic.headers">
                  {{productType.name}}<br>
                  {{productType.packing}}<br>
                  {{productType.products_count}}x{{productType.price}}={{productType.total}}
                </b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <b-tr v-for="location in statusStatistic.rows">
                <b-th :colspan="4">
                  {{location[0].location_name}}<br>
                  {{location[0].total_count}} товаров на сумму
                  {{location[0].total_sum}}
                </b-th>
                <b-td class="text-right" v-for="count in getCount(location)">
                  {{count}}
                </b-td>
              </b-tr>
            </b-tbody>
          </b-table-simple>
        </template>
        <div class="centered" v-else>
          <b-spinner></b-spinner>
        </div>
      </b-tab>
      <b-tab @click="loadData(4)" title="Продан">
        <template v-if="!spiner">
          <b-table-simple v-if="statusStatistic" small responsive>
            <b-thead>
              <b-tr>
                <b-th :colspan="4">Все товары</b-th>
                <b-th class="text-right"
                      v-for="productType in statusStatistic.headers">
                  {{productType.name}}<br>
                  {{productType.packing}}<br>
                  {{productType.products_count}}x{{productType.price}}={{productType.total}}
                </b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <b-tr v-for="location in statusStatistic.rows">
                <b-th :colspan="4">
                  {{location[0].location_name}}<br>
                  {{location[0].total_count}} товаров на сумму
                  {{location[0].total_sum}}
                </b-th>
                <b-td class="text-right" v-for="count in getCount(location)">
                  {{count}}
                </b-td>
              </b-tr>
            </b-tbody>
          </b-table-simple>
        </template>
        <div class="centered" v-else>
          <b-spinner></b-spinner>
        </div>
      </b-tab>
    </b-tabs>
  </div>
</template>

<script>
import {
  statisticComputed,
  statisticMethods,
  productMethods,
  locationsComputed,
  productComputed,
  driversComputed,
  productTypesComputed,
  botsComputed
} from "@state/helpers";
import LineChart from "../LineChart";

export default {
  name: "statistic.sells",
  components: {LineChart},
  data() {
    return {
      options: {
        responsive: true,
        maintainAspectRation: false
      },
      filterFields: [
        {
          name: 'location',
          value: '',
          type: 'select',
        },
        {
          name: 'driver',
          value: '',
          type: 'select',
        },
        {
          name: 'bot',
          value: '',
          type: 'select',
        },
        {
          name: 'product_type',
          value: '',
          type: 'select',
        },
        {
          name: "date_start",
          value: "",
          type: "date"

        },
        {
          name: "date_end",
          value: "",
          type: "date"
        }
      ],
      datacollection: {},
      showDays: 1,
    };
  },
  watch: {
    chartData() {
      this.$data._chart.update()
    }
  },
  computed: {
    ...statisticComputed,
    ...locationsComputed,
    ...productComputed,
    ...driversComputed,
    ...productTypesComputed,
    ...botsComputed
  },
  mounted() {
    this.loadStatistic();
  },
  methods: {
    ...statisticMethods,
    ...productMethods,
    getCount(items) {
      var count = [];
      for (var i = 1; i < items.length; i++) {
        count.push(items[i]);
      }
      return count;
    },
    loadData(status) {
      if (status === 0) {
        return this.loadStatistic();
      } else {
        return this.loadStatusStatistic(status);
      }
    },
  },
};
</script>