<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <Form :driver="driver" :errors="errors" type="Редактировать"
          @sumbit-driver="tryToEditDriver" :permissionTypes="driver_permissions" :is-create="false" v-if="!spiner"/>
    <div class="centered" v-else>
      <b-spinner></b-spinner>
    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {driversMethods, driversComputed} from "@state/helpers";
import Form from '@views/driver/form';

export default {
  computed: {
    ...driversComputed
  },
  name: "edit-driver",
  page: {
    title: 'Edit Driver',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader, Form},
  async created() {
    await this.getById(this.$route.params.id);
    this.driver_permissions = await this.loadPermissions();
    this.title += ` ${this.driver.name}`;
    this.items[2].title += ` ${this.driver.name}`;

  },
  data() {
    return {
      title: 'Редактирование',
      driver_permissions: [],
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
          text: 'Редактирование:',
          active: true,
        },
      ],
      errors: {},
    };
  },
  methods: {
    ...driversMethods,
    tryToEditDriver(form) {
      this.edit({
        ...form,
        id: this.driver.number,
      }).then(() => {
        this.$router.push(this.$route.query.redirectFrom ? this.$route.query.redirectFrom : {name: 'index-drivers'});
      }).catch((res) => {
        this.errors = res.response.data.errors;
      });
    }
  }
};
</script>

<style lang="scss" module></style>