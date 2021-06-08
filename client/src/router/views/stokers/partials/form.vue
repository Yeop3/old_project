<template>
  <div class="row">
    <div class="col-md-12">
      <div class="card-box row justify-content-center">
        <b-form @submit.prevent="sumbitForm" class="col-lg-6 col-md-7 col-sm-12">

          <form-select
              name="source_number"
              v-model="field.source_number"
              :options="botsSelect"
              label="Бот"
              :errors="errors"
              :size="'sm'"
          >
          </form-select>

          <form-multiselect
              label="Клиент"
              name="client_number"
              v-model="selectedClient"
              :options="clientsSelect"
              track-by="number"
              optionLabel="label"
              placeholder="Выберите клиента"
              searchable
              @input="(clientSelect) => field.client_number = clientSelect ? clientSelect.number : null"
              :errors="errors"
          />

          <form-select
              name="location_number"
              v-model="field.location_number"
              :options="locationsSelect"
              label="Локация"
              :errors="errors"
              :size="'sm'"
          >

          </form-select>

          <form-select
              name="product_type_number"
              v-model="field.product_type_number"
              :options="productTypesSelect"
              label="Продукт"
              :errors="errors"
              :size="'sm'"
          >

          </form-select>

          <b-form-group id="button-group" class="mt-4 column">
            <div class="row justify-content-center">
              <b-button type="submit"  variant="primary" class="btn-block col-4" :disabled="submitting">
                {{type}}
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
	import FormSelect from "@components/ui/form/FormSelect";
	import Multiselect from "vue-multiselect";
	import {botsComputed, commissionComputed} from "@state/helpers";
	import FormTextarea from "@components/ui/form/FormTextarea";
	import FormMultiselect from '@components/ui/form/FormMultiSelect';
	import {BIcon, BIconTrash} from 'bootstrap-vue';

	export default {
		name: "form-stoker",
		components: {FormTextarea, FormInput, FormSelect, Multiselect, BIcon, BIconTrash, FormMultiselect},
		computed: {
			...botsComputed,
		},
		props: {
      stoker:{
        type: Object,
        default: () => {}
      },
      clientsSelect: {
      	type: Array,
				default: () => []
			},
      errors: {
      	type: Object
      },
      type: {
      	type: String
      },
			locationsSelect: {
				type: Array,
				default: () => []
			},
			productTypesSelect: {
				type: Array,
				default: () => [],
			},
			botsSelect: {
				type: Array,
				default: () => [],
			},
			submitting: {
				type: Boolean,
				default: false
			},
		},
		data() {
			return {
				field: {
					source_number: null,
					client_number: null,
					location_number: null,
					product_type_number: null
        },
				selectedClient: null,
			}
		},
		created() {
			if (this.stoker) {
				this.field.source_number = this.stoker.source.number ;
				this.selectedClient = this.stoker.client;
				this.field.client_number = this.stoker.client.number;
				this.field.location_number = this.stoker.location.number;
				this.field.product_type_number = this.stoker.product_type.number;
				this.field.number = this.stoker.number;
				console.log(this.stoker);
			}
		},
		methods: {
			sumbitForm() {
				console.log(this.field);

				this.$emit('submit-form', this.field);
			},
		}
	};
</script>


<style lang="scss">
</style>