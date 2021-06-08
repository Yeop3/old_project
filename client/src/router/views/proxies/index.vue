<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box">
          <div class="card mt-2">
            <div class="card-body">

              <b-overlay :show="spiner" rounded="sm" no-center>
                <b-button class="mb-2" size="xs" @click="$router.push({name: 'proxies.create'})"
                          variant="primary">
                  Добавить
                </b-button>
                <template v-slot:overlay>
                  <div class="centered">
                    <b-spinner variant="secondary"></b-spinner>
                  </div>
                </template>
                <b-table
                    :items="proxies"
                    :fields="fields"
                    class="text-center"
                    responsive
                    no-local-sorting
                    @sort-changed="sort"
                    sortDirection="desc"
                >

                  <template v-slot:top-row="columns">
                    <td></td>
                    <td>
                      <b-select
                          :name="filterField.find((value) => value.name === 'type').name"
                          :options="filterField.find((value) => value.name === 'type').options"
                          v-model="filterField.find((value) => value.name === 'type').value"
                          @change="filters"
                          size="sm"
                      >

                      </b-select>
                    </td>
                    <td>
                      <b-form-input
                          :type="filterField.find((value) => value.name === 'ip').type"
                          :name="filterField.find((value) => value.name === 'ip').name"
                          v-model="filterField.find((value) => value.name === 'ip').value"
                          @blur="filters"
                          size="sm"
                      >
                      </b-form-input>
                    </td>
                    <td>
                      <b-form-input
                          :type="filterField.find((value) => value.name === 'port').type"
                          :name="filterField.find((value) => value.name === 'port').name"
                          v-model="filterField.find((value) => value.name === 'port').value"
                          @blur="filters"
                          size="sm"
                      >
                      </b-form-input>
                    </td>
                  </template>

                  <template v-slot:cell(is_working)="row">
                    <b-badge v-if="row.item.is_working === 0" variant="danger">
                      Не работает
                    </b-badge>
                    <b-badge v-else variant="success">
                      Работает
                    </b-badge>
                  </template>

                  <template v-slot:cell(actions)="row">
                    <div class="btn-group">
                      <b-button v-b-tooltip="'Посмотреть'" @click="$router.push({name: 'proxies.show', params:{
                                            number: row.item.number,
                                        }})" size="xs" class="btn-primary">
                        <b-icon icon="eye"></b-icon>
                      </b-button>
                      <b-button v-b-tooltip="'Изменить'" @click="$router.push({name: 'proxies.edit', params:{
                                            number: row.item.number,
                                        }})" size="xs" class="btn-warning">
                        <b-icon icon="pencil"></b-icon>
                      </b-button>
                      <b-button v-b-tooltip="'Удалить'" @click="tryToDeleteProxy(row.item.number)"
                                size="xs" class="btn-danger">
                        <b-icon icon="trash"></b-icon>
                      </b-button>
                    </div>
                  </template>
                </b-table>
              </b-overlay>
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
	import {proxyMethods, proxyComputed} from "@state/helpers";
	import {BIcon, BIconEye, BIconPencil, BIconTrash} from "bootstrap-vue";

	export default {
		name: "proxies.index",
		page: {
			title: 'Прокси',
			meta: [{name: 'description', content: appConfig.description}],
		},
		components: {Layout, PageHeader, BIcon, BIconTrash, BIconPencil, BIconEye},
		data() {
			return {
				title: 'Прокси',
				items: [
					{
						text: 'Главная',
						to: '/',
					},
					{
						text: 'Прокси',
						active: true,
					},
				],
				filterField: [
					{
						name: "type",
						value: null,
						type: "select",
						options: [
							{text: "Все", value: null},
							{text: "SOCKS5", value: "socks5"},
						],
					},
					{
						name: "ip",
						value: "",
						type: "text"

					},
					{
						name: "port",
						value: "",
						type: "text"

					},
				],
				fields: [
					{
						key: 'number',
						label: 'ID',
					},
					{
						key: 'proxy_type',
						label: 'Тип',
						sortable: true,
					},
					{
						key: 'ip',
						label: 'IP/Host',
						sortable: true,
					},
					{
						key: 'port',
						label: 'Порт',
						sortable: true,
					},
					{
						key: 'note',
						label: 'Заметка',
						sortable: true,
					},
					{
						key: 'auth',
						label: 'Авторизация',
					},
					{
						key: 'country',
						label: 'Страна',
					},
					{
						key: 'is_working',
						label: 'Состояние'
					},
					{
						key: 'actions',
						label: 'Действия',
					},
				],
			};
		},
		computed: {
			...proxyComputed,
		},
		mounted() {
			this.loadProxies();
		},
		methods: {
			...proxyMethods,
			async tryToDeleteProxy(number) {
				try {
					const value = await this.$bvModal.msgBoxConfirm('Действительны ли вы хотите это удалить?', {
						title: 'Пожалуйста подтвердите',
						size: 'sm',
						buttonSize: 'sm',
						okVariant: 'danger',
						okTitle: 'Удалить',
						cancelTitle: 'Отмена',
						footerClass: 'p-2',
						hideHeaderClose: false,
						centered: true
					});
					if (value) {
						this.deleteProxy(number).then((res) => {
							this.loadProxies();
						});
					}
				} catch (e) {
				}
			},
			async filters() {
				let params = {...this.$route.query};
				for (let value of this.filterField) {
					if (value.value)
						params[value.name] = value.value;
				}
				await this.loadProxies(params);
			},
			async sort(sortProduct) {
				console.log(sortProduct);
				let params = {...this.$route.query};

				params['sortDirection'] = sortProduct.sortDesc === true ? 'desc' : 'asc';
				params['sortField'] = sortProduct.sortBy;

				await this.loadProxies(params);
			},
		}
	};
</script>

<style scoped>

</style>