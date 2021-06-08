<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Table
                :qiwi-manuals="qiwiManuals"
                :qiwi-manual-phone="qiwiManualPhone"
                :spiner="spiner"
                @filters="filters"
                v-if="!spinerAll"
        >
        </Table>
        <div class="centered" v-else>
            <b-spinner></b-spinner>
        </div>
    </Layout>
</template>

<script>
    import {paymentQiwiManualDeletedMethods, paymentqiwiManualDeletedComputed,} from "@state/helpers";
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import Table from "@views/payments/qiwi-manual/partials/table";
    import store from "@state/store";

    export default {
        name: "index-operators",
        computed: {
            ...paymentqiwiManualDeletedComputed,
        },
        mounted() {
            this.spinerAll = true;
            Promise.all([
                this.$store.dispatch('payments/qiwiManual/getIndex', {page: this.$route.query.page}),
                this.$store.dispatch('payments/qiwiManual/getSelectPhone'),
            ]).finally(() => this.spinerAll = false);
        },
        page: {
            title: 'Ручные Qiwi-Оплаты',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, Table},
        methods: {
            ...paymentQiwiManualDeletedMethods,
            async pagination(page) {
                await this.getIndex({page: page});
                this.$router.push({path: `/payments/qiwi_manual/?page=${page}`});
            },
            async deleteOperators(id) {
                try {
                    await this.deleteById(id);
                    await this.getIndex(this.page);
                } catch (res) {
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }

            },
            async filters(filterField) {
                let params = {};
                for (let value of filterField) {
                    if (value.value)
                        params[value.name] = value.value;
                }
                await this.getIndex({page: this.page, params});
            }
        },
        watch: {
            $route: 'getIndex'
        },
        data() {
            return {
                title: 'Ручные Qiwi-Оплаты',
                spinerAll: false,
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Ручные Qiwi-Оплаты',
                        active: true,
                    },
                ],
            };
        },
    };
</script>

