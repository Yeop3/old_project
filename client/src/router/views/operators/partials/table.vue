<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="card-box">
        <h4 class="header-title">
          <router-link class="btn btn-primary btn-xs" tag="a" to="/operators/create/">Добавить оператора
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
                :items="operators.data"
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
                      :type="filterField.find((value) => value.name === 'name').type"
                      :name="filterField.find((value) => value.name === 'name').name"
                      v-model="filterField.find((value) => value.name === 'name').value"
                      size="sm"
                      @blur="filters"
                  >

                  </b-form-input>
                </td>
              </template>

              <template v-slot:cell(client)="data">
                <router-link v-if="data.item.client" tag="a" :to="`/clients/${data.item.client.number}`">
                  {{data.item.client.label}}
                </router-link>
                <b-badge variant="warning" v-else>
                  Не выбран
                </b-badge>
              </template>

              <template v-slot:cell(actions)="data">
                <div class="btn-group">
                  <router-link class="btn btn-primary btn-xs" tag="a"
                               :to="`/operators/edit/${data.item.number}`">
                    <b-icon icon="pencil"></b-icon>
                  </router-link>
                  <b-button class="btn" variant="danger" size="xs"
                            @click="deleteProductTypes(data.item.number)">
                    <b-icon icon="trash"></b-icon>
                  </b-button>
                </div>
              </template>
            </b-table>
            <b-pagination
                v-model="currentPage"
                :total-rows="totalRows"
                :per-page="perPage"
                aria-controls="drivers-table"
                align="left"
                @change="paginate"
                v-if="operators.last_page > 1"
            ></b-pagination>
          </div> <!-- end table-responsive-->
        </b-overlay>
      </div> <!-- end card-box -->
    </div> <!-- end col -->
  </div>
</template>

<script>
import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';
import {commissionComputed} from '@state/helpers';

export default {
  name: "table-operators",
  computed: {},
  props: {
    operators: {
      type: Object,
      default: () => {
      }
    },
    status: {
      type: Array,
      default: () => [],
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
      filterField: [
        {
          name: "name",
          value: "",
          type: "text"

        },
      ],
      page: 1,
      sortBy: "priority",
      sortDesc: true,
      fields: [
        {
          key: 'number',
          label: 'ID',
          sortable: true
        },
        {
          key: 'name',
          label: 'Имя оператора',
          sortable: true
        },
        {
          key: 'client',
          label: 'Клиент',
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
    this.currentPage = this.operators.current_page;
    this.perPage = this.operators.per_page;
    this.totalRows = this.operators.total;
  },
  methods: {
    paginate(pageNum) {
      this.$emit('paginate', pageNum);
    },
    async deleteProductTypes(id) {
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
          this.$emit('delete-by-id', id);
        }
      } catch (e) {
      }
    },
    filters() {
      this.filterField.find((value) => value.name === 'name').value = this.filterField.find(
          (value) => value.name === 'name').value.trim();
      this.$emit('filters', this.filterField);
    },
  }
};
</script>

<style scoped>

</style>