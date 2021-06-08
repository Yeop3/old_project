<template>

  <Layout>
    <PageHeader :title="title" :items="items"/>
    <Form :errors="errors"
          :locations-select="locations_select"
          :product-types-select="product_types_select"
          :clients-select="clients_for_select"
          :bots-select="select_bots"
          type="Создать"
          @submit-form="tryToCreateStoker"
          :submitting="submitting"
    />

  </Layout>
  
</template>

<script>
	import appConfig from '@src/app.config';
	import Layout from '@layouts/main';
	import PageHeader from '@components/page-header';
  import Form from '@views/stokers/partials/form';
	import {
		clientsComputed,
    locationsComputed,
    productTypesComputed,
    botsComputed,
    clientsMethods,
    stokerMethods
	} from "@state/helpers";

	export default {
		name: "create-stoker",
		components: {PageHeader, Layout, Form},
		page: {
			title: 'Создание ответственного',
			meta: [{name: 'description', content: appConfig.description}],
		},
    computed: {
			...locationsComputed,
			...productTypesComputed,
      ...clientsComputed,
      ...botsComputed,
    },
		data() {
			return {
				title: 'Создание',
				items: [
					{
						text: 'Главная',
						to: '/',
					},
					{
						text: 'Ответственные',
						to: '/stokers',
					},
					{
						text: 'Создание',
						active: true,
					},
				],
				errors: {},
				submitting: false,
			};
		},
    mounted() {
			this.loadClientsForSelect();
		},
		methods: {
			...stokerMethods,
			...clientsMethods,
			tryToCreateStoker(field) {
				this.submitting = true;
				this.createStoker(field).then((res) => {
					this.$router.push(this.$route.query.redirectFrom || {name: 'index-stokers'});
				}).catch((res) => {
					this.errors = res.response.data.errors || {};
					this.$bvToast.toast(res.response.data.message, {
						title: 'Errors',
						variant: 'danger',
						autoHideDelay: 5000,
					});
				}).finally(() => {
					this.submitting = false;
				});
			}
    }
	}
</script>

<style scoped>

</style>