<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box" v-if="!spiner">
          <div class="card mt-2">
            <div v-if="stoker" class="card-body">
              <h1 class="mb-3"></h1>
              <b-button
                  class="mr-2"
                  @click="$router.push({name: 'edit-stokers', params: {number: stoker.number}})"
                  variant="primary"
                  size="xs"
              >
                <b-icon icon="pencil"></b-icon>
                Изменить
              </b-button>
              <b-button
                  class="btn"
                  @click="tryToDeleteStoker(stoker.number)"
                  variant="danger"
                  size="xs"
              >
                <b-icon icon="trash"></b-icon>
                Удалить
              </b-button>
              <b-table
                  class="mt-3"
                  stacked
                  :items="[stoker]"
                  :fields="fields"
              >
                <template v-slot:cell(client)="data">
                  <template>
                    <router-link
                        tag="a"
                        :to="`/clients/${ data.item.client.number }`"
                    >
                      <template>
                        @{{ data.item.client.username }}
                      </template>
                    </router-link>
                    <br>
                    <template>
                      {{ data.item.client.name }}
                    </template>
                    <br>
                    Телеграм ID: {{ data.item.client.telegram_id }}
                    <br>
                  </template>
                </template>

                <template v-slot:cell(bot)="data">
                  <router-link
                      :to="`/bots/card/${data.item.source.number}`"
                      tag="a"
                      class="text-primary"
                  >
                    {{data.item.source.name}}
                  </router-link>
                </template>

                <template v-slot:cell(location)="data">
                  <router-link
                      :to="`/locations/show/${data.item.location.number}`"
                      tag="a"
                      class="text-primary"
                  >
                    {{data.item.location.name_chain}}
                  </router-link>
                </template>

                <template v-slot:cell(product_type)="data">
                  <router-link
                      :to="`/product-types/show/${data.item.product_type.number}`"
                      tag="a"
                      class="text-primary"
                  >
                    {{data.item.product_type.name}}
                  </router-link>
                </template>
              </b-table>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-center mb-3" v-else>
          <b-spinner></b-spinner>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
	import appConfig from '@src/app.config'
	import Layout from '@layouts/main'
	import PageHeader from '@components/page-header'
	import {stokerComputed, stokerMethods} from "@state/helpers";
	import {BIcon, BIconPencil, BIconTrash} from "bootstrap-vue";

	export default {
		name: "show-stokers",
		page: {
			title: 'Ответственный',
			meta: [{name: 'description', content: appConfig.description}],
		},
		components: {
			Layout,
      PageHeader,
			BIcon,
			BIconPencil,
			BIconTrash
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
						to: '/stokers',
					},
					{
						text: 'Карточка ответственного',
						active: true,
					}
				],
				fields: [
					{
						key: 'client',
						label: 'Клиент',
					},
					{
						key: 'bot',
						label: 'Бот',
					},
					{
						key: 'location',
						label: 'Локация',
					},
					{
						key: 'product_type',
						label: 'Продукт',
					},
				],
        spiner: true,
			}
		},
		computed:{
			...stokerComputed,
		},
		mounted() {
			this.loadStoker(this.$route.params.number)
        .finally(()=> this.spiner = false);
		},
		methods:{
			...stokerMethods,
			async tryToDeleteStoker(number) {
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
						this.deleteStoker(number).then((res) => {
							this.$router.push({name: 'proxies.index'});
						});
					}
				} catch (e) {
				}
			},
		}
	};
</script>

<style>
</style>