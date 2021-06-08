<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box row justify-content-center" v-if="!spiner">
          <bot-form
              v-if="bot"
              :bot="bot"
              :errors="errors"
              :operatorsForBots="operators_for_bots"
              :botLogicsForSelect="botLogicsForSelect"
              @submit="tryToUpdateBot"
              type="Изменить"
              :submitting="submitting"
          />
        </div>

        <div class="centered" v-else>
          <b-spinner></b-spinner>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {
  botsMethods,
  botsComputed,
  botLogicsMethods,
  botLogicsComputed,
  operatorForBotsMethods,
  operatorComputed
} from '@state/helpers';
import BotForm from './form';

export default {
  name: "bot.edit",
  page: {
    title: 'Изменение бота',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader, BotForm},
  data() {
    return {
      title: 'Изменение бота',
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
          text: 'Изменение бота',
          active: true
        }
      ],
      submitting: false,
      errors: {},
    }
  },
  computed: {
    ...botsComputed,
    ...botLogicsComputed,
    ...botLogicsComputed,
    ...operatorComputed,
  },
  mounted() {
    this.loadOperatorsForBots();
    this.loadBotLogics();
    this.load(this.$route.params.number);
  },
  methods: {
    ...botsMethods,
    ...botLogicsMethods,
    ...operatorForBotsMethods,
    tryToUpdateBot(form) {
      this.errors = {};
      this.submitting = true;
      this.update(form)
          .then(() => {
            this.$router.push(this.$route.query.redirectFrom || {name: 'bots.index'})
          })
          .catch(res => {
            this.errors = res.response.data.errors;
          })
          .finally(() => {
            this.submitting = false;
          });
    },
  }
}
</script>

<style scoped>

</style>