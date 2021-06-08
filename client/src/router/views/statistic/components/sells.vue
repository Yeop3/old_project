<template>
  <div>
    <b-row>
      <b-col lg="3" md="6">
        <b-form-datepicker
            id="date_start"
            @input="filters"
            label-reset-button="Сбросить"
            label-no-date-selected="Дата от не выбрана"
            reset-button
            placeholder="Дата от"
            v-model="filterFields.find((value) => value.name === 'date_start').value"
            size="sm"
            :date-format-options="{
                            'year': 'numeric',
                            'month': 'numeric',
                            'day': 'numeric',
                          }"
        >
        </b-form-datepicker>
      </b-col>
      <b-col class="mb-2" lg="3" md="6">
        <b-form-datepicker
            id="date_end"
            label-reset-button="Сбросить"
            reset-button
            label-no-date-selected="Дата до не выбрана"
            placeholder="Дата до"
            @input="filters"
            v-model="filterFields.find((value) => value.name === 'date_end').value"
            size="sm"
            :date-format-options="{
                            'year': 'numeric',
                            'month': 'numeric',
                            'day': 'numeric',
                          }"
        >
        </b-form-datepicker>
      </b-col>
      <b-col>
        <b-button size="sm" @click="resetFilters">Сбросить фильтры</b-button>
      </b-col>
    </b-row>
    <b-select
        class="col-lg-3 mb-1"
        size="sm"
        :options="filterFields.find((value) => value.name === 'location').options"
        v-model="filterFields.find((value) => value.name === 'location').value"
        @change="filters"
    >

    </b-select>
    <b-select
        class="col-lg-3 mb-1"
        size="sm"
        :options="filterFields.find((value) => value.name === 'driver').options"
        v-model="filterFields.find((value) => value.name === 'driver').value"
        @change="filters"
    >

    </b-select>
    <b-select
        class="col-lg-3 mb-1"
        size="sm"
        :options="filterFields.find((value) => value.name === 'product_type').options"
        v-model="filterFields.find((value) => value.name === 'product_type').value"
        @change="filters"
    >

    </b-select>
    <b-select
        class="col-lg-3 mb-1"
        size="sm"
        :options="filterFields.find((value) => value.name === 'bot').options"
        v-model="filterFields.find((value) => value.name === 'bot').value"
        @change="filters"
    >

    </b-select>

    <!--                  <b-form-checkbox-->
    <!--                      v-model="showDays"-->
    <!--                      name="checkbox-days"-->
    <!--                      @input="handleShowDays"-->
    <!--                      :value="1"-->
    <!--                      :unchecked-value="0"-->
    <!--                  >-->
    <!--                    Отображать дни-->
    <!--                  </b-form-checkbox>-->

    <line-chart
        :chart-data="datacollection"
        :options="options"
        :height="650"
        :width="1500"
    />
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
  botsComputed,
  getFilters,
  saveFilters
} from "@state/helpers";
import LineChart from "../LineChart";

export default {
  name: "statistic.sells",
  components: {LineChart},
  data() {
    return {
      filterParams: {},
      filterStorageKey: 'statistic_sell_filters',
      options: {
        responsive: true,
        maintainAspectRation: false
      },
      filterFields: [
        {
          name: 'location',
          value: null,
          type: 'select',
        },
        {
          name: 'driver',
          value: null,
          type: 'select',
        },
        {
          name: 'bot',
          value: null,
          type: 'select',
        },
        {
          name: 'product_type',
          value: null,
          type: 'select',
        },
        {
          name: "date_start",
          value: null,
          type: "date"

        },
        {
          name: "date_end",
          value: null,
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
  async created() {

    this.filterParams = getFilters(this.filterStorageKey)

    this.filterFields = this.filterFields.map((value) => {
      switch (value.name) {
        case "bot":
          value.options = this.select_bots;
          break;
        case "location":
          value.options = this.locations_select;
          break;
        case "product_type":
          value.options = this.product_types_select;
          break;
        case "driver":
          value.options = this.drivers_select;
          break;
        case 'date_start':
          break;
        case 'date_end':
          break;
      }
      return value;
    });

    await this.chartStatistic({show_days: this.showDays, params: this.filterParams})
    this.loadChartStatistic()
    this.initValueFilters()
  },
  methods: {
    ...statisticMethods,
    ...productMethods,
    loadChartStatistic() {
      this.datacollection = {
        labels: [],
        datasets: [
          {
            label: 'Всего',
            backgroundColor: 'rgba(54, 162, 235, 0)',
            data: [],
            borderWidth: 4,
            borderColor: 'rgba(255, 99, 132, 1)',
          }
        ]
      };

      const fixDateItem = item => item > 9 ? item : `0${item}`;

      for (var i = 0; i < this.chart.length; i++) {
        if (this.showDays){
          this.datacollection.labels.push(fixDateItem(this.chart[i].day) + "." + fixDateItem(this.chart[i].month) + "." + this.chart[i].year);
        } else {
          this.datacollection.labels.push(fixDateItem(this.chart[i].month) + "." + this.chart[i].year);
        }

        this.datacollection.datasets[0].data.push(this.chart[i].count);
      }
    },
    async filters() {
      let params = {};
      for (let value of this.filterFields) {
        if (value.value) {
          params[value.name] = value.value;
        }
      }
      params.show_days = this.showDays;

      saveFilters(this.filterStorageKey, params)

      await this.chartStatistic({params})
      this.loadChartStatistic()
    },
    handleShowDays() {
      this.filters();
    },
    initValueFilters() {
      this.filterFields.forEach(field => {
        const filterValue = this.filterParams[field.name]
        if (filterValue) {
          field.value = filterValue
        }
      })
    },
    resetFilters() {
      this.filterFields.forEach(f => f.value = null)
      this.filters()
    }
  },
};
</script>