<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>
    <div class="row">
      <div class="col-lg-12">
        <div class="card-box" v-if="!spiner">
          <b-button variant="success" class="mb-2" @click="exportExcelById()">
            <b-icon-download></b-icon-download>
            Excel отчет
          </b-button>
          <b-table
              small
              :fields="field_product_info"
              :items="info.productInfo"
          >

            <template v-slot:cell(sold)="data">
              {{ data.item.paid + data.item.given_operator }}
            </template>

            <template v-slot:cell(total)="data">
              {{ data.item.paid + data.item.given_operator + data.item.relocation }}
            </template>

            <template v-slot:custom-foot>
              <b-th>Всего:</b-th>
              <b-th>{{ soldTotal }}</b-th>
              <b-th>{{ relocationTotal }}</b-th>
              <b-th>{{ Total }}</b-th>

            </template>

          </b-table>
          <b-table
              small
              :items="info.productInfo"
              :fields="field_product_sold_info"

          >
            <template v-slot:cell(productTotal)="data">
              {{ data.item.paid + data.item.given_operator + data.item.relocation }}
            </template>

            <template v-slot:cell(realPacking)="data">
              {{ data.item.real_packing * (data.item.paid + data.item.given_operator + data.item.relocation) }}
            </template>

            <template v-slot:cell(sold)="data">
              {{ data.item.paid + data.item.given_operator }}
            </template>

            <template v-slot:cell(total)="data">
              {{ data.item.paid + data.item.given_operator + data.item.relocation }}
            </template>

          </b-table>
          <b-row>
            <b-col lg="7" md="6">
              <b-table
                  stacked
                  small
                  :fields="field_operator_info"
                  :items="[info.shiftInfo]"

              >
                <template v-slot:cell(duration)="data">
                  <span v-if="duration.years !== 0">{{ duration.years }} лет, </span>
                  <span v-if="duration.years !== 0">{{ duration.months }} месяцев, </span>
                  <span v-if="duration.days !== 0">{{ duration.days }} дней, </span>
                  <span v-if="duration.hours !== 0">{{ duration.hours }} часов, </span>
                  <span v-if="duration.minutes !== 0">{{ duration.minutes }} минут, </span>
                  <span v-if="duration.seconds !== 0">{{ duration.seconds }} секунд</span>
                </template>

                <template v-slot:cell(ended_at)="data">
                  <span v-if="data.item.ended_at === null">Смена еще не закончилась</span>
                  <span v-else>{{ data.item.ended_at }}</span>
                </template>

                <template v-slot:cell(orders_count)="data">
                  {{ data.item.orders.length }}
                </template>
              </b-table>
            </b-col>
            <b-col lg="5" md="6">
              <b-table
                  stacked
                  :fields="field_orders_count"
                  :items="[info.ordersCount]"

              >
              </b-table>
            </b-col>
          </b-row>
        </div> <!-- end card-box -->
        <div class="d-flex justify-content-center mb-3" v-else>
          <b-spinner></b-spinner>
        </div>
      </div> <!-- end col -->
    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {shiftsComputed, shiftsMethods} from "@state/helpers";
import moment from "moment";
import {BIcon, BIconDownload} from "bootstrap-vue"

export default {
  name: "show-shifts",
  components: {PageHeader, Layout, BIcon, BIconDownload},
  page: {
    title: 'Просмотр ',
    meta: [{name: 'description', content: appConfig.description}],
  },
  data() {
    return {
      title: 'Просмотр ',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Смены',
          to: '/shifts',
        },
        {
          text: 'Отчет за смену',
          active: true,
        },
      ],
      field_operator_info: [
        {
          key: 'operator.name',
          label: 'Оператор смены',
        },
        {
          key: 'duration',
          label: 'Продолжительность',
        },
        {
          key: 'started_at',
          label: 'Начало смены',
        },
        {
          key: 'ended_at',
          label: 'Конец смены',
        },
        {
          key: 'orders_count',
          label: 'Всего заказов',
        },
        {
          key: 'payment_qiwi',
          label: 'Ручных Qiwi-Оплат',
        },
      ],
      field_orders_count: [
        {
          key: 'awaiting_payment',
          label: 'Заказы: Ожидает оплаты',
        },
        {
          key: 'partially_paid',
          label: 'Заказы: Частично оплачен',
        },
        {
          key: 'paid',
          label: 'Заказы: Оплачен',
        },
        {
          key: 'canceled_by_client',
          label: 'Заказы: Отменен клиентом',
        },
        {
          key: 'canceled_by_timeout',
          label: 'Заказы: Отменен по таймауту',
        },
        {
          key: 'canceled_by_system',
          label: 'Заказы: Отменен системой',
        },
        {
          key: 'canceled_by_operator',
          label: 'Заказы: Отменен оператором',
        },
        {
          key: 'given_operator',
          label: 'Заказы: Отдан оператором',
        },
        {
          key: 'relocation',
          label: 'Заказы: Переклад',
        },
      ],
      field_product_info: [
        {
          key: 'name',
          label: '',
        },
        {
          key: 'sold',
          label: 'Продано, шт',
        },
        {
          key: 'relocation',
          label: 'Переклад, шт',
        },
        {
          key: 'total',
          label: 'Всего, шт',
        },
      ],
      field_product_sold_info: [
        {
          key: 'name',
          label: '',
        },
        {
          key: 'productTotal',
          label: 'Общее кол-во',
        },
        {
          key: 'realPacking',
          label: 'Общее реальное кол-во',
        },
        {
          key: 'sold',
          label: 'Продано, шт',
        },
        {
          key: 'relocation',
          label: 'Переклад, шт',
        },
        {
          key: 'total',
          label: 'Всего, шт',
        },
      ],
      duration: {},
      info: [],
    };
  },
  computed: {
    ...shiftsComputed,
    soldTotal() {
      return this.info.productInfo.reduce((accum, item) => {
        return accum + item.paid + item.given_operator
      }, 0)
    },
    relocationTotal() {
      return this.info.productInfo.reduce((accum, item) => {
        return accum + item.relocation
      }, 0)
    },
    Total() {
      return this.info.productInfo.reduce((accum, item) => {
        return accum + item.paid + item.given_operator + item.relocation
      }, 0)
    }
  },
  mounted() {
    this.loadShift(this.$route.params.number).then((res) => {
      console.log(this.shift);
      this.info = Object.assign([], this.shift);
      if (this.info.shiftInfo.ended_at === null) {
        this.duration = moment.duration(moment() - moment(this.info.shiftInfo.started_at))._data;
      } else {
        this.duration = moment.duration(moment(this.info.shiftInfo.ended_at) - moment(this.info.shiftInfo.started_at))._data;
      }

      this.info.shiftInfo.started_at = moment(this.info.shiftInfo.started_at).locale('ru').format('L LTS',);
      if (this.info.shiftInfo.ended_at !== null) {
        this.info.shiftInfo.ended_at = moment(this.info.shiftInfo.ended_at).locale('ru').format('L LTS');
      }
    });
  },
  methods: {
    ...shiftsMethods,
    async exportExcelById() {
      await this.exportExcelByID(this.shift.number);
    }
  }
};
</script>

<style scoped>

</style>