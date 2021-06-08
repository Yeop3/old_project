<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card-box row justify-content-center">
        <b-form @submit.prevent="submit" class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
          <form-input
              name="token"
              label="Токен"
              v-model="form.token"
              :errors="errors"
              description="* Токен бота - это уникальный секретный ключ выданный @BotFather'ом."
              :size="'sm'"
          />

          <form-input
              name="name"
              label="Название"
              v-model="form.name"
              :errors="errors"
              description="Придумайте уникальное название для этого бота, которое будет показываться Вам в админке."
              :size="'sm'"
          />

          <form-select
              name="operator_number"
              v-model="form.operator_number"
              label="Ответственный оператор"
              :options="operatorsForBots"
              :error="errors"
              :size="'sm'"
          />

          <form-multiselect
              label="Курьеры"
              name="drivers"
              v-model="selectedDrivers"
              :options="drivers_select"
              optionLabel="text"
              placeholder="Выберите курьеров"
              searchable
              @input="(drivers) => form.driver_numbers = drivers.map(d => d.value)"
              :errors="errors"
              :multiple="true"
              trackBy="value"
          />

          <form-select
              label="Логика работы бота"
              v-model="selectedBotLogic"
              :options="botLogicsForSelect"
              @change="handleBotLogic"
              description="* Логика работы бота - это набор сообщений и настроек, по которым бот будет взаимодействовать с клиентом.
                            С помощью данной настройки можно легко переключать бота в различные режимы.
                            По умолчанию в системе присутствуют лишь базовые логики."
              :error="errors"
              :size="'sm'"
          />

          <b-form-checkbox
              v-model="form.active"
              name="active"
              :value="1"
              :unchecked-value="0"
          >
            Активен
          </b-form-checkbox>

          <b-form-text>
            * В активном состоянии бот будет реагировать на комманды.
            <br>
            * В противном случае все комманды будут игнорироваться.
          </b-form-text>

<!--          <b-form-checkbox-->
<!--              v-model="form.allow_create_clients"-->
<!--              name="allow_create_clients"-->
<!--              :value="1"-->
<!--              :unchecked-value="0"-->
<!--          >-->
<!--            Разрешено добавлять новых клиентов в базу-->
<!--          </b-form-checkbox>-->

<!--          <b-form-text>-->
<!--            * При включенном состоянии бот будет добавлять в базу и обслуживать новых клиентов.-->
<!--            <br>-->
<!--            * В противном случае обслуживаться будут только те клиенты, которые уже есть в базе данных.-->
<!--          </b-form-text>-->

          <b-form-group id="button-group" class="mt-4 column">
            <div class="row justify-content-center">
              <b-button type="submit" variant="primary" class="btn" :disabled="submitting">
                {{ type }}
              </b-button>
            </div>
          </b-form-group>
        </b-form>
      </div>
    </div>
  </div>
</template>

<script>
import FormInput from '@components/ui/form/FormInput';
import FormSelect from '@components/ui/form/FormSelect';
import FormMultiselect from '@components/ui/form/FormMultiSelect';
import {
  driversComputed,
} from '@state/helpers';

export default {
  name: "Form",
  components: {FormInput, FormSelect, FormMultiselect},
  data() {
    return {
      form: {
        name: null,
        token: null,
        operator_number: null,
        logic_number: null,
        logic_type: null,
        active: false,
        allow_create_clients: false,
        driver_numbers: [],
      },
      selectedBotLogic: null,
      selectedDrivers: [],
    };
  },
  props: ['bot', 'type', 'errors', 'operatorsForBots', 'botLogicsForSelect', 'submitting'],
  computed: {
    ...driversComputed,
  },
  created() {


    if (this.bot) {
      this.form.number = this.bot.number
      this.form.name = this.bot.name;
      this.form.token = this.bot.token;
      this.form.active = this.bot.active ? 1 : 0;
      this.form.allow_create_clients = this.bot.allow_create_clients ? 1 : 0;
      this.form.operator_number = this.bot.operator ? this.bot.operator.number : null;
      this.form.driver_numbers = this.bot.driver_numbers;
      if (this.bot.logic) {
        this.form.logic_number = this.bot.logic.number;
        this.form.logic_type = this.bot.logic.type;
        this.selectedBotLogic = {
          logic_type: this.bot.logic.type,
          logic_number: this.bot.logic.number
        };
      }

      this.$store.dispatch('drivers/getSelectDriver').then(() => {
        if (this.bot.driver_numbers) {
          this.selectedDrivers = this.drivers_select.filter(d => this.bot.driver_numbers.includes(d.value));
        }
      });
    }
  },
  methods: {
    submit() {
      this.$emit('submit', this.form);
    },
    handleBotLogic({logic_number, logic_type}) {
      this.form.logic_number = logic_number;
      this.form.logic_type = logic_type;
    }
  }
};
</script>

<style lang="scss" module></style>