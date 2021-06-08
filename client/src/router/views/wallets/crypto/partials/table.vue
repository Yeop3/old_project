<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="card-box">
        <h4 class="header-title">
          <router-link class="btn btn-primary btn-xs mb-2" tag="a" to="/crypto-wallet/create">Добавить
          </router-link>
        </h4>
        <b-overlay :show="spiner" rounded="sm" no-center>
          <template v-slot:overlay>
            <div class="centered">
              <b-spinner variant="secondary"></b-spinner>
            </div>
          </template>
          <div class="table-responsive">
            <b-table
                id="drivers-table"
                :items="crypto.data"
                :fields="fields"
                small
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                sortDirection="desc"
            >
              <template v-slot:top-row="columns">
                <td></td>
                <td>
                  <b-form-input
                      :name="filterField.find((value) => value.name === 'name').name"
                      :type="filterField.find((value) => value.name === 'name').type"
                      v-model="filterField.find((value) => value.name === 'name').value"
                      @blur="filterCol"
                      size="sm"
                  ></b-form-input>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                  <b-form-input
                      :name="filterField.find((value) => value.name === 'comment').name"
                      :type="filterField.find((value) => value.name === 'comment').type"
                      v-model="filterField.find((value) => value.name === 'comment').value"
                      @blur="filterCol"
                      size="sm"
                  ></b-form-input>
                </td>
                <td></td>
                <td></td>
              </template>
              <template v-slot:cell(created_at)="data">
                <template v-if="data.item.created_at">
                  {{ getHumanDate(data.item.created_at) }}
                </template>
              </template>
              <template v-slot:cell(proxy)="data">
                <div>
                  <template v-if="data.item.proxy">
                    {{data.item.proxy.ip}}:{{data.item.proxy.port}} - {{data.item.proxy.username}}
                  </template>
                </div>
                <template v-if="data.item.proxy">
                  <b-badge v-if="data.item.proxy.is_working === 0" variant="danger">
                    Не работает
                  </b-badge>
                  <b-badge v-else variant="success">
                    Работает
                  </b-badge>
                </template>
              </template>

              <template v-slot:cell(actions)="data">
                <div class="btn-group">
                  <router-link class="btn btn-primary btn-xs" tag="a"
                               :to="`/crypto-wallet/show/${data.item.number}`">
                    <b-icon icon="eye"></b-icon>
                  </router-link>
                  <router-link class="btn btn-warning btn-xs" tag="a"
                               :to="`/crypto-wallet/edit/${data.item.number}`">
                    <b-icon icon="pencil"></b-icon>
                  </router-link>
                  <b-button
                      class="btn"
                      variant="danger"
                      size="xs"
                      @click="deleteCrypto(data.item.number)"
                      :disabled="data.item.status === 3"
                  >
                    <b-icon icon="trash"></b-icon>
                  </b-button>
                </div>
              </template>
            </b-table>
          </div> <!-- end table-responsive-->
        </b-overlay>
      </div> <!-- end card-box -->
    </div> <!-- end col -->
  </div>
</template>

<script>
	import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';
	import { getHumanDate } from "@state/helpers";

	export default {
		name: "table-crypto",
		props: {
			crypto: {
				type: Object,
			},
			spiner: {
				type: Boolean,
			}
		},
		components: {BIcon, BIconTrash, BIconPencil, BIconEye},
		created() {

		},
		methods: {
			filterCol() {
				this.filterField.forEach((value) => {
					value.value = value.value.trim();
				});
				this.$emit('filter', this.filterField);
			},
			deleteCrypto(id){
				this.$emit('delete-crypto', id);
			}
		},
		data() {
			return {
				getHumanDate,
				sortBy: "number",
				sortDesc: true,
				fields: [
					{
						key: 'number',
						label: 'ID',
						sortable: true
					},
					{
						key: 'name',
						label: 'Название',
						sortable: true
					},
					{
						key: 'created_at',
						label: 'Создан',
						sortable: true
					},
					{
						key: 'confirmations',
						label: 'Количество подтверждений',
						sortable: true
					},
					{
						key: 'proxy',
						label: 'Прокси',
						sortable: true
					},
					{
						key: 'currency',
						label: 'Валюта',
						sortable: true
					},
					{
						key: 'payment_type_readable',
						label: 'Метод оплаты',
						sortable: false
					},
					{
						key: 'comment',
						label: 'Комментарий',
						sortable: true
					},
					{
						key: 'crypto_transactions',
						label: 'Транзакции по заказам',
						sortable: true
					},
					{
						key: 'actions',
						label: 'Действия',
					},
				],
				filterField: [
					{
						name: "name",
						value: "",
						type: "text"
					},
					{
						name: "comment",
						value: "",
						type: "text"
					},
				]
			};
		}
	};
</script>

<style scoped>

</style>