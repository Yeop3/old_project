<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box" v-if="!spiner">
                    <div class="card mt-2">
                        <div v-if="proxy" class="card-body">
                            <h1 class="mb-3"></h1>
                            <b-button class="mr-2" @click="$router.push({name: 'proxies.edit', params: {number: proxy.number}})" variant="primary">
                                <i class="remixicon-pencil-line mr-1"></i>
                                Изменить
                            </b-button>
                            <b-button class="btn" @click="tryToDeleteProxy(proxy.number)" variant="danger">
                                <i class="remixicon-delete-bin-6-line pl-1"></i>
                                Удалить
                            </b-button>
                            <b-table
                                    class="mt-3"
                                    stacked
                                    :items="[proxy]"
                                    :fields="fields"
                            >
                                <template v-slot:cell(ip_port)="data">
                                    {{data.item.ip}} : {{data.item.port}}
                                </template>

                                <template v-slot:cell(is_working)="data">
                                    <b-badge v-if="data.item.is_working === 0" variant="danger">
                                        Не работает
                                    </b-badge>
                                    <b-badge v-else variant="success">
                                        Работает
                                    </b-badge>
                                </template>
                            </b-table>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mb-3" v-else>
                    <b-spinner></b-spinner>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
    import appConfig from '@src/app.config'
    import Layout from '@layouts/main'
    import PageHeader from '@components/page-header'
    import {proxyMethods, proxyComputed} from "@state/helpers";

    export default {
        name: "proxies.show",
        page: {
            title: 'Прокси',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader},
        data() {
            return {
                title: 'Прокси',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Прокси',
                        to: '/proxies',
                    },
                    {
                        text: 'Карточка прокси',
                        active: true,
                    }
                ],
                fields: [

                    {
                        key: 'proxy_type',
                        label: 'Тип',
                    },
                    {
                        key: 'ip_port',
                        label: 'IP/Host : Порт',
                    },
                    {
                        key: 'note',
                        label: 'Заметка',
                    },
                    {
                        key: 'country',
                        label: 'Страна',
                    },
                    {
                        key: 'is_working',
                        label: 'Состояние'
                    }
                ],
            }
        },
        computed:{
            ...proxyComputed,
        },
        mounted() {
            this.loadProxy(this.$route.params.number);
        },
        methods:{
            ...proxyMethods,
            async tryToDeleteProxy(number) {
                try {
                    const value = await this.$bvModal.msgBoxConfirm('Действительны ли вы хотите это удалить?', {
                        title: 'Пожалуйста подтвердите',
                        size: 'sm',
                        buttonSize: 'sm',
                        okVariant: 'danger',
                        okTitle: 'Удалить',
                        cancelTitle: 'Отмена',
                        footerClass: 'p-2',
                        hideHeaderClose: false,
                        centered: true
                    });
                    if (value) {
                        this.deleteProxy(number).then((res) => {
                            this.$router.push({name: 'proxies.index'});
                        });
                    }
                } catch (e) {
                }
            },
        }
    };
</script>

<style>
    .table.b-table.b-table-stacked > tbody > tr > :first-child{
        border-top-width: 1px !important;
    }
    .table.b-table.b-table-stacked > tbody > tr > :last-child{
        border-bottom: 1px solid #dee2e6 !important;
    }
</style>