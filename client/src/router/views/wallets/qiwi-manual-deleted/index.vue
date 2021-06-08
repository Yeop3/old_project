<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Table
                :qiwi-manuals-deleted="qiwiManualsDeleted"
                @restore-qiwi="restoreQiwi"
                @delete-by-id="deleteById"
                @clear-trash="clearTrash"
                v-if="!spiner"
        >
        </Table>
        <div class="centered" v-else>
            <b-spinner></b-spinner>
        </div>
    </Layout>
</template>

<script>
    import {qiwiManualDeletedComputed, qiwiManualDeletedMethods} from '@state/helpers';
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import Table from "@views/wallets/qiwi-manual-deleted/partials/table";

    export default {
        name: "index-operators",
        computed: {
            ...qiwiManualDeletedComputed,
        },
        page: {
            title: 'Корзина',
            meta: [{name: 'description', content: appConfig.description}],
        },
        mounted() {
            this.getIndexDeleted({page: this.$route.query.page});
        },
        components: {Layout, PageHeader, Table},
        methods: {
            ...qiwiManualDeletedMethods,
            async pagination(page) {
                await this.getIndexDeleted(this.page);
                this.$router.push({path: `/wallets/qiwi_manual/deleted?page=${page}`});
            },
            async restoreQiwi(id){
                try {
                    await this.restoreQiwiById(id);
                    await this.getIndexDeleted(this.page);
                }catch (res) {
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }
            },
            async deleteById(id){
                try {
                    await this.deleteByIdDeleted(id);
                    await this.getIndexDeleted(this.page);
                }catch (res) {
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }
            },
            async clearTrash(){
                try {
                    await this.qiwiDeletedClear();
                }catch (res) {
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }
                await this.getIndexDeleted(this.page);
            }
        },
        watch: {
            $route: 'getIndexDeleted'
        },
        data() {
            return {
                title: 'Корзина',
                page: 1,
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Qiwi-Кошельки для оплаты',
                        to: '/wallets/qiwi_manual',
                    },
                    {
                        text: 'Корзина',
                        active: true,
                    },
                ],
            };
        },
    };
</script>

