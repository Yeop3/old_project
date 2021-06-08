<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box row justify-content-center">
          <b-table
              stacked
              :items="[product_type]"
              :fields="fields"
              :busy.sync="spiner"
          >
            <template v-slot:table-busy>
              <div class="d-flex justify-content-center mb-3">
                <b-spinner></b-spinner>
              </div>
            </template>
            <template v-slot:cell(commission)="data">
              {{ data.item.commission_value }} {{
                commission_types.find((value) => data.item.commission_type
                    === value.value).text
              }}
            </template>
            <template v-slot:cell(packing)="data">
              {{ data.item.packing }} {{
                unit_types.find((value) => data.item.unit
                    === value.value).text
              }}
            </template>
            <template v-slot:cell(payment_methods)="data">
              <b-form-group disabled>
                <b-form-checkbox-group
                    v-if="product_type"
                    v-model="product_type.payment_methods"
                    :options="paymentMethods"
                ></b-form-checkbox-group>
              </b-form-group>
            </template>
          </b-table>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {productTypesComputed, commissionComputed, unitComputed, productTypesMethods, paymentMethodComputed, paymentMethods as paymentStoreMethods} from '@state/helpers';

export default {
  name: "ShowProductType",
  components: {PageHeader, Layout},
  computed: {
    ...productTypesComputed,
    ...commissionComputed,
    ...unitComputed,
    ...paymentMethodComputed,
  },
  page: {
    title: 'Просмотр ',
    meta: [{name: 'description', content: appConfig.description}],
  },
  async created() {
    this.loadPaymentMethods();
    await this.getById(this.$route.params.id)
    this.title += this.product_type.name;
    this.items[2].text += this.product_type.name;
    this.fields.push({...this.product_type});
  },
  data() {
    return {
      fields: [
        {
          key: 'number',
          label: 'ID',
        },
        {
          key: 'name',
          label: 'Имя',
        },
        {
          key: 'price',
          label: 'Цена',
        },
        {
          key: 'commission',
          label: 'Комиссия'
        },
        {
          key: 'packing',
          label: 'Количество'
        },
        {
          key: 'payment_methods',
          label: 'Методы оплаты'
        },
      ],
      title: 'Просмотр ',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Товары',
          to: '/product-types',
        },
        {
          text: 'Товар: ',
          active: true,
        },
      ],
    };
  },
  methods: {
    ...productTypesMethods,
    ...paymentStoreMethods,
  },
};
</script>

<style lang="scss" module></style>