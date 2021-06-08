<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <b-overlay :show="spiner" rounded="sm" no-center>
          <template v-slot:overlay>
            <div class="centered">
              <b-spinner variant="secondary"></b-spinner>
            </div>
          </template>
          <div class="card-box">
            <div class="card mt-2">
              <div class="card-body">
                <b-row>
                  <b-col v-for="botLogic in botLogics" lg="4" sm="6">
                    <b-card no-body>
                      <b-card-header class="text-center"><a href="#"><h4>{{ botLogic.name }}</h4>
                      </a>
                      </b-card-header>
                      <b-card-body>
                        <b-card-text>{{ botLogic.description }}</b-card-text>
                      </b-card-body>
                      <b-card-footer v-if="botLogic.type === '1'" class="text-center">
                        <b-button variant="primary mr-1 mt-1" @click="$router.push({ name: 'bot-logics.show', params: {
                                                    number: botLogic.number,
                                                    type: botLogic.type_readable
                                                    }})">
                          <b-icon icon="eye"></b-icon>
                          Посмотреть
                        </b-button>
                        <b-button
                            v-if="currentUser.role !== 3"
                            variant="warning mt-1"
                            @click="tryToCloneBotLogic(botLogic.type_readable, botLogic.number)"
                        >
                          <b-icon icon="files"></b-icon>
                          Клонировать
                        </b-button>
                      </b-card-footer>
                      <b-card-footer v-else class="text-center">
                        <template v-if="currentUser.role !== 3">
                          <b-button variant="primary mr-1 mt-1" @click="$router.push({ name: 'bot-logics.edit', params: {
                                                    number: botLogic.number,
                                                    type: botLogic.type_readable
                                                    }})">
                            <b-icon icon="pencil"></b-icon>
                            Изменить
                          </b-button>
                          <b-button variant="warning mr-1 mt-1"
                                    @click="tryToCloneBotLogic(botLogic.type_readable, botLogic.number)">
                            <b-icon icon="files"></b-icon>
                            Клонировать
                          </b-button>
                          <b-button variant="danger mt-1"
                                    @click="tryToDeleteBotLogic(botLogic.number)">
                            <b-icon icon="trash"></b-icon>
                            Удалить
                          </b-button>
                        </template>

                        <template v-else>
                          <b-button variant="primary mr-1 mt-1" @click="$router.push({ name: 'bot-logics.show', params: {
                                                        number: botLogic.number,
                                                        type: botLogic.type_readable
                                                    }})">
                            <b-icon icon="eye"></b-icon>
                            Посмотреть
                          </b-button>
                        </template>
                      </b-card-footer>
                    </b-card>
                  </b-col>
                </b-row>
              </div>
            </div>
          </div>
        </b-overlay>
      </div>
    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {authComputed, botLogicsComputed, botLogicsMethods} from "@state/helpers";
import {BIcon, BIconEye, BIconFiles, BIconPencil, BIconTrash} from 'bootstrap-vue';


export default {
  name: "bot-logics.index",
  page: {
    title: 'Логика ботов',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader, BIcon, BIconTrash, BIconPencil, BIconEye, BIconFiles},
  data() {
    return {
      title: 'Логика ботов',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Логика ботов',
          active: true,
        },
      ],
    };
  },
  computed: {
    ...botLogicsComputed,
    ...authComputed,
  },
  async mounted() {
   // console.log('spiner', this.spiner);
    await this.loadBotLogics();
   // console.log('spiner', this.spiner);
  },
  methods: {
    ...botLogicsMethods,
    tryToCloneBotLogic(type_readable, number) {
      return this.cloneBotLogic({type_readable, number})
          .then((res) => {
            this.loadBotLogics();
          })
          .catch((err) => {
          });
    },
    async tryToDeleteBotLogic(number) {
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
          this.deleteBotLogic(number).then((res) => {
            this.loadBotLogics();
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