<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card-box row justify-content-center">
        <b-form @submit.prevent="sumbitForm" class="col-6">

          <form-input
              name="name"
              label="Имя"
              v-model="field.name"
              :errors="errors"
              :size="'sm'"
          >
          </form-input>

          <form-multiselect
              label="Клиент"
              name="client_number"
              v-model="selectedClient"
              :options="clients_for_select"
              track-by="number"
              optionLabel="label"
              placeholder="Выберите клиента"
              searchable
              @input="(client) => field.client_number = client ? client.number : null"
              :errors="errors"
          />

          <!--                  <form-input-->
          <!--                      name="email"-->
          <!--                      type="email"-->
          <!--                      label="Email"-->
          <!--                      v-model="field.email"-->
          <!--                      :errors="errors"-->
          <!--                      :size="'sm'"-->
          <!--                  >-->
          <!--                  </form-input>-->


          <!--                  <form-input-->
          <!--                      name="password"-->
          <!--                      type="password"-->
          <!--                      label="Пароль"-->
          <!--                      v-model="field.password"-->
          <!--                      :errors="errors"-->
          <!--                      :size="'sm'"-->
          <!--                  >-->
          <!--                  </form-input>-->


          <b-form-group id="button-group" class="mt-4 column">
            <div class="row justify-content-center">
              <b-button type="submit" variant="primary" class="btn-block col-4">
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
import FormInput from "@components/ui/form/FormInput";
import FormMultiselect from '@components/ui/form/FormMultiSelect';
import {
  clientsComputed,
  clientsMethods,
} from '@state/helpers';

export default {
  name: "form-product",
  components: {FormInput, FormMultiselect},
  computed: {
    ...clientsComputed,
  },
  props: {
    operator: {
      type: Object,
      default: () => {
      }
    },
    type: {
      type: String,
    },
    errors: {
      type: Object
    },
  },
  data() {
    return {
      field: {},
      selectedClient: null,
    };
  },
  created() {
    this.field = {...this.operator};
    this.field.client_number = this.operator.client ? this.operator.client.number : null;
    this.loadClientsForSelect().then(() => {
      if (this.operator.client) {
        this.selectedClient = this.clients_for_select.find(c => c.number === this.operator.client.number);
      }
    });
  },
  methods: {
    ...clientsMethods,
    sumbitForm() {
      this.$emit('sumbit-form', this.field);
    }
  },
};
</script>

<style scoped>

</style>