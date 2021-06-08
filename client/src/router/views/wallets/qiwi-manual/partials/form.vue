<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box row justify-content-center">
                <b-form @submit.prevent="sumbitForm" class="col-6">
                    <FormTel
                            v-if="!isUpdate"
                            name="phone"
                            label="Телефон"
                            v-model="field.phone"
                            :errors="errors"
                    />


                    <form-input
                            name="min_paid_orders_count"
                            label="Минимальное количество оплаченных заказов на счету клиентов для выдачи им данного кошелька"
                            v-model="field.min_paid_orders_count"
                            :errors="errors"
                            type="number"
                            :min="0"
                            description="* 0 - кошелек может выдаваться любым клиентам."
                            :size="'sm'"
                    >
                    </form-input>


                    <form-input
                            name="note"
                            label="Заметка"
                            v-model="field.note"
                            :errors="errors"
                            description="* Можно указать любой комментарий для кошелька"
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
                            Активен
                        </b-form-checkbox>
                        <b-form-text>* В активном состоянии этот кошелек сможет выдаваться клиенту для оплаты. В
                            противном случае кошелек клиенту не выдается.
                        </b-form-text>
                    </b-form-group>

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
    import FormTel from "@components/ui/form/phoneInput";

    export default {
        name: "form-product",
        components: {FormInput, FormSelect, FormTel},
        computed: {},
        props: {
            qiwiManual: {
                type: Object,
                default: () => {
                }
            },
            errors: {
                type: Object
            },
            type: {
                type: String
            },
            isUpdate: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                field: {},

            };
        },
        created() {
            this.field = {...this.qiwiManual};
        },
        methods: {
            sumbitForm() {
                this.$emit('sumbit-form', this.field);
            }
        },
    };
</script>

<style scoped>

</style>