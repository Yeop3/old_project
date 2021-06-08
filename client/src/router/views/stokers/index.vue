<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>
    <Table
        :stokers="stokers"
        @delete-by-id="tryToDeleteStoker"
        v-if="!spiner_stoker"
    >
    </Table>
    <div class="centered" v-else>
      <b-spinner class=""></b-spinner>
    </div>
  </Layout>
</template>

<script>
	import appConfig from "@src/app.config.json";
	import Layout from "@layouts/main";
	import PageHeader from "@components/page-header";
	import Table from "@views/stokers/partials/table";
	import {stokerComputed, stokerMethods} from "@state/helpers";

	export default {
		name: "index-stokers",
		computed: {
			...stokerComputed
		},
		page: {
			title: 'Ответственные',
			meta: [{name: 'description', content: appConfig.description}],
		},
		components: {Layout, PageHeader, Table},
		created() {
			this.loadStokers();
		},
		methods: {
			...stokerMethods,
		// 	async pagination(page) {
		// 		await this.getIndex({page:this.page});
		// 		this.$router.push({path: `/operators/?page=${page}`});
		// 	},
			tryToDeleteStoker(number) {
				this.deleteStoker(number)
					.then((res) => {
						this.loadStokers();
					}).catch((res) => {
					this.$bvToast.toast(res.response.data.message, {
						title: 'Errors',
						variant: 'danger',
						autoHideDelay: 5000,
					});
				});
			},
		// 	async filters(filterField){
		// 		let params = {};
		// 		for (let value of filterField) {
		// 			if (value.value)
		// 				params[value.name] = value.value;
		// 		}
		// 		await this.getIndex({page: this.page, params});
		// 	}
		},
		data() {
			return {
				title: 'Ответственные',
				items: [
					{
						text: 'Главная',
						to: '/',
					},
					{
						text: 'Ответственные',
						active: true,
					},
				],
        spiner: true,
			};
		},
	};
</script>

<style scoped>

</style>