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
          >
          </form-input>

          <form-input
              name="price"
              label="Цена"
              v-model="field.price"
              :errors="errors"
              type="number"
              :min="1"
              :step="0.01"
              :size="'sm'"
          >
          </form-input>

          <b-row>
            <b-col lg="8">
              <form-input
                  name="commission_value"
                  label="Комиссия"
                  v-model="field.commission_value"
                  :errors="errors"
                  type="number"
                  :min="0"
                  description="Дополнительная наценка на товары данного типа"
                  :size="'sm'"
              >
              </form-input>
            </b-col>
            <b-col lg="4">
              <form-select
                  name="commission_type"
                  label="Тип"
                  v-model="field.commission_type"
                  :options="commission_types"
                  :errors="errors"
                  :size="'sm'"
              >
              </form-select>
            </b-col>
          </b-row>

          <b-row>
            <b-col lg="8">
              <form-input
                  name="packing"
                  label="Количество"
                  v-model="field.packing"
                  :errors="errors"
                  type="number"
                  :min="1"
                  :step="0.001"
                  :size="'sm'"
              >
              </form-input>
            </b-col>
            <b-col lg="4">
              <form-select
                  name="unit"
                  label="Размерность"
                  v-model="field.unit"
                  :options="unit_types"
                  :errors="errors"
                  :size="'sm'"
              >
              </form-select>
            </b-col>
          </b-row>

          <form-multiselect
              label="Локации"
              name="locations"
              v-model="selectedLocations"
              :options="locations_root_select"
              optionLabel="text"
              placeholder="Выберите локации"
              searchable
              @input="(locations) => field.location_numbers = locations.map(l => l.value)"
              :errors="errors"
              :multiple="true"
              trackBy="value"
          />

          <b-form-group label="Методы оплаты:">
            <b-form-checkbox-group
                id="payment_methods"
                name="payment_methods"
                v-model="field.payment_methods"
                :options="paymentMethods"
                :state="errors['payment_methods'] === undefined ? null : false"
            ></b-form-checkbox-group>

            <b-form-invalid-feedback :state="errors['payment_methods'] === undefined ? null : false" :id="'payment_methods-feedback'">
              <span :key="i" v-for="(error, i) in errors['payment_methods']">
                {{error}}
              </span>
            </b-form-invalid-feedback>
          </b-form-group>

          <b-form-checkbox
              v-model="field.active"
              name="active"
              :value="1"
              :unchecked-value="0"
          >
            Активен
          </b-form-checkbox>

          <b-form-text>
            * В активном состоянии бот будет отображать данный бот в списке продуктов.
          </b-form-text>

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
import FormMultiselect from '@components/ui/form/FormMultiSelect';
import {
  commissionComputed,
  unitComputed,
  paymentMethodComputed,
  paymentMethods as paymentStoreMethods,
  locationsComputed,
  locationsMethods,
} from '@state/helpers';

export default {
  name: "formProductTypes",
  components: {FormInput, FormSelect, FormMultiselect},
  computed: {
    ...commissionComputed,
    ...unitComputed,
    ...paymentMethodComputed,
    ...locationsComputed,
  },
  data() {
    return {
      field: {
        name: "",
        price: 1,
        commission_type: 1,
        packing: 1,
        unit: 1,
        commission_value: 0,
        payment_methods: [],
        location_numbers: [],
      },
      selectedLocations: [],
    };
  },
  props: ['productType', 'type', 'errors'],
  created() {
    this.field = {...this.productType};
    if (this.type !== 'Сохранить') {
      this.field.commission_value = 0;
      this.field.commission_type = this.commission_types[0].value;
      this.field.active = 0;
    }

    this.getSelect().then(() => {
    	console.log(this.locations_root_select);
      if (this.type !== 'Создать') {
        this.selectedLocations = this.locations_root_select.filter(l => this.field.location_numbers.includes(l.value));
      }
    });
    this.loadPaymentMethods().then((paymentMethods) => {
      if (this.type === 'Создать') {
        this.field.payment_methods = paymentMethods.map(p => p.value);
        this.field = Object.assign({}, this.field);
      }
    });
  },
  methods: {
    ...paymentStoreMethods,
    ...locationsMethods,
    sumbitProductType() {
      this.$emit('sumbit-product-type', this.field);
    }
  },
};
</script>

<style scoped>

</style>