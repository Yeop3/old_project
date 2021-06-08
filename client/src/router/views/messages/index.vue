<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box">
          <h3>Сообщения</h3>
          <h4 v-if="client">Клиент: {{ '@' + client.username }}</h4>
          <h4 v-if="bot">Бот: {{ bot.username }}</h4>
          <form-select
              v-model="botCheck"
              :options="bot_select"
              @change="handleBotSelect"
              :size="'sm'"
          />
          <div class="card mt-2" v-if="bot">
            <div class="card-body">
              <div class="row">
                <div class="col-6 col-md-10 col-sm-12 d-flex flex-wrap mb-2">
                  <b-input-group class="col-xl-4 col-lg-7 col-md-5 col-sm-12 mb-1">
                    <b-input-group-prepend is-text>
                      <b-icon-calendar></b-icon-calendar>
                    </b-input-group-prepend>
                    <datetime
                        v-model="dateStart"
                        type="datetime"
                        input-class="form-control text-break text-wrap bg-transparent h-auto text-muted"
                        hidden-name="date_start"
                        placeholder="Дата от"
                        @input="loadMessages"
                        value-zone="Europe/Kiev"
                    ></datetime>
                  </b-input-group>

                  <b-input-group class="col-xl-4 col-lg-7 col-md-5 col-sm-12 mb-1">
                    <b-input-group-prepend is-text>
                      <b-icon-calendar></b-icon-calendar>
                    </b-input-group-prepend>
                    <datetime
                        v-model="dateEnd"
                        type="datetime"
                        input-class="form-control text-break text-wrap bg-transparent h-auto text-muted"
                        hidden-name="date_end"
                        placeholder="Дата до"
                        @input="loadMessages"
                        value-zone="Europe/Kiev"
                    ></datetime>
                  </b-input-group>
                </div>
              </div>

              <b-overlay :show="spinner" rounded="sm" no-center>
                <template v-slot:overlay>
                  <div class="centered">
                    <b-spinner variant="secondary"></b-spinner>
                  </div>
                </template>
                <div class="table-responsive">
                  <b-table
                      v-if="messages"
                      :items="messages.data"
                      :fields="fields"
                      class="text-center"
                      small
                      no-local-sorting
                      sortDirection="desc"
                  >
                    <template v-slot:cell(from_bot)="row">
                      <div style="white-space: pre-line">
                        {{ row.item.from_bot ? '@' + bot.username : '@' + client.username }}
                      </div>
                    </template>
                    <template v-slot:cell(text)="row">
                      <div v-if="row.item.from_bot" v-html="row.item.text">
                        {{ row.item.text }}
                      </div>
                      <div v-else style="white-space: pre-line">
                        {{ row.item.text }}
                      </div>
                    </template>
                    <template v-slot:cell(created_at)="row">
                      <div style="white-space: pre-line">
                        {{ row.item.created_at }}
                      </div>
                    </template>
                  </b-table>
                  <b-pagination
                      v-if="messages"
                      v-model="currentPage"
                      :total-rows="messages.total"
                      :per-page="messages.per_page"
                      aria-controls="drivers-table"
                      align="left"
                      @change="paginate"
                  ></b-pagination>
                </div>
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
import {BIcon, BIconCalendar, BIconEye, BIconPencil, BIconTrash} from "bootstrap-vue";
import queryString from 'query-string';
import axios from 'axios';
import {Datetime} from 'vue-datetime';
import FormSelect from "@components/ui/form/FormSelect";

export default {
  name: "messages.index",
  page: {
    title: 'Сообщения',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {FormSelect, Layout, PageHeader, BIcon, BIconTrash, BIconPencil, BIconEye, Datetime, BIconCalendar},
  data() {
    return {
      title: 'Сообщения',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Сообщения',
          active: true,
        },
      ],
      fields: [
        {
          key: 'from_bot',
          label: '',
        },
        {
          key: 'text',
          label: 'Сообщение',
        },
        {
          key: 'created_at',
          label: 'Дата',
        },
      ],
      currentPage: 1,
      lastPage: 1,
      messages: null,
      spinner: false,
      dateStart: null,
      dateEnd: null,
      client: null,
      bot: null,
      botCheck: null,
      bot_select: [
        {value: null, text: "Не выбрано"}
      ]
    };
  },
  computed: {},
  mounted() {
    //console.log(this.$route.params);
    this.loadClient();
    if (this.$route.params.botNumber) {

      this.loadMessages();
      if(this.lastPage){
        this.currentPage = this.lastPage;
      }
      this.loadBot();
      this.botCheck = this.$route.params.botNumber;
    }
    this.loadBots();
  },
  methods: {
    loadMessages() {
      const query = queryString.stringify({
        page: this.currentPage,
        date_start: this.dateStart,
        date_end: this.dateEnd,
      });

      if (!this.$route.params.clientNumber || !this.$route.params.botNumber) {
        return;
      }

      this.spinner = true;
      axios.get(`/api/messages/${this.$route.params.clientNumber}/${this.$route.params.botNumber}?` + query)
          .then(res => {

            //TODO: MAKE NORMAL PAGINATION!
            this.lastPage = res.data.last_page;
            this.messages = res.data;
            this.messages.data = this.messages.data.map(
                message => {

                  const regex = /Номер заказа: ([\d]+)/gm;
                  const subst = `Номер заказа: <a href="/orders/show/$1">$1</a>`;

                  message.text.replace(regex, subst);
                  message.created_at = new Date(message.created_at)
                      .toLocaleString("ru-RU", {
                            year: 'numeric',
                            month: 'numeric',
                            day: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric'
                          }
                      );



                  return message;
                });
          })
          .finally(() => this.spinner = false);

    },
    loadClient() {
      this.spinner = true;

      axios.get('/api/clients/' + this.$route.params.clientNumber)
          .then(res => {
            this.client = res.data;
          })
          .finally(() => this.spinner = false);
    },
    loadBot() {
      this.spinner = true;

      axios.get('/api/bots/' + this.$route.params.botNumber)
          .then(res => {
            this.bot = res.data;
          })
          .finally(() => this.spinner = false);
    },
    async paginate(pageNum) {
      this.currentPage = pageNum;
      this.loadMessages();
    },
    async filters() {
      let params = {};
      for (let value of this.filterField) {
        if (value.value)
          params[value.name] = value.value;
      }
      await this.loadMessages({page: 1, params: params});
    },
    async loadBots() {
      this.spinner = true;

      const data = await axios.get('/api/bots/select?client_number=' + this.$route.params.clientNumber);
      this.bot_select = data.data;
      this.bot_select.unshift({value: null, text: "Не выбрано"});
    },
    handleBotSelect() {
      this.$router.push(`/messages/${this.botCheck}/${this.$route.params.clientNumber}`);
    }
  },
};
</script>

<style src="vue-datetime/dist/vue-datetime.css"></style>
