<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
                <template v-if="!spiner">
                    <b-form @submit.prevent="sumbitForm" class="justify-content-center">
                        <div class="justify-content-center">
                            <div class="card-box">
                                <b-row>
                                    <b-col lg="6">
                                        <form-input
                                                v-if="reservation"
                                                name="sections.reservation.reservation_time_crypto"
                                                label="Время резерва товара в крипте"
                                                v-model="reservation.reservation_time_crypto"
                                                :errors="errors"
                                                type="number"
                                                :size="'sm'"
                                        ></form-input>
                                    </b-col>
                                </b-row>

                                <b-row v-if="settingsForm">
                                    <b-col lg="6">
                                        <form-select
                                                name="sections.crypto.wallets_resolving_type"
                                                label="Способ идентификации входящих транзакций"
                                                v-model="settingsForm.wallets_resolving_type"
                                                :options="resolvingTypes"
                                                :errors="errors"
                                                type="text"
                                                :size="'sm'"
                                        >
                                            <template v-slot:description>
                                                <div style="font-size: 13px" class="mb-1">
                                                    * При способе <b>Только "Один заказ - один кошелек" кошельки</b> на каждый заказ будет предоставляться один кошелек.
                                                    Во избежании очередей необходимо иметь несколько кошельков,
                                                    уменьшить время резерва товара,
                                                    а также можно включить автоматическую отмену частично оплаченных просроченных заказов
                                                </div>

                                                <div style="font-size: 13px" class="mb-1">
                                                    * При способе <b>Только Bitaps кошельки</b> транзакция будет идентифицироваться
                                                    с помощью сервиса <a href="https://bitaps.com/" target="_blank">bitaps.com</a>.
                                                    Он предоставляет адрес для оплаты, при платеже информирует наш сервис об оплате, и после неё пересылает деньги на ваш кошелек.
                                                    <br>
                                                    <b>Bitaps взымает комисию</b>, подробнее <a href="https://developer.bitaps.com/forwarding" target="_blank">тут</a>
                                                </div>

                                                <div style="font-size: 13px">
                                                    * При способе <b>"Один зазаз - один кошелек" кошельки, Bitaps кошельки когда закончатся первые</b>
                                                    объединяются два верхних способа.
                                                    Когда кошельки со способом оплаты "Один заказ - один кошелек" заняты заказами, клиентам для оплаты начинают выдаваться кошельки Bitaps
                                                </div>
                                            </template>
                                        </form-select>
                                        <b-form-group id="button-group" class="mt-4 column">
                                            <div class="row justify-content-center">
                                                <b-button type="submit" variant="primary" class="btn-block col-4">
                                                    Сохранить
                                                </b-button>
                                            </div>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                            </div>
                        </div>
                    </b-form>
                </template>
            </div>
        </div>
    </Layout>
</template>

<script>
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import FormInput from "@components/ui/form/FormInput";
    import {mapActions, mapGetters, mapState} from "vuex";
    import FormSelect from "@components/ui/form/FormSelect";


    export default {
        name: "settings.crypto",
        components: {FormSelect, Layout, PageHeader, FormInput},
        page: {
            title: 'Крипто-Настройки',
            meta: [{name: 'description', content: appConfig.description}],
        },
        data() {
            return {
                title: 'Крипто - Настройки',
                reservation: null,
                settingsForm: null,
                errors: {},
                items: [],
                resolvingTypes: [
                    {
                        value: 'only_rotate',
                        text: `Только "Один заказ - один кошелек" кошельки`
                    },
                    {
                        value: 'only_bitaps',
                        text: `Только Bitaps кошельки`
                    },
                    {
                        value: 'rotate_and_bitaps',
                        text: `"Один заказ - один кошелек" кошельки, Bitaps кошельки когда закончатся первые`
                    },
                ]
            };
        },
        computed: {
            ...mapGetters('settings', ['settings']),
            ...mapState('settings', {
                spiner: (state) => state.spiner,
            }),
        },
        mounted() {
            this.reloadSettings();
        },
        methods: {
            ...mapActions('settings', ['loadSettings', 'updateSettings']),
            reloadSettings() {
                this.loadSettings(['crypto', 'reservation']).then(() => {
                    this.settingsForm = Object.assign({}, this.settings.crypto);
                    this.reservation = Object.assign({}, this.settings.reservation)
                });
            },
            sumbitForm() {
                this.updateSettings({sections: {
                    crypto: this.settingsForm,
                    reservation: this.reservation
                }})
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
        }
    };
</script>

<style scoped>

</style>