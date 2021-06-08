<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <Form :location="location"
          :errors="errors"
          :locations-select="locations_select"
          :drivers-select="drivers_select"
          type="Обновить"
          @sumbit-location="tryToEditLocation"
          :is-edit="true"
          v-if="!spiner_form"
    />
    <div class="centered" v-else>
      <b-spinner></b-spinner>
    </div>
  </Layout>
</template>
<script>
import PageHeader from "@components/page-header";
import Layout from "@layouts/main";
import Form from "@views/locations/form";
import appConfig from "@src/app.config.json";
import {driversComputed, driversMethods, locationsComputed, locationsMethods} from "@state/helpers";

export default {
  name: "locations-edit",
  components: {PageHeader, Layout, Form},
  page: {
    title: 'Редактирование локации',
    meta: [{name: 'description', content: appConfig.description}],

  },
  computed: {
    ...locationsComputed,
    ...driversComputed
  },
  mounted() {
  	this.spiner_form = true;
  	this.getById(this.$route.params.id)
      .finally(()=>this.spiner_form = false);
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
          text: 'Локации товаров',
          to: '/locations',
        },
        {
          text: 'Редактирование',
          active: true,
        },
      ],
      errors: {},
			spiner_form: false
    };
  },
  methods: {
    ...locationsMethods,
    tryToEditLocation(field) {
      this.edit(field).then((res) => {
        this.$router.push(this.$route.query.redirectFrom || {name: 'index-location'});
      }).catch((res) => {
        this.errors = res.response.data.errors;
        this.$bvToast.toast(res.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      });
    }
  }
};
</script>

<style scoped>

</style>