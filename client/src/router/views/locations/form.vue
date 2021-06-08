<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card-box row justify-content-center">
        <b-form @submit.prevent="sumbitProductType" class="col-lg-5 col-md-7 col-sm-12">

          <form-input
              name="name"
              label="Имя"
              v-model="field.name"
              :errors="errors"
              :size="'sm'"
              description="Наименование локации"
          >
          </form-input>


          <form-multiselect
              label="Курьеры"
              name="driver_number"
              v-model="selectedDrivers"
              :options="drivers_select"
              track-by="number"
              optionLabel="text"
              placeholder="Выберите курьеров"
              searchable
              @input="(drivers) => field.driver_numbers = drivers.map(d => d.value)"
              :errors="errors"
              :multiple="true"
              trackBy="value"
          />

          <b-row>
            <b-col lg="9" md="7">

              <form-input
                  name="commission_value"
                  label="Комиссия"
                  v-model="field.commission_value"
                  :errors="errors"
                  type="number"
                  :min="0"
                  :size="'sm'"
                  description="Дополнительная наценка на товары данного типа"

              >
              </form-input>

            </b-col>
            <b-col lg="3" md="5">
              <form-select
                  name="commission_type"
                  label="Тип"
                  v-model="field.commission_type"
                  :options="commission_types"
                  :errors="errors"
                  :size="'sm'"
                  description="(проценты и фикс)"
              >
              </form-select>
              <b-form-text>Тип комиссии (проценты и фикс)
              </b-form-text>
            </b-col>
          </b-row>

          <form-input
              name="priority"
              label="Приоритет"
              v-model="field.priority"
              :errors="errors"
              type="number"
              :min="0"
              :size="'sm'"
              description="Приоритет в выдаче бота"
          >
          </form-input>


          <form-select
              v-if="!isEdit"
              name="parent_number"
              label="Родительская локация"
              v-model="field.parent_number"
              :options="locationsSelect"
              :errors="errors"
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
import FormInput from '@components/ui/form/FormInput';
import FormSelect from '@components/ui/form/FormSelect';
import FormCheckbox from '@components/ui/form/FormCheckbox';
import FormMultiSelect from "@components/ui/form/FormMultiSelect";
import {commissionComputed, driversComputed} from '@state/helpers';


export default {
  name: "location-form",
  components: {FormInput, FormSelect, FormCheckbox, "form-multiselect": FormMultiSelect},
  computed: {
    ...commissionComputed,
    ...driversComputed
  },
  data() {
    return {
      field: {
        name: "",
        commission_value: 1,
        priority: 0,
        is_branch: true,
        parent_number: null,
        driver_numbers: [],
      },
      parents: [
        {
          value: null,
          text: "Нет"
        }
      ],
      selectedDrivers: []
    };
  },
  props: ['location', 'type', 'errors', 'locationsSelect', 'isEdit'],
  created() {
    this.field = {...this.location};
    this.field.driver_numbers = this.location.driver_numbers;
    this.$store.dispatch('drivers/getSelectDriver').then(() => {
      if (this.location.driver_numbers) {
        this.selectedDrivers = this.drivers_select.filter(d => this.location.driver_numbers.includes(d.value));
      }
    });
  },
  methods: {
    sumbitProductType() {
      this.$emit('sumbit-location', this.field);
    }
  },
};
</script>

<style lang="scss" module></style>