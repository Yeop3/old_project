<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box" v-if="!spiner">
          <div class="card ">
            <div class="card-body">
              <b-alert :show="isReinstallSuccess" @dismissed="isReinstallSuccess=0" variant="success" dismissible
                       class="text-center">
                {{ reinstallSuccess }}
              </b-alert>
              <b-alert :show="isReinstallError" @dismissed="isReinstallError=0" variant="danger" dismissible
                       class="text-center">
                {{ reinstallError }}
              </b-alert>
              <b-button class="mr-2" @click="tryToChangeInfoBot" variant="primary" size="xs">
                <i class="remixicon-pencil-line mr-1"></i>
                Изменить
              </b-button>
              <b-button class="mr-2" @click="tryToReinstallWebhook" variant="warning" size="xs">
                <i class="remixicon-refresh-line"></i>
                Переустановка Webhook
              </b-button>
              <b-button class="bt" @click="tryToDeleteBot" variant="danger" size="xs">
                <i class="remixicon-delete-bin-6-line pl-1"></i>
                Удалить
              </b-button>
              <b-table class="mt-3" stacked :items="bot_info" :fields="fields">
                <template v-slot:cell(operator)="row">
                  <template v-if="row.item.operator">
                    {{row.item.operator.name}}({{row.item.operator.client ? row.item.operator.client.telegram_id :  'клиент не назначен' }})
                  </template>
                  <template v-else>
                    Не указан
                  </template>
                </template>

                <template v-slot:cell(drivers)="row">
                  <b-badge
                      v-for="driver in row.item.drivers"
                      :key="driver.number"
                      variant="info"
                      class="mr-1"
                  >
                    {{driver.name}}
                  </b-badge>
                </template>

                <template v-slot:cell(type)="row">
                  Стандартный
                  <br>
                  Telegram
                </template>

                <template v-slot:cell(logic)="row">
                  {{ row.item.logic.name }}
                </template>
              </b-table>
            </div>
          </div>
        </div>
        <div class="centered" v-else>
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
import {botsMethods, botsComputed} from "@state/helpers";


export default {
  name: "bots.show",
  page: {
    title: 'Карточка бота',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader},
  data() {
    return {
      title: 'Карточка бота',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Боты',
          to: '/bots',
        },
        {
          text: 'Карточка бота',
          active: true
        }
      ],
      fields: [
        {
          key: 'number',
          label: 'ID'
        },
        {
          key: 'telegram_bot_token',
          label: 'Telegram Bot Token'
        },

        {
          key: 'webhook_install',
          label: 'Webhook установлен'
        },
        {
          key: 'username',
          label: 'Username'
        },
        {
          key: 'name',
          label: 'Имя'
        },
        {
          key: 'operator',
          label: 'Ответственный оператор'
        },
        {
          key: 'drivers',
          label: 'Курьеры'
        },
        {
          key: 'last_error_message',
          label: 'Последнее сообщение об ошибке'
        },
        {
          key: 'last_error_date',
          label: 'Дата последнего сообщения об ошибке'
        },
        {
          key: 'pending_update_count',
          label: 'Сообщения ожидающие обработки'
        },
        {
          key: 'status',
          label: 'Состояние'
        },
        {
          key: 'logic',
          label: 'Логика'
        },
        {
          key: 'allow_create_clients',
          label: 'Разрешено добавлять новых клиентов в базу'
        },
        {
          key: 'orders',
          label: 'Заказы'
        },
        {
          key: 'clients',
          label: 'Клиенты'
        },
      ],

      bot_info: [],
      isReinstallSuccess: 0,
      isReinstallError: 0,
      reinstallSuccess: null,
      reinstallError: null,
      dismissSecs: 5,
    }
  },
  computed: {
    ...botsComputed,
  },
  mounted() {
    this.load(this.$route.params.number).then((bot) => {
      this.getWebhookInfo(this.bot.token).then((info) => {
        this.bot_info.push({
          number: this.bot.number,
          logic: this.bot.logic.name,
          name: this.bot.name,
          username: '@' + this.bot.username,
          telegram_bot_token: this.bot.token,
          operator: this.bot.operator,
          drivers: this.bot.drivers,
          status: this.bot.active ? 'Активен' : 'Не активен',
          allow_create_clients: this.bot.allow_create_clients ? 'Да' : 'Нет',
          last_error_message: this.bot_webhookinfo.last_error_message ? this.bot_info.last_error_message : '-',
          last_error_date: this.bot_webhookinfo.last_error_date ? this.bot_info.last_error_date : '-',
          webhook_install: info.url ? 'Да' : 'Нет',
          pending_update_count: this.bot_webhookinfo.pending_update_count,
        })
      })
    })
  },
  methods: {
    ...botsMethods,
    async tryToDeleteBot() {
      var number = this.bot_info[0].number;
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
          this.delete(number)
              .then(() => {
                this.$router.push({name: 'bots.index'})
              });
        }
      } catch (e) {
      }
    },
    tryToChangeInfoBot() {
      var number = this.bot_info[0].number;
      return this.$router.push({name: 'bots.edit', params: {number}});
    },
    tryToReinstallWebhook() {
      return this.reinstallWebhook(this.bot.number)
          .then((res) => {
            if (res.ok) {
              this.reinstallSuccess = 'Webhook успешно переустановлен';
              this.isReinstallSuccess = 5;
            } else {
              this.isReinstallError = 5;
              this.reinstallError = 'Произошла ошибка: ' + res.description;
            }
          })
          .catch((error) => {
            this.isReinstallError = 5;
            this.reinstallError = 'Произошла ошибка: ' + error.response.data.message;
          })
    },
  }
}
</script>

<style>
.table.b-table.b-table-stacked > tbody > tr > :first-child {
  border-top-width: 1px !important;
}

.table.b-table.b-table-stacked > tbody > tr > :last-child {
  border-bottom: 1px solid #dee2e6 !important;
}
</style>