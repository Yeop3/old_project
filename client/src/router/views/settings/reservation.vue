<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
                <template v-if="!spiner">
                    <b-form v-if="settingsForm" @submit.prevent="sumbitForm">
                        <div class="row justify-content-between">
                            <div class="col-md-6 col-sm-12">
                                <div class="card-box">
                                    <form-select
                                            name="sections.reservation.reservation_type"
                                            label="Тип резервирования товара"
                                            v-model="settingsForm.reservation_type"
                                            :options="[
                                            {value: 'old', text: 'Резервировать наиболее старый товар'},
                                            {value: 'random', text: 'Резервировать случайный товар'},
                                        ]"
                                            :size="'sm'"
                                    >
                                    </form-select>

                                    <form-checkbox
                                            v-model="settingsForm.auto_cancel_partially_paid_orders"
                                            name="sections.reservation.auto_cancel_partially_paid_orders"
                                            checked-value="1"
                                            unchecked-value="0"
                                    >
                                        Автоматически отменять частично оплаченные просроченные заказы

                                        <template v-slot:description>
                                            * В выключенном состоянии частично оплаченные заказы при окончании времени
                                            резерва отменяться не будут, а будут ждать полной оплаты либо ручной отмены
                                            оператором.
                                        </template>
                                    </form-checkbox>

<!--                                    <form-input-->
<!--                                            name="sections.reservation.reservation_time_qiwi"-->
<!--                                            v-model="settingsForm.reservation_time_qiwi"-->
<!--                                            label="Время резерва товара в Qiwi (минут)"-->
<!--                                            :errors="errors"-->
<!--                                            type="number"-->
<!--                                    />-->

                                    <form-input
                                            name="sections.reservation.reservation_time_qiwi_manual"
                                            v-model="settingsForm.reservation_time_qiwi_manual"
                                            label="Время резерва товара в ручном режиме обработки заказов в Qiwi (минут)"
                                            :errors="errors"
                                            type="number"
                                            :size="'sm'"

                                    />
                                    <form-input
                                            name="sections.crypto.reservation_crypto"
                                            v-model="settingsForm.reservation_time_crypto"
                                            label="Время резерва товара в Крипте"
                                            :errors="errors"
                                            type="number"
                                            :size="'sm'"

                                    />
                                    <form-input
                                            name="sections.crypto.reservation_kuna"
                                            v-model="settingsForm.reservation_time_kuna"
                                            label="Время резерва товара в Kuna"
                                            :errors="errors"
                                            type="number"
                                            :size="'sm'"

                                    />
                                    <form-input
                                            name="sections.crypto.reservation_global_money_card"
                                            v-model="settingsForm.reservation_time_global_money_card"
                                            label="Время резерва товара в GlobalMoney Card"
                                            :errors="errors"
                                            type="number"
                                            :size="'sm'"

                                    />
                                    <form-input
                                            name="sections.crypto.reservation_global_money_online"
                                            v-model="settingsForm.reservation_time_global_money_online"
                                            label="Время резерва товара в GlobalMoney Online"
                                            :errors="errors"
                                            type="number"
                                            :size="'sm'"

                                    />
                                    <form-input
                                            name="sections.crypto.reservation_global_money_terminal"
                                            v-model="settingsForm.reservation_time_global_money_terminal"
                                            label="Время резерва товара в GlobalMoney Terminal"
                                            :errors="errors"
                                            type="number"
                                            :size="'sm'"

                                    />
                                    <form-input
                                            name="sections.crypto.reservation_easy_pay_online"
                                            v-model="settingsForm.reservation_time_easy_pay_online"
                                            label="Время резерва товара в EasyPay Online"
                                            :errors="errors"
                                            type="number"
                                            :size="'sm'"

                                    />
                                    <form-input
                                            name="sections.crypto.reservation_easy_pay_terminal"
                                            v-model="settingsForm.reservation_time_easy_pay_terminal"
                                            label="Время резерва товара в EasyPay Terminal"
                                            :errors="errors"
                                            type="number"
                                            :size="'sm'"

                                    />
                                    <b-form-group id="button-group" class="mt-4 column">
                                        <div class="row justify-content-center">
                                            <b-button type="submit" variant="primary" class="btn-block col-4">
                                                Сохранить
                                            </b-button>
                                        </div>
                                    </b-form-group>
                                </div>
                            </div>
                        </div>
                    </b-form>
                </template>
                <div class="centered" v-else>
                    <b-spinner></b-spinner>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import FormInput from "@components/ui/form/FormInput";
    import FormSelect from "@components/ui/form/FormSelect";
    import FormCheckbox from "@components/ui/form/FormCheckbox";
    import {mapActions, mapGetters, mapState} from 'vuex';

    export default {
        name: "settings.reservation",
        computed: {
            ...mapGetters('settings', ['settings']),
            ...mapState('settings', {
                spiner: (state) => state.spiner,
            }),
        },
        page: {
            title: 'Настройки резервирования',
            meta: [{name: 'description', content: appConfig.description}],
        },
        data() {
            return {
                title: 'Настройки резервирования',
                settingsForm: null,
                errors: {},
                items: [],
            };
        },
        components: {Layout, PageHeader, FormInput, FormSelect, FormCheckbox},
        mounted() {
            this.reloadSettings();
        },
        methods: {
            ...mapActions('settings', ['loadSettings', 'updateSettings']),
            reloadSettings() {
                this.loadSettings(['reservation']).then(() => {
                    this.settingsForm = Object.assign({}, this.settings.reservation);
                });
            },
            sumbitForm() {
                this.updateSettings({sections: {reservation: this.settingsForm}})
                    .then(() => {
                        this.$bvToast.toast('Сохранено', {
                            variant: 'success',
                            autoHideDelay: 5000,
                        });

                        this.reloadSettings();
                    })
                    .catch((res) => {
                        this.errors = res.response.data.errors;
                        this.$bvToast.toast(res.response.data.message, {
                            title: 'Errors',
                            variant: 'danger',
                            autoHideDelay: 5000,
                        });
                    });
            },
        },
    };
</script>