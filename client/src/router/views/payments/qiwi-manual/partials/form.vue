<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box row justify-content-center">
                <b-form @submit.prevent="sumbitForm" class="col-6">

                    <form-input
                            type="number"
                            name="amount"
                            label="Сумма"
                            v-model="field.amount"
                            :errors="errors"
                            :min="0"
                            :step="0.01"
                            description="* Оплаченная сумма в (грн)"
                            :size="'sm'"
                    >
                    </form-input>

                    <form-select
                        name="order_number"
                        label="Номер заказа"
                        :options="orderSelect"
                        :errors="errors"
                        v-model="field.order_number"
                        :size="'sm'"
                    ></form-select>

                    <form-phone
                        name="client_wallet"
                        v-model="field.client_wallet"
                        :errors="errors"
                        label="Кошелек клиента"
                        description="Кошелек, с которого пришла оплата (заполняется по желанию)"
                        :size="'sm'"
                    ></form-phone>

                    <form-input
                            name="comment"
                            label="Комментарий"
                            v-model="field.comment"
                            :errors="errors"
                            description="Заполняется по желанию"
                            :size="'sm'"
                    >
                    </form-input>

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
    import FormPhone from "@components/ui/form/phoneInput";

    export default {
        name: "form-qiwi-payment",
        components: {
            FormInput,
            FormSelect,
            FormPhone
        },
        props: {
            qiwiManual: {
                type: Object,
            },
            type: {
                type: String
            },
            errors: {
                type: Object,
                default: () => ({}),
            },
            orderSelect: {
                type: Array,
                default: () => ([])
            },
            orderNumber: {
                type: [Number, String],
                default: null
            }
        },
        data(){
            return {
                field : {},
            }
        },
        mounted() {
          this.field = {...this.qiwiManual};
          this.field.order_number = this.orderNumber;
        },
        methods: {
            sumbitForm(){
                this.$emit('sumbit-form', this.field);
            }
        },
    };
</script>

<style scoped>

</style>