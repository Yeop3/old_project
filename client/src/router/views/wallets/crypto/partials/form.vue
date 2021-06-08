<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box row justify-content-center">
                <b-form @submit.prevent="sumbitForm" class="col-6">
                    <form-select
                            name="proxy_id"
                            label="Прокси"
                            v-model="field.proxy_id"
                            :errors="errors"
                            :options="proxySelect"
                            :size="'sm'"
                    >
                    </form-select>
                    <form-input
                            name="name"
                            label="Название"
                            v-model="field.name"
                            :errors="errors"
                            description="* Введите любое название (для отображения)."
                            :size="'sm'"
                    >
                    </form-input>

                    <form-input
                            name="address"
                            label="Адрес"
                            v-model="field.address"
                            :errors="errors"
                            :readonly="isUpdate"
                            :size="'sm'"
                    >
                    </form-input>

                    <form-input
                            name="confirmations"
                            label="Количество подтверждений"
                            v-model="field.confirmations"
                            :errors="errors"
                            type="number"
                    >
                    </form-input>

                    <form-select
                            name="currency"
                            label="Валюта"
                            v-model="field.currency"
                            :errors="errors"
                            :options="currencies"
                            :disabled="isUpdate"
                            :size="'sm'"
                            @change="handleCurrency"
                    />

                    <form-select
                            name="payment_type"
                            label="Метод оплаты"
                            v-model="field.payment_type"
                            :errors="errors"
                            :options="paymentTypes"
                            :disabled="isUpdate"
                            :size="'sm'"
                    />

                    <form-textarea
                            name="comment"
                            label="Комментарий"
                            v-model="field.comment"
                            :errors="errors"
                            description="* Можно указать любой комментарий."
                    >
                    </form-textarea>

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
    import FormTextarea from "@components/ui/form/FormTextarea";
    import FormSelect from "@components/ui/form/FormSelect";

    export default {
        name: "form-crypto",
        components: {FormSelect, FormTextarea, FormInput},
        props: {
            crypto: {
                type: Object,
            },
            type: {
                type: String,
            },
            errors: {
                type: Object
            },
            proxySelect: {
                type: Array
            },
            isUpdate: {
                type: Boolean,
                default: false,
            }
        },
        data() {
            return {
                availabledCurrencies: [
                    'btc',
                    'eth'
                ],
                field: {},
                currencies: [
                    {text: "Не выбрано", value: null},
                    {text: "BTC", value: "btc"},
                    {text: "BCH", value: "bch"},
                    {text: "LTC", value: "ltc"},
                    {text: "ETH", value: "eth"},
                ],

                currenciesOfOnOrderOneWallet: [
                    {text: "Не выбрано", value: null},
                    {text: "BTC", value: "btc"},
                ],

                paymentTypes: [
                    {text: "Не выбрано", value: null},
                    {text: "Один заказ - один кошелек", value: 1},
                    {text: "Bitaps", value: 2},
                ],

                paymentTypesOfNotBtc: [
                    {text: "Не выбрано", value: null},
                    {text: "Bitaps", value: 2},
                ],
            };
        },
        mounted() {
            this.field = {...this.crypto};
        },
        methods: {
            sumbitForm() {
                this.$emit('submit-form', this.field)
            },
            handleCurrency(currency) {
                if (!this.availabledCurrencies.includes(currency)) {
                    this.field.currency = null;
                    this.$bvToast.toast('Данный кошелек пока недоступен, выберите другой.', {
                        title: 'Внимание',
                        variant: 'warning',
                        autoHideDelay: 10000,
                    });
                }
            },
        },
    };
</script>

<style scoped>

</style>