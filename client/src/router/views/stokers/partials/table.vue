<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="card-box">
        <h4 class="header-title">
          <router-link class="btn btn-primary btn-xs" tag="a" to="/stokers/create/">Добавить ответственного
          </router-link>
        </h4>
        <div class="table-responsive">
          <b-table
              id="drivers-table"
              :items="stokers.data"
              :fields="fields"
              small
              :sort-by.sync="sortBy"
              :sort-desc.sync="sortDesc"
              sortDirection="desc"
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
            <template v-slot:cell(actions)="data">
              <div class="btn-group">
                <router-link
                    :to="`/stokers/show/${data.item.number}`"
                    tag="a"
                    class="btn btn-primary btn-xs"
                >
                  <b-icon icon="eye"></b-icon>
                </router-link>
                <router-link class="btn btn-warning btn-xs" tag="a"
                             :to="`/stokers/edit/${data.item.number}`">
                  <b-icon icon="pencil"></b-icon>
                </router-link>
                <b-button
                    class="btn"
                    variant="danger"
                    size="xs"
                    @click="deleteStoker(data.item.number)"
                >
                  <b-icon icon="trash"></b-icon>
                </b-button>
              </div>
            </template>
<!--             <template v-slot:top-row="columns">-->
<!--               <td></td>-->
<!--               <td>-->
<!--                 <b-form-input-->
<!--                     :type="filterField.find((value) => value.name === 'name').type"-->
<!--                     :name="filterField.find((value) => value.name === 'name').name"-->
<!--                     v-model="filterField.find((value) => value.name === 'name').value"-->
<!--                     size="sm"-->
<!--                     @blur="filters"-->
<!--                 >-->
<!--                 </b-form-input>-->
<!--               </td>-->
<!--             </template>-->
          </b-table>
<!--           <b-pagination-->
<!--               v-model="currentPage"-->
<!--               :total-rows="totalRows"-->
<!--               :per-page="perPage"-->
<!--               aria-controls="drivers-table"-->
<!--               align="left"-->
<!--               @change="paginate"-->
<!--               v-if="operators.last_page > 1"-->
<!--           ></b-pagination>-->
        </div> <!-- end table-responsive-->
      </div> <!-- end card-box -->
    </div> <!-- end col -->
  </div>
</template>

<script>
	import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';

	export default {
		name: "table-stokers",
		computed: {},
		props: {
			stokers: {
				type: Object,
			},
			spiner: {
				type: Boolean,
			}

		},
		components: {
			BIcon,
			BIconTrash,
			BIconPencil,
			BIconEye,
		},
		data() {

			return {
				page: 1,
				sortBy: "priority",
				sortDesc: true,
				fields: [
          {
          	key: 'number',
            label: 'ID'
          },
					{
						key: 'client',
						label: 'Клиент',
					},
					{
						key: 'location',
						label: 'Локация',
					},
					{
						key: 'product_type',
						label: 'Товар',
					},
					{
						key: 'bot',
						label: 'Бот',
					},
					{
						key: 'actions',
						label: 'Действия'
					},
				],
				perPage: 3,
				currentPage: 1,
				totalRows: 54,
			};
		},
		mounted() {
			this.currentPage = this.stokers.current_page;
			this.perPage = this.stokers.per_page;
			this.totalRows = this.stokers.total;
		},
		methods: {
			paginate(pageNum) {
				this.$emit('paginate', pageNum);
			},
			async deleteStoker(number) {
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
						this.$emit('delete-by-id', number);
					}
				} catch (e) {
				}
			},
			// filters() {
			// 	this.filterField.find((value) => value.name === 'name').value = this.filterField.find(
			// 		(value) => value.name === 'name').value.trim();
			// 	this.$emit('filters', this.filterField);
			// },
		}
	};
</script>

<style scoped>
</style>