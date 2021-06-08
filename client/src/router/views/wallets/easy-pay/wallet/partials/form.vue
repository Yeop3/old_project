<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card-box row justify-content-center">
        <b-form @submit.prevent="sumbitForm" class="col-6">
          <form-input
              name="name"
              label="Название"
              v-model="field.name"
              :errors="errors"
              :size="'sm'"
          >
          </form-input>

          <form-input
              name="wallet_number"
              label="Номер кошелька"
              v-model="field.wallet_number"
              :errors="errors"
              :size="'sm'"
          >
          </form-input>

          <form-phone
              name="phone"
              v-model="field.phone"
              :errors="errors"
              label="Логин"
          ></form-phone>

          <form-input
              name="password"
              label="пароль"
              type="password"
              v-model="field.password"
              :errors="errors"
              :size="'sm'"
          >
          </form-input>

          <form-input
              name="limit"
              label="лимит"
              type="number"
              :step="0.01"
              v-model="field.limit"
              :errors="errors"
              :size="'sm'"
          >
          </form-input>

          <form-select
              name="proxy_id"
              label="Прокси"
              v-model="field.proxy_number"
              :errors="errors"
              :options="proxySelect"
              :size="'sm'"
          >
          </form-select>

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
import FormInput from '@components/ui/form/FormInput'
import FormSelect from '@components/ui/form/FormSelect'
import FormPhone from '@components/ui/form/phoneInput'

export default {
  name: 'global-money-form',
  components: {
    FormInput,
    FormSelect,
    FormPhone
  },
  props: {
    data: {
      type: Object,
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
    type: {
      type: String
    },
    typeSelect: {
      type: Array
    },
    proxySelect: {
      type: Array
    },
  },
  watch: {

  },
  data () {
    return {
      field: {},
    }
  },
  mounted () {
   // console.log(this.data);
    this.field = { ...this.data }
  },
  methods: {
    sumbitForm () {
      this.$emit('on-submit', this.field)
    }
  }
}
</script>

<style scoped>

</style>