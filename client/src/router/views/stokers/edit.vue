<template>

  <Layout>
    <PageHeader :title="title" :items="items"/>
    <Form :stoker="stoker"
          :errors="errors"
          :locations-select="locations_select"
          :product-types-select="product_types_select"
          :clients-select="clients_for_select"
          :bots-select="select_bots"
          type="Обновить"
          @submit-form="tryToUpdateStoker"
          v-if="!loading"
          :submitting="submitting"
    />
    <div class="centered" v-else>
      <b-spinner></b-spinner>
    </div>

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
		stokerMethods,
		stokerComputed
	} from "@state/helpers";

	export default {
		name: "edit-stoker",
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
      ...stokerComputed,
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
				loading: true,
			};
		},
		mounted() {
			this.loadStoker(this.$route.params.number)
        .finally(()=>this.loading = false)
			this.loadClientsForSelect();
		},
		methods: {
			...stokerMethods,
			...clientsMethods,
			tryToUpdateStoker(field) {
				this.submitting = true;
				this.updateStoker(field).then((res) => {
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