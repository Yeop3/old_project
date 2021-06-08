<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>

    <div class="row">
      <div class="col-md-12">
        <div class="card-box row justify-content-center">
          <bot-form
              :bot="bot"
              :errors="errors"
              :operatorsForBots="operators_for_bots"
              :botLogicsForSelect="botLogicsForSelect"
              @submit="tryToCreateBot"
              type="Создать"
              :submitting="submitting"
          />
        </div>
      </div>
    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config'
import Layout from '@layouts/main'
import PageHeader from '@components/page-header'
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
  name: "bot-create",
  page: {
    title: 'Добавление бота',
    meta: [{name: 'description', content: appConfig.description}],
  },
  components: {Layout, PageHeader, BotForm},
  data() {
    return {
      title: 'Добавление бота',
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
          text: 'Добавление бота',
          active: true,
        },
      ],
      submitting: false,
      errors: {},
    }
  },
  computed: {
    ...botsComputed,
    ...botLogicsComputed,
    ...operatorComputed,
  },
  mounted() {
    this.loadBotLogics();
    this.loadOperatorsForBots();
  },
  methods: {
    ...botsMethods,
    ...botLogicsMethods,
    ...operatorForBotsMethods,
    tryToCreateBot(form) {
      this.errors = {};
      this.submitting = true;
      this.create(form)
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