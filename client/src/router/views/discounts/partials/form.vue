<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box row justify-content-center">
                <b-form @submit.prevent="sumbitForm" class="col-lg-6 col-md-8 col-sm-12">

                    <form-input
                            name="name"
                            label="Название"
                            v-model="field.name"
                            :errors="errors"
                            description="* Придумайте название для скидки."
                            :size="'sm'"
                    >
                    </form-input>

                    <form-input
                            name="discount_value"
                            label="Размер скидки, %"
                            v-model="field.discount_value"
                            :errors="errors"
                            description="* Допускаются значения от 0.1 до 99,9."
                            type="number"
                            :min="0.1"
                            :max="99.9"
                            :step="0.1"
                            :size="'sm'"
                    >
                    </form-input>

                    <form-input
                            name="discount_value"
                            label="Приоритет"
                            v-model="field.discount_priority"
                            :errors="errors"
                            description="* При распространении нескольких скидок на один и тот же заказ, будет применена
                             та скидка, приоритет которой выше. При равных приоритетах будет выбрана скидка с максимальным
                             размером. Учитывается также персональная скидка клиента. По умолчанию приоритет скидки равен 100.
                              Вы можете повысить либо понизить данное значение."
                            type="number"
                            :min="0"
                            :step="1"
                            :size="'sm'"
                    >
                    </form-input>

                    <b-form-group
                            id="date_start"
                            label="Начало действия"
                    >
                        <b-input-group class="">
                            <b-input-group-prepend is-text>
                                <b-icon-calendar></b-icon-calendar>
                            </b-input-group-prepend>
                            <datetime
                                    v-model="field.date_start"
                                    type="datetime"
                                    input-class="form-control text-break text-wrap bg-transparent h-auto text-muted"
                                    :phrases="phrases"
                                    :auto="true"
                                    hidden-name="date_start"
                            ></datetime>
                            <b-input-group-append>
                                <b-button variant="danger" @click="field.date_start = null">X</b-button>
                            </b-input-group-append>
                        </b-input-group>
                        <b-form-invalid-feedback :id="'date_start-feedback'" v-if="errors.hasOwnProperty('date_start')">
                              <span :key="i" v-for="(error, i) in errors['date_start']">
                                {{error}}
                              </span>
                        </b-form-invalid-feedback>
                        <b-form-text>Если оставить пустым, то скидка будет активна в любой момент времени.</b-form-text>
                    </b-form-group>

                    <b-form-group
                            id="date_end"
                            label="Конец действия"
                    >
                        <b-input-group class="">
                            <b-input-group-prepend is-text>
                                <b-icon-calendar></b-icon-calendar>
                            </b-input-group-prepend>
                            <datetime
                                    v-model="field.date_end"
                                    type="datetime"
                                    input-class="form-control text-break text-wrap bg-transparent h-auto text-muted"
                                    :phrases="phrases"
                                    :state="errors['date_end'] === undefined ? null : false"
                                    hidden-name="date_end"
                            ></datetime>
                            <b-input-group-append>
                                <b-button variant="danger" @click="field.date_end = null">X</b-button>
                            </b-input-group-append>
                        </b-input-group>
                        <b-form-invalid-feedback :id="'date_end-feedback'">
                              <span :key="i" v-for="(error, i) in errors['date_end']">
                                {{error}}
                              </span>
                        </b-form-invalid-feedback>
                        <b-form-text>Если оставить пустым, то скидка будет активна все время.</b-form-text>
                    </b-form-group>

                    <b-form-group
                            label="Локации"
                    >
                        <Multiselect
                                v-model="location_numbers"
                                :options="locationSelect"
                                :multiple="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :preserve-search="true"
                                placeholder=""
                                label="text"
                                track-by="value"
                                :state="errors['location_numbers'] === undefined ? null : false"
                                searchable
                                selectLabel="Нажмите Enter, чтобы выбрать"
                                selectGroupLabel="Нажмите Enter, чтобы выбрать группу"
                                selectedLabel="Выбрано"
                                deselectLabel="Нажмите Enter, чтобы удалить"
                                deselectGroupLabel="Нажмите Enter, чтобы отменить выбор группы"
                        >
                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span
                                    class="multiselect__single" v-if="values.length && !isOpen">{{ values.length }} выбрано</span>
                            </template>
                        </Multiselect>
                        <b-form-invalid-feedback :id="'location_numbers-feedback'">
                              <span :key="i" v-for="(error, i) in errors['location_numbers']">
                                {{error}}
                              </span>
                        </b-form-invalid-feedback>
                        <b-form-text>Скидка распространяется только на выбранные локации.</b-form-text>
                    </b-form-group>

                    <b-form-group
                            label="Товары"
                    >
                        <Multiselect
                                v-model="product_type_numbers"
                                :options="productTypesSelect"
                                :multiple="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :preserve-search="true"
                                placeholder=""
                                label="text"
                                track-by="value"
                                name="product_type_numbers"
                                :state="errors['product_type_numbers'] === undefined ? null : false"
                                searchable
                                hideSelected
                                selectLabel="Нажмите Enter, чтобы выбрать"
                                selectGroupLabel="Нажмите Enter, чтобы выбрать группу"
                                selectedLabel="Выбрано"
                                deselectLabel="Нажмите Enter, чтобы удалить"
                                deselectGroupLabel="Нажмите Enter, чтобы отменить выбор группы"
                        >
                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span
                                    class="multiselect__single" v-if="values.length && !isOpen">{{ values.length }} выбрано</span>
                            </template>
                        </Multiselect>
                        <b-form-invalid-feedback :id="'product_type_numbers-feedback'">
                              <span :key="i" v-for="(error, i) in errors['product_type_numbers']">
                                {{error}}
                              </span>
                        </b-form-invalid-feedback>
                        <b-form-text>Скидка распространяется только на выбранные товары.</b-form-text>
                    </b-form-group>

                    <form-input
                            name="client_min_paid_orders_count"
                            label="Минимальное количество оплаченных заказов у клиента"
                            v-model="field.client_min_paid_orders_count"
                            :errors="errors"
                            description="* Если указать значение > 0, то скидка будет применяться только для тех клиентов,
                             у которых количество оплаченных заказов будет больше либо равно указанному."
                            type="number"
                            :min="0"
                            :size="'sm'"
                    >
                    </form-input>

                    <form-input
                            name="client_min_income"
                            label="Минимальный приход у клиента, грн"
                            v-model="field.client_min_income"
                            :errors="errors"
                            description="* Если указать значение > 0, то скидка будет применяться только для тех клиентов,
                             у которых приход по заказам выше либо равен указанной сумме."
                            type="number"
                            :min="0"
                            :size="'sm'"
                    >
                    </form-input>

                    <b-form-group>
                        <b-form-checkbox
                                v-model="field.active"
                                name="active"
                                :value="true"
                                :unchecked-value="false"
                        >
                            Активна
                        </b-form-checkbox>
                        <b-form-text>* Скидка будет работать только если она активна.
                        </b-form-text>
                    </b-form-group>

                    <form-textarea
                            name="description"
                            v-model="field.description"
                            label="Описание"
                            description="* Можете вписать любой комментарий для своего удобства."
                    ></form-textarea>

                    <b-form-group id="button-group" class="mt-4 column">
                        <div class="row justify-content-center">
                            <b-button type="submit" variant="primary" class="btn-block col-4">
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
    import FormTextarea from "@components/ui/form/FormTextarea";
    import Multiselect from "vue-multiselect";
    import {Datetime} from 'vue-datetime';
    import {commissionComputed} from "@state/helpers";
    import {BIcon, BIconCalendar} from 'bootstrap-vue';

    export default {
        name: "form-product",
        components: {
            FormInput,
            FormSelect,
            Multiselect,
            FormTextarea,
            datetime: Datetime,
            BIcon,
            BIconCalendar
        },
        computed: {
            ...commissionComputed,
        },
        props: {
            discount: {
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
            productTypesSelect: {
                type: Array,
            },
            locationSelect: {
                type: Array,
            },
        },
        data() {
            return {
                field: {},
                product_type_numbers: [],
                location_numbers: [],
                phrases: {ok: 'Выбрать', cancel: 'Отмена'}
            };
        },
        created() {
            this.field = {...this.discount};
            if (this.field.product_types) {
                this.product_type_numbers = this.field.product_types.map(value => (
                    {
                        value: value.number,
                        text: value.name,
                    }
                ));
            }
            if (this.field.locations) {
                this.location_numbers = this.field.locations.map(value => ({
                    value: value.number,
                    text: value.name_chain
                }));
            }
            if (this.field.date_start) {
                this.field.date_start = new Date(this.field.date_start).toISOString();
            }
            if (this.field.date_end) {
                this.field.date_end = new Date(this.field.date_end).toISOString();
            }
        },
        methods: {
            sumbitForm() {
                //console.log(this.location_numbers);
                this.field.location_numbers = this.location_numbers.map(value => (value.value));
                this.field.product_type_numbers = this.product_type_numbers.map(value => (value.value));
                this.$emit('sumbit-form', this.field);
            }
        },
    };
</script>

<style src="vue-datetime/dist/vue-datetime.css"></style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<style lang="scss">
    .multiselect{
        min-height: 31px !important;
    }

    .multiselect__select{
        height: 31px !important;
    }

    .multiselect__tag{
        min-height: 31px !important;
        padding-top: 0;
    }

    .multiselect__input{
        margin-top: 3px;
    }

    .multiselect__single{
        margin-bottom: 0;
        margin-top: 3px;
        font-weight: 400;
        font-size: 0.875rem;
    }
</style>
