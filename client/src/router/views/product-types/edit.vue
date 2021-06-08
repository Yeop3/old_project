<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <Form :productType="product_type" :errors="errors" type="Сохранить" @sumbit-product-type="tryToEditProductType"
          v-if="!spiner"/>
    <div class="centered" v-else>
      <b-spinner></b-spinner>
    </div>

  </Layout>
</template>

<script>
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {productTypesComputed, productTypesMethods} from '@state/helpers';
import Form from '@views/product-types/form';

export default {
  name: "EditProductType",
  components: {PageHeader, Layout, Form},
  computed: {
    ...productTypesComputed,
  },
  page: {
    title: 'Редактирование товара',
    meta: [{name: 'description', content: appConfig.description}],
  },
  async created() {
    await this.getById(this.$route.params.id);
    this.items[2].text += this.product_type.name;
    this.items[2].to += this.product_type.number.toString();
  },
  data() {
    return {
      title: 'Редактирование',
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
          to: '/product-types/show/',
        },
        {
          text: 'Редактирование',
          active: true,
        },
      ],
      errors: {}
    };
  },
  methods: {
    ...productTypesMethods,
    tryToEditProductType(field) {

      this.edit({field, id: this.product_type.number}).then((res) => {
        this.$router.push(this.$route.query.redirectFrom ?
            this.$route.query.redirectFrom :
            {name: 'index-product-types'}
        );
      }).catch((res) => {
        this.errors = res.response.data.errors;
      });
    }
  }
};
</script>

<style lang="scss" module></style>