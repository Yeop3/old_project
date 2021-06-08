<template xmlns="">
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box" v-if="!spiner">
          <div class="card mt-2">
            <div class="card-body">
              <div class="btn-group mr-2">

                <b-button class="btn" @click="openGiveModal" variant="warning">
                  <b-icon icon="chat-dots"></b-icon>
                  Отправить сообщение
                </b-button>
                <b-button
                    class="btn"
                    @click="$router.push({ name: 'messages.client', params: {
                                                    clientNumber: client.number
                                                    }})"
                    variant="success"
                >
                  <b-icon icon="card-list"></b-icon>
                  Переписка
                </b-button>
                <b-button class="btn" @click="openHistoryModal(client)" variant="secondary">
                  <b-icon icon="person-lines-fill"></b-icon>
                  История
                </b-button>
                <b-button @click="$router.push({ name: 'clients.edit', params: {
                                                    number: client.number
                                                    }})" variant="primary">
                  <b-icon icon="pencil"></b-icon>
                  Изменить
                </b-button>
                <b-button @click="tryToDeleteClient(client.number)" variant="danger">
                  <b-icon icon="trash"></b-icon>
                  Удалить
                </b-button>
              </div>
              <b-table
                  class="mt-2"
                  :items="[client]"
                  :fields="fields"
                  stacked
              >
                <template v-slot:cell(username)="row">
                  <template v-if="row.item.username">
                    @{{ row.item.username }}
                  </template>
                  <b-badge variant="warning" v-else>
                    Ник не указан
                  </b-badge>
                </template>
                <template v-slot:cell(name)="row">
                  <template v-if="row.item.name">
                    {{ row.item.name }}
                  </template>
                  <b-badge variant="warning" v-else>
                    Имя не указано
                  </b-badge>
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

                <template v-slot:cell(bots)="row">
                  <div v-for="bot in row.item.bots" :key="`bot-${bot.number}-client-${row.item.number}`">
                    <router-link
                        tag="a"
                        :to="`/bots/card/${bot.number}`"
                    >
                      {{ bot.username }}
                    </router-link>
                  </div>

                  <b-button
                      variant="primary"
                      size="xs"
                      class="dropdown-left"
                      :to="`/messages/${row.item.number}`"
                  >
                    Сообщения
                  </b-button>
                </template>

                <template v-slot:cell(all_orders)="row">
                  <div>
                    {{ row.item.orders.length }}
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

                <template v-slot:cell(coming)="row">
                  {{ row.item.coming }} грн.
                </template>

                <template v-slot:cell(get_products)="row">
                  {{ row.item.paid + row.item.given_operator }}
                </template>

                <template v-slot:cell(paid_all)="row">
                  {{ row.item.paid }}
                </template>

              </b-table>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-center mb-3" v-else>
          <b-spinner></b-spinner>
        </div>
      </div>
      <!-- <b-modal
           id="modal-give"
           ref="modal-give"
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
          :item="client"
          v-if="showModal"
          :errors="errors"
          id="modal-give"
      />
      <ModalHistory
          :title-modal="'История изменений'"
          :client="item"
          :client-history="clientHistory"
          id="modal-history"
      />
    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config'
import Layout from '@layouts/main'
import PageHeader from '@components/page-header'
import {clientsComputed, clientsMethods} from "@state/helpers";
import {BIcon, BIconChatDots, BIconPencil, BIconTrash, BIconCardList, BIconPersonLinesFill} from 'bootstrap-vue';
import FormInput from "@components/ui/form/FormInput";
import FormTextarea from '@components/ui/form/FormTextarea';
import ModalMessages from '@views/clients/modal-messages';
import ModalHistory from '@views/clients/components/modal-history';
import axios from "axios";

export default {
  name: "clients.show",
  page: {
    title: 'Клиенты',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {
    Layout,
    PageHeader,
    BIcon,
    BIconPencil,
    BIconCardList,
    BIconTrash,
    BIconChatDots,
    BIconPersonLinesFill,
    FormInput,
    FormTextarea,
    ModalMessages,
    ModalHistory,
  },
  data() {
    return {
      title: 'Клиенты',
      message: '',
      titleModal: 'Отправить сообщение @',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Клиенты',
          to: '/clients',
        },
        {
          text: 'Карточка клиента',
          active: true
        }
      ],
      fields: [
        {
          key: 'number',
          label: 'ID',
        },
        {
          key: 'telegram_id',
          label: 'Телеграм ID',
        },
        {
          key: 'username',
          label: 'Ссылка',
        },
        {
          key: 'name',
          label: 'Полное имя',
        },
        // {
        //   key: 'web_login',
        //   label: 'Веб-логин',
        // },
        {
          key: 'note',
          label: 'Заметка',
        },
        {
          key: "bots",
          label: 'Боты'
        },
        {
          key: 'created_at',
          label: 'Добавился',
        },
        {
          key: 'visited_at',
          label: 'Посещение',
        },
        {
          key: 'ban_expires_at',
          label: 'Забанен',
        },
        {
          key: 'in_black_list',
          label: 'В черном списке',
        },
        {
          key: 'paid_all',
          label: 'Всего оплаченых заказов (за все время)',
        },
        {
          key: 'coming',
          label: 'Приход',
        },
        {
          key: 'discount_value',
          label: 'Размер персональной скидки, %',
        },
        {
          key: 'all_orders',
          label: 'Всего заказов (в базе)',
        },
        {
          key: 'get_products',
          label: 'Всего товаров получил (в базе)',
        },
        {
          key: 'awaiting_payment',
          label: 'Заказы (в базе): Ожидает оплаты',
        },
        {
          key: 'partially_paid',
          label: 'Заказы (в базе): Частично оплачен',
        },
        {
          key: 'paid',
          label: 'Заказы (в базе): Оплачен',
        },
        {
          key: 'canceled_by_client',
          label: 'Заказы (в базе): Отменен клиентом',
        },
        {
          key: 'canceled_by_timeout',
          label: 'Заказы (в базе): Отменен по таймауту',
        },
        {
          key: 'canceled_by_system',
          label: 'Заказы (в базе): Отменен системой',
        },
        {
          key: 'canceled_by_operator',
          label: 'Заказы (в базе): Отменен оператором',
        },
        {
          key: 'given_operator',
          label: 'Заказы (в базе): Отдан оператором',
        },
      ],
      item: null,
      clientHistory: [],
      showModal: false,
      errors: {},
    }
  },
  computed: {
    ...clientsComputed,

  },
  mounted() {
    this.loadClient(this.$route.params.number);
  },
  methods: {
    ...clientsMethods,
    async openGiveModal() {
      this.titleModal += this.client.username || this.client.name;
      this.showModal = true;
      await this.$nextTick();
      this.$bvModal.show('modal-give');
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
      this.$bvModal.hide('modal-give');
      this.showModal = false;
    },
    async tryToDeleteClient(number) {
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
          this.deleteClient(number).then((res) => {
            this.$router.push({name: 'clients.index'});
          });
        }
      } catch (e) {
      }
    },
    async tryToBanClient(days, number) {
      const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите забанить этого клиента?', {
        title: 'Пожалуйста подтвердите',
        size: 'sm',
        buttonSize: 'sm',
        okVariant: 'warning',
        okTitle: 'Да',
        cancelTitle: 'Нет',
        footerClass: 'p-2',
        hideHeaderClose: false,
        centered: true
      });
      if (value) {
        await this.banClient({days: days, number: number});
        await this.loadClient(number);
      }
    },
    async tryToUnBanClient(number) {
      const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите разбанить этого клиента?', {
        title: 'Пожалуйста подтвердите',
        size: 'sm',
        buttonSize: 'sm',
        okVariant: 'warning',
        okTitle: 'Да',
        cancelTitle: 'Нет',
        footerClass: 'p-2',
        hideHeaderClose: false,
        centered: true
      });
      if (value) {
        await this.unBanClient(number);
        await this.loadClient(number);
      }
    },
    async tryToBlackListClient(number) {
      const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите внести этого клиента в черный список?', {
        title: 'Пожалуйста подтвердите',
        size: 'sm',
        buttonSize: 'sm',
        okVariant: 'warning',
        okTitle: 'Да',
        cancelTitle: 'Нет',
        footerClass: 'p-2',
        hideHeaderClose: false,
        centered: true
      });
      if (value) {
        await this.blackListClient(number);
        await this.loadClient(number);
      }
    },
    async tryToUnBlackListClient(number) {
      const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите удалить этого клиента из черного списка?', {
        title: 'Пожалуйста подтвердите',
        size: 'sm',
        buttonSize: 'sm',
        okVariant: 'warning',
        okTitle: 'Да',
        cancelTitle: 'Нет',
        footerClass: 'p-2',
        hideHeaderClose: false,
        centered: true
      });
      if (value) {
        await this.unBlackListClient(number);
        await this.loadClient(number);
      }
    },
    async openHistoryModal(item) {
      let params = {};
      params['number'] = item.number;
      const history = await axios.get(`api/clients/history`, {params});
      this.clientHistory = history.data;
      this.item = item;
      this.$bvModal.show('modal-history');
    },
  }
};
</script>

<style>
.table.b-table.b-table-stacked > tbody > tr > :first-child {
  border-top-width: 1px !important;
}

.table.b-table.b-table-stacked > tbody > tr > :last-child {
  border-bottom: 1px solid #dee2e6 !important;
}
</style>