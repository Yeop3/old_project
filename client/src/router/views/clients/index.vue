<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box">
          <div class="card mt-2">
            <div class="card-body">
              <b-overlay :show="spiner" rounded="sm" no-center>
                <template v-slot:overlay>
                  <div class="centered">
                    <b-spinner variant="secondary"></b-spinner>
                  </div>
                </template>
                <div>
                  <b-dropdown variant="primary "
                              class="mb-2 mr-2" size="xs">
                    <template v-slot:button-content>
                      <i class="fas fa-download"></i>
                      Скачать базу клиентов (CSV)
                    </template>
                    <b-dropdown-item @click="tryExportTelegramCsv">
                      <i class="fas fa-download"></i>
                      Telegram клиенты
                    </b-dropdown-item>
                  </b-dropdown>
                  <b-dropdown variant="outline-info"
                              class="mb-2" text="Еще" size="xs">
                    <b-dropdown-item @click="tryUnBanAll">
                      <i class="far fa-heart"></i>
                      Разбанить всех
                    </b-dropdown-item>
                    <b-dropdown-item :to="{name: 'index-hand-dispatch-actual-telegram'}">
                      <i class="fas fa-paper-plane"></i>
                      Актуальные клиенты для ручной Telegram рассылки
                    </b-dropdown-item>
                    <b-dropdown-item :to="{name: 'clients.index.black-list'}">
                      <i class="fas fa-exclamation-circle"></i>
                      Черный список клиентов
                    </b-dropdown-item>
                    <b-dropdown-item :to="{name: 'clients.index.spam.reserve'}">
                      <i class="fas fa-exclamation-circle"></i>
                      Спамеры резервов
                    </b-dropdown-item>
                  </b-dropdown>
                </div>
                <div class="table-responsive">
                  <b-table
                      :items="clients.data"
                      :fields="fields"
                      class="text-center"
                      small
                      no-local-sorting
                      @sort-changed="onSorting"
                      :sortDirection="filterParams.sortDirection || 'desc'"
                      :sort-by="filterParams.sortField || null"
                  >
                    <template v-slot:top-row="columns">
                      <td :key="`filter_${field.key}`" v-for="field in fields">
                        <filter-field v-if="field.filter" :field="field" @on-input="onFilterInput" @on-click-reset-button="onClickResetFiltersButton" />
                      </td>
                    </template>

                    <template v-slot:cell(client)="row">
                      <router-link
                          tag="a"
                          :to="`/clients/${ row.item.number }`"
                      >
                        <template v-if="row.item.username">
                          @{{ row.item.username }}
                        </template>
                        <b-badge variant="warning" v-else>
                          Ник не указан
                        </b-badge>
                      </router-link>
                      <br>
                      <template v-if="row.item.name">
                        {{ row.item.name }}
                      </template>
                      <b-badge variant="warning" v-else>
                        Имя не указано
                      </b-badge>
                      <br>
                      Телеграм ID: {{ row.item.telegram_id }}
                      <br>
                      <b-button @click="openHistoryModal(row.item)" size="xs">
                        История
                      </b-button>
                    </template>

                    <template v-slot:cell(source)="row">
                      <router-link
                          tag="a"
                          :to="`/bots/card/${row.item.source.number}`"
                      >
                        {{ row.item.source.username }}
                      </router-link>
                    </template>

                    <template v-slot:cell(bots)="row">
                      <div v-for="bot in row.item.bots" :key="`bot-${bot.number}-client-${row.item.number}`">
                        <router-link
                            tag="a"
                            :to="`/bots/card/${bot.number}`"
                        >
                          {{ bot.username }}
                        </router-link>
                      </div>
                    </template>

                    <template v-slot:cell(ban_expires_at)="row">
                      <div v-if="row.item.ban_expires_at">
                        <div>
                          <b-button
                              variant="warning"
                              size="xs"
                              @click="tryToUnBanClient(row.item.number)"
                          >Разбанить
                          </b-button>
                        </div>
                      </div>
                      <div v-else>
                        <div>
                          <b-dropdown
                              text="Забанить"
                              size="xs"
                              variant="primary"
                              id="dropdown-left"
                          >
                            <b-dropdown-item @click="tryToBanClient(1, row.item.number)">
                              Забанить на
                              сутки
                            </b-dropdown-item>
                            <b-dropdown-item @click="tryToBanClient(7, row.item.number)">
                              Забанить на
                              неделю
                            </b-dropdown-item>
                            <b-dropdown-item @click="tryToBanClient(30, row.item.number)">
                              Забанить
                              на месяц
                            </b-dropdown-item>
                            <b-dropdown-item @click="tryToBanClient(365, row.item.number)">
                              Забанить
                              на год
                            </b-dropdown-item>
                          </b-dropdown>
                        </div>
                      </div>
                    </template>

                    <template v-slot:cell(in_black_list)="row">
                      <div v-if="row.item.in_black_list">
                        <div>
                          <b-button
                              variant="warning"
                              size="xs"
                              @click="tryToUnBlackListClient(row.item.number)"
                          >Убрать из ЧС
                          </b-button>
                        </div>
                      </div>
                      <div v-else>
                        <div>
                          <b-button
                              variant="primary"
                              size="xs"
                              @click="tryToBlackListClient(row.item.number)"
                          >В ЧС
                          </b-button>
                        </div>
                      </div>
                    </template>

                    <template v-slot:cell(note)="row">
                      <div v-if="row.item.note">
                        {{ row.item.note }}
                      </div>
                      <div v-else>
                        -
                      </div>
                    </template>

                    <template v-slot:cell(orders)="row">
                      <a v-b-tooltip="'Оплачен'">{{ row.item.paid }}</a>
                      /
                      <a v-b-tooltip="'Отдан оператором'">{{ row.item.given_operator }}</a>
                      /
                      <a v-b-tooltip="'Всего заказов'">{{ row.item.orders.length }}</a>
                      <br>
                      <span
                          v-b-tooltip="'Всего оплаченных заказов (за все время)'">{{
                          row.item.paid
                        }} заказов оплатил</span>
                      <br>
                      Приход: {{ row.item.coming }}
                    </template>

                    <template v-slot:cell(actions)="row">
                      <div class="btn-group mb-2">
                        <b-button v-b-tooltip="'Переписки'"
                                  @click="$router.push({ name: 'messages.client', params: {
                                                    clientNumber: row.item.number
                                                    }})"
                                  size="xs"
                                  class="btn-primary">
                          <b-icon icon="card-list"></b-icon>
                        </b-button>
                        <b-button v-b-tooltip="'Сообщение'" @click="openGiveModal(row.item)"
                                  size="xs" class="btn-success">
                          <b-icon icon="chat-dots"></b-icon>
                        </b-button>
                        <b-button
                            v-b-tooltip="'Изменить'"
                            @click="$router.push({ name: 'clients.edit', params: {
                                                        number: row.item.number
                                                        }})"
                            size="xs"
                            class="btn-warning">
                          <b-icon icon="pencil"></b-icon>
                        </b-button>

                        <!--                        <b-button-->
                        <!--                            v-b-tooltip="'Удалить'"-->
                        <!--                            @click="tryToDeleteClient(row.item.number)"-->
                        <!--                            size="xs"-->
                        <!--                            class="btn-danger">-->
                        <!--                          <b-icon icon="trash"></b-icon>-->
                        <!--                        </b-button>-->


                      </div>
                    </template>
                  </b-table>
                  <b-pagination
                      v-if="clients.last_page > 1"
                      v-model="currentPage"
                      :total-rows="clients.total"
                      :per-page="clients.per_page"
                      aria-controls="drivers-table"
                      align="left"
                      @change="paginate"
                  ></b-pagination>
                </div>
              </b-overlay>
            </div>
          </div>
          <!-- <b-modal
               id="modal-message"
               ref="modal-message"
               :title="titleModal"
               @ok="handleOkGive"
           >
             <form ref="form" @submit.stop.prevent="handleSubmit">
               <form-textarea
                   name="price"
                   description="* Введите сообщение."
                   v-model="message"
                   label="Сообщение, которое хотите отправить."
                   :size="'sm'"
               ></form-textarea>
             </form>
           </b-modal>-->
          <ModalMessages
              :title-modal="titleModal"
              @handle-submit="handleSubmit"
              :item="item"
              :errors="errors"
              v-if="showModal"
              id="modal-message"
          />
          <ModalHistory
              :title-modal="'История изменений'"
              :client="item"
              :client-history="clientHistory"
              id="modal-history"
          />
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {clientsComputed, clientsMethods, getFilters, saveFilters, modalConfirm} from "@state/helpers";
import {BIcon, BIconCardList, BIconChatDots, BIconEye, BIconPencil, BIconTrash} from "bootstrap-vue";
import FormTextarea from '@components/ui/form/FormTextarea';
import ModalMessages from '@views/clients/modal-messages';
import ModalHistory from '@views/clients/components/modal-history';
import FormSelect from "@components/ui/form/FormSelect";
import queryString from "query-string";
import axios from 'axios'
import {mapActions, mapState} from 'vuex'
import FilterField from '@views/components/FilterField';

export default {
  name: "clients.index",
  page: {
    title: 'Клиенты',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {
    FormSelect,
    Layout,
    PageHeader,
    BIcon,
    BIconTrash,
    BIconPencil,
    BIconEye,
    BIconCardList,
    BIconChatDots,
    FormTextarea,
    ModalMessages,
    ModalHistory,
    FilterField
  },
  data() {
    return {
      filterStorageKey: 'client_filters',
      title: 'Клиенты',
      message: '',
      titleModalInit: 'Отправить сообщение @',
      titleModal: '',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Клиенты',
          active: true,
        },
      ],
      fields: [
        {
          key: 'number',
          label: 'ID',
          sortable: true,
          filter: {
            value: null,
            type: "number"
          }
        },
        {
          key: 'client',
          label: 'Клиент',
          filter: {
            value: null,
            type: "text"
          }
        },
        {
          key: 'created_at',
          label: 'Добавился',
          sortable: true,
          filter: {
            value: null,
            type: "date"
          }
        },
        {
          key: 'bots',
          label: 'Боты',
          filter: {
            value: null,
            type: "select",
            options: []
          }
        },
        {
          key: 'visited_at',
          label: 'Посещение',
          sortable: true,
        },
        {
          key: 'ban_expires_at',
          label: 'Забанен',
          sortable: true,
          filter: {
            value: null,
            type: "select",
            options: [
              {text: "Все", value: null},
              {text: "Забанен", value: 1},
              {text: "Не забанен", value: 2},
            ]
          }
        },
        {
          key: 'in_black_list',
          label: 'В ч.с.',
          sortable: true,
          filter: {
            value: null,
            type: "select",
            options: [
              {text: "Все", value: null},
              {text: "В черном списке", value: 1},
              {text: "Не в черном списке", value: 2},
            ]
          }
        },
        {
          key: 'note',
          label: 'Заметка',
          filter: {
            value: null,
            type: "text"
          }
        },
        {
          key: 'discount_value',
          label: 'Скидка',
          sortable: true,
          filter: {
            value: null,
            type: "number"
          }
        },
        {
          key: 'orders',
          label: 'Заказы',
        },
        {
          key: 'actions',
          label: 'Действия',
          filter: {
            type: "reset-button"
          }
        },
      ],
      clientsTable: [],
      currentPage: 1,
      filterParams: {},
      item: null,
      showModal: false,
      errors: {},
      clientHistory: [],
    };
  },
  computed: {
    ...clientsComputed,
    ...mapState('bots', {botsList: 'select_bots'})
  },
  async created() {
    this.filterParams = getFilters(this.filterStorageKey)
    await this.getBotsList()
    this.fields.find(f => f.key === 'bots').filter.options = this.botsList
    await this.loadClients({params: this.filterParams});
    this.initValueFilters()
  },
  methods: {
    modalConfirm,
    ...clientsMethods,
    ...mapActions({
      getBotsList: 'bots/getSelect'
    }),
    initValueFilters() {
      this.fields.forEach(field => {
        if (!field.filter) return
        const filterValue = this.filterParams[field.key]
        if (filterValue) {
          field.filter.value = filterValue
        }
      })
    },
    async paginate(pageNum) {
      this.currentPage = pageNum;
      this.filterParams.page = pageNum;
      saveFilters(this.filterStorageKey, this.filterParams)
      this.loadClients({params: this.filterParams});
    },
    async openGiveModal(item) {
      this.item = item;
      this.titleModal = this.titleModalInit + item.username;
      this.showModal = true;
      await this.$nextTick();
      this.$bvModal.show('modal-message');
    },
    async openHistoryModal(item) {
      let params = {};
      params['number'] = item.number;
      const history = await axios.get(`api/clients/history`, {params});
      this.clientHistory = history.data;
      this.item = item;
      this.$bvModal.show('modal-history');
    },
    handleOkGive(bvModalEvt) {
      bvModalEvt.preventDefault();
      this.handleSubmit();
    },
    async handleSubmit(field) {
      await this.sendMessageToClient(field)
          .then((res) => {
          })
          .catch((err) => {
            this.errors = err.response.data.errors;
            this.$bvToast.toast(this.errors, {
              title: 'Errors',
              variant: 'danger',
              autoHideDelay: 5000,
            });
          });
      await this.$nextTick();
      this.message = '';
      this.$bvModal.hide('modal-message');
      this.showModal = false;
    },
    async tryToDeleteClient(number) {
      try {
        const value = await this.modalConfirm('Действительны ли вы хотите это удалить?')
        if (value) {
          this.deleteClient(number).then((res) => {
            this.loadClients({page: 1});
          });
        }
      } catch (e) {
      }
    },
    async tryToBanClient(days, number) {
      const value = await this.modalConfirm('Вы уверены, что хотите забанить этого клиента?')
      if (value) {
        await this.banClient({days: days, number: number});
        await this.loadClients({page: 1});
      }
    },
    async tryToUnBanClient(number) {
      const value = await this.modalConfirm('Вы уверены, что хотите разбанить этого клиента?')
      if (value) {
        await this.unBanClient(number);
        await this.loadClients({page: 1});
      }
    },
    async tryToBlackListClient(number) {
      const value = await this.modalConfirm('Вы уверены, что хотите внести этого клиента в черный список?')
      if (value) {
        await this.blackListClient(number);
        await this.loadClients({page: 1});
      }
    },
    async tryToUnBlackListClient(number) {
      const value = await this.modalConfirm('Вы уверены, что хотите удалить этого клиента из черного списка?')
      if (value) {
        await this.unBlackListClient(number);
        await this.loadClients({page: 1});
      }
    },
    async onFilterInput() {

      let params = {};
      for (let field of this.fields) {
        if (field.filter)
          params[field.key] = field.filter.value;
      }

      this.filterParams = {...this.filterParams, ...params, page: 1};
      this.currentPage = 1;

      saveFilters(this.filterStorageKey, this.filterParams)

      await this.loadClients({params: this.filterParams});

    },
    async onSorting(header) {
      if (!header.sortBy) return;

      const params = {};
      params['sortField'] = header.sortBy;
      params['sortDirection'] = header.sortDesc === true ? 'desc' : 'asc';

      this.filterParams = {...this.filterParams, ...params, page: 1};
      this.currentPage = 1;

      saveFilters(this.filterStorageKey, this.filterParams)

      await this.loadClients({page: this.currentPage, params: this.filterParams});

    },
    async tryExportTelegramCsv() {
      await this.exportTelegramCsv();
    },
    async tryUnBanAll() {
      await this.unbanAll();
      await this.loadClients({page: 1});
    },
    clearFilters() {
      this.fields.forEach(f => {
        if (f.filter)
          f.filter.value = null
      })
    },
    onClickResetFiltersButton() {
      this.clearFilters()
      this.onFilterInput()
    },
  }
};
</script>

<style>
.b-icon {
  color: rgb(95, 95, 95);
}
</style>