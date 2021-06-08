<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <div class="row">
            <div class="col-md-12">
                <div class="card-box" v-if="!spiner_crypto">
                    <b-alert show variant="danger" dismissible v-if="crypt.proxy.is_working === 0">Выбранный проски не работает</b-alert>
                    <div class="card mt-2">
                        <div class="card-body">
                            <b-table
                                    class="mt-2"
                                    :items="[crypt]"
                                    :fields="fields"
                                    stacked
                            >
                                <template v-slot:cell(created_at)="data">
                                    <template v-if="data.item.created_at">
                                    {{ getHumanDate(data.item.created_at) }}
                                    </template>
                                </template>
                                <template v-slot:cell(proxy)="data">
                                    <div>
                                        <template v-if="data.item.proxy">
                                            {{data.item.proxy.ip}}:{{data.item.proxy.port}} - {{data.item.proxy.username}}
                                        </template>
                                    </div>
                                  <template v-if="data.item.proxy">
                                    <b-badge v-if="data.item.proxy.is_working === 0" variant="danger">
                                        Не работает
                                    </b-badge>
                                    <b-badge v-else variant="success">
                                        Работает
                                    </b-badge>
                                  </template>
                                </template>

                            </b-table>
                        </div>
                    </div>
                </div  >
                <div class="centered" v-else>
                    <b-spinner></b-spinner>
                </div>
            </div>
        </div>

    </Layout>
</template>

<script>

    import {cryptoWalletMethods, cryptoWalletComputed, getHumanDate} from "@state/helpers";
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import Form from "@views/wallets/crypto/partials/form";
    import {mapActions, mapState} from "vuex";

    export default {
        name: "edit-crypto",
        page: {
            title: 'Редактировать Крипто-кошелек',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, Form},
        computed: {
            ...cryptoWalletComputed,
        },
        data() {
            return {
                getHumanDate,
                title: 'Просмотр Крипто-кошелек',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Крипто-кошельки',
                        to: '/crypto-wallet/',
                    },
                    {
                        text: 'Просмотр Крипто-кошелек',
                        active: true,
                    },
                ],
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                    },
                    {
                        key: 'address',
                        label: 'Адрес для пересылки',
                    },
                    {
                        key: 'confirmations',
                        label: 'Количество подтверждений',
                    },
                    {
                        key: 'created_at',
                        label: 'Создан',
                    },
                    {
                        key: 'currency',
                        label: 'Валюта',
                    },
                    {
                        key: 'name',
                        label: 'Название',
                    },
                    {
                        key: 'proxy',
                        label: 'Прокси',
                    },

                ],
            };
        },
        async created() {
            await this.getCrypt(this.$route.params.id);
        },
        methods: {
            ...cryptoWalletMethods,
        }
    };
</script>

<style scoped>

</style>