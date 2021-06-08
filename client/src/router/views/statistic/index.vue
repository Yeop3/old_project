<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box">
          <div class="card mt-2">
            <div class="card-body">
              <b-tabs>
                <b-tab title="Продажи">
                  <sells/>
                </b-tab>
                <b-tab title="Наличие">
                  <stocks/>
                </b-tab>
              </b-tabs>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
	import appConfig from '@src/app.config';
	import Layout from '@layouts/main';
	import PageHeader from '@components/page-header';
	import Sells from "./components/sells";
	import Stocks from "./components/stock";

	export default {
		name: "statistic.index",
		page: {
			title: 'Статистика',
			meta: [{name: 'description', content: appConfig.description}],
		},
		components: {Layout, PageHeader, Sells, Stocks},
		data() {
			return {
				title: 'Статистика',
				items: [
					{
						text: 'Главная',
						to: '/',
					},
					{
						text: 'Статистика',
						active: true,
					},
				],
			};
		},
		watch: {
			chartData() {
				this.$data._chart.update()
			}
		},
		computed: {
		},
		mounted() {
		},
		methods: {
		},
	};
</script>

<style>
</style>