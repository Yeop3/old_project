<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <OrderFilters
                :order-status="order_status"
                :order-sellers="order_sellers"
                :filter-fields="filterField"
                :order-counter-filter-status="orderCounterFilterStatus"
                @filters="filters"
                v-if="!spinerCounter"
        />

        <Table
                :orders="orders"
                :order-status="order_status"
                :order-sellers="order_sellers"
                :spiner="spiner_table"
                :filter-fields="filterField"
                @filters="filters"
        >
        </Table>

    </Layout>
</template>

<script>
    import {
        orderComputed,
        orderMethods,
    } from "@state/helpers";
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import Table from "@views/orders/partials/table";
    import OrderFilters from "@views/orders/partials/filter";

    export default {
        name: "index-orders",
        computed: {
            ...orderComputed,
        },
        page: {
            title: 'Все заказы',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, Table, OrderFilters},
        async mounted() {
            this.getIndex({
                page: this.$route.query.page || 1,
            });
            this.spinerCounter = true;
            await this.setCountFilterStatus();
            this.spinerCounter = false;
        },
        methods: {
            ...orderMethods,
            async pagination(page) {
                await this.getIndex({page: this.page});
                this.$router.push({path: `/orders/?page=${page}`});
            },

            async getCounter() {
                await this.setCountFilter();
                await this.setCountFilterStatus(this.$route.query.order);
            },
            filters: async function (fieldFilters) {
                let params = {};
                for (let value of fieldFilters) {
                    if (value.value) {
                        params[value.name] = value.value;
                    }
                }
                // console.log(params);
                /*this.$router.push({
                    "name": 'index-orders',
                    query
                });*/
                this.getIndex({
                    page: this.$route.query.page || 1,
                    params
                });
            },
        },
        watch: {
            // $route: 'getIndex',
        },
        data() {
            return {
                title: 'Все заказы',
                page: 1,
                filterField: [
                    {
                        name: "number",
                        value: "",
                        type: "number"

                    },
                    {
                        name: "order_sellers",
                        value: null,
                        type: "select",
                        options: [],
                    },
                    {
                        name: "order_status",
                        value: null,
                        type: "select",
                        options: [],
                    },
                    {
                        name: "order",
                        value: "all",
                        type: "select",

                    },
                ],
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Все заказы',
                        active: true,
                    },
                ],
                spinerCounter: false,
            };
        },
    };
</script>

<style scoped>

</style>