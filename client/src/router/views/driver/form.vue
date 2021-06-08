<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card-box row justify-content-center">
        <b-form @submit.prevent="onSubmit" class="col-lg-6 col-md-6 col-sm-12">
          <form-input
              name="name"
              label="Имя"
              v-model="form.name"
              :errors="errors"
              :size="'sm'"
          >
          </form-input>

          <form-multiselect
              label="Клиент"
              name="client_number"
              v-model="form.client_number"
              :options="clientList"
              optionLabel="label"
              track-by="number"
              placeholder="Выберите клиента"
              searchable
              :errors="errors"
          />



          <b-form-group v-if="!isCreate" label="Возможности:">
            <b-form-checkbox-group
                id="permissions"
                name="permissions"
                v-model="form.permissions"
                :options="permissionTypes"
                :state="errors['permissions'] === undefined ? null : false"
            ></b-form-checkbox-group>

            <b-form-invalid-feedback :state="errors['permissions'] === undefined ? null : false"
                                     :id="'permissions-feedback'">
              <span :key="i" v-for="(error, i) in errors['permissions']">
                {{ error }}
              </span>
            </b-form-invalid-feedback>
          </b-form-group>

          <form-multiselect
              label="Локации"
              name="location_numbers"
              v-model="form.location_numbers"
              :options="listLocations"
              track-by="value"
              optionLabel="text"
              searchable
              multiple
              :close-on-select="false"
              :errors="errors"
          />

          <b-form-group id="button-group" class="mt-4 column">
            <div class="row justify-content-center">
              <b-button type="submit" variant="primary" class="btn">
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
import FormMultiselect from '@components/ui/form/FormMultiSelect';
import {mapActions, mapState} from 'vuex'

export default {
  components: {FormInput, FormMultiselect},
  data() {
    return {
      listLocations: [],
      form: {
        name: "",
        client_number: null,
        permissions: [],
        location_numbers: [],
      },
      selectedClient: null,
    };
  },
  props: ['driver', 'type', 'errors', 'permissionTypes', 'isCreate'],
  computed: {
    ...mapState('location', {
      locationList: 'locations_select'
    }),
    ...mapState('clients', {
      clientList: 'clients_for_select'
    })
  },
  async created() {
    await this.getClientList()
    await this.getLocationList()

    this.listLocations = [{text: 'Все', value: null}, ...this.locationList.filter(l => l.value)]
    if (!this.driver) return;
    this.form.name = this.driver.name || null;
    this.form.client_number = this.driver.client || null;
		this.form.permissions = this.driver.permissions || [];

		this.form.location_numbers = this.driver.locations.map(l => {
      const locationSelect = this.locationList.find(loc => loc.value === l.number)
      return {
        text: locationSelect.text,
        value: locationSelect.value
      }
    })
  },
  methods: {
    ...mapActions('location', {
      getLocationList: 'getSelect'
    }),
    ...mapActions('clients', {
      getClientList: 'loadClientsForSelect'
    }),
    onSubmit() {
      const preparedForm = {...this.form}
      preparedForm.client_number = this.form.client_number.number
      preparedForm.location_numbers = this.form.location_numbers.filter(l => l.value).map(l => l.value)
      this.$emit('sumbit-driver', preparedForm);
    }
  }
};
</script>

<style lang="scss" module></style>