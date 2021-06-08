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
                                    <form-checkbox
                                            v-model="settingsForm.monitoring_system_on"
                                            name="sections.common.monitoring_system_on"
                                            checked-value="1"
                                            unchecked-value="0"
                                    >
                                        Включить систему мониторинга

                                        <template v-slot:description>
                                            * Система мониторинга отслеживает состояние заказов, кошельков, проверяет
                                            оплату, высылает клиентам уведомления, отменяет неоплаченные заказы.
                                        </template>
                                    </form-checkbox>

                                    <form-input
                                            name="sections.common.monitoring_system_latency"
                                            label="Задержка между операциями мониторинга (сек)"
                                            v-model="settingsForm.monitoring_system_latency"
                                            :errors="errors"
                                            type="number"
                                    />
                                </div>
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="card-box">
                                    <form-input
                                            name="sections.common.admin_panel_name"
                                            label="Название админки"
                                            v-model="settingsForm.admin_panel_name"
                                            :errors="errors"
                                    >
                                        <template v-slot:description>
                                            * Введите название, которое будет отображено в заголовке админки.
                                        </template>
                                    </form-input>
                                </div>
                            </div>
                        </div>

                        <b-form-group class="mt-4 column">
                            <b-button type="submit" variant="primary" class="btn-block col-lg-2 col-md-3 col-sm-12">
                                Сохранить
                            </b-button>
                        </b-form-group>
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
    import FormCheckbox from "@components/ui/form/FormCheckbox";
    import {mapActions, mapGetters, mapState} from 'vuex';

    export default {
        name: "settings.common",
        computed: {
            ...mapGetters('settings', ['settings']),
            ...mapState('settings', {
                spiner: (state) => state.spiner,
            }),
        },
        page: {
            title: 'Основные настройки',
            meta: [{name: 'description', content: appConfig.description}],
        },
        data() {
            return {
                title: 'Основные настройки',
                settingsForm: null,
                errors: {},
                items: [],
            };
        },
        components: {Layout, PageHeader, FormInput, FormCheckbox},
        mounted() {
            this.reloadSettings();
        },
        methods: {
            ...mapActions('settings', ['loadSettings', 'updateSettings']),
            reloadSettings() {
                this.loadSettings(['common']).then(() => {
                    this.settingsForm = Object.assign({}, this.settings.common);
                });
            },
            sumbitForm() {
                this.updateSettings({sections: {common: this.settingsForm}})
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