<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <Form :is-create="true" :errors="errors" type="Создать" @sumbit-driver="tryToCreateDriver"/>

  </Layout>
</template>

<style lang="scss" module></style>

<script>
import appConfig from '@src/app.config'
import Layout from '@layouts/main'
import PageHeader from '@components/page-header'
import {driversMethods} from '@state/helpers';
import Form from '@views/driver/form'

export default {
  name: "create-driver",
  page: {
    title: 'Создание Курьера',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader, Form},
  data() {
    return {
      title: 'Добавить курьера',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Курьеры',
          to: '/drivers',
        },
        {
          text: 'Создание',
          active: true,
        },
      ],
      name: '',
      errors: {}
    }
  },
  methods: {
    ...driversMethods,
    tryToCreateDriver(form) {
      this.create(form)
        .then(() => {
          this.$router.push(this.$route.query.redirectFrom ? this.$route.query.redirectFrom : {name: 'index-drivers'})
        }).catch((res) => {
          this.errors = res.response.data.errors;
        });
    }
  }
}
</script>