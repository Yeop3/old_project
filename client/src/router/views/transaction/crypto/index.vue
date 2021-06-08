<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <b-overlay :show="spiner_crypto" rounded="sm" no-center>
            <template v-slot:overlay>
                <div class="centered">
                    <b-spinner variant="secondary"></b-spinner>
                </div>
            </template>
            <Table
                    :crypto="crypto"
                    :spiner="spiner_crypto"
                    :select-wallet="select_wallet"
                    @filter="filter"
            />
        </b-overlay>
    </Layout>
</template>

<script>

    import {
        cryptoTransactionComputed,
        cryptoTransactionMethods
    } from "@state/helpers";
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import Table from "@views/transaction/crypto/partials/table";
    import {mapActions, mapState} from "vuex";

    export default {
        name: "index",
        page: {
            title: 'Крипто-транзакции по заказам',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, Table},
        computed: {
            ...cryptoTransactionComputed,
            ...mapState('wallet/crypto', {
                select_wallet: (state) => state.select,
            })
        },
        data() {
            return {
                title: 'Крипто-транзакции по заказам',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Крипто-транзакции по заказам',
                        active: true,
                    },
                ],
                page: 1,
            };
        },
        async created() {
            await this.getIndex({page: this.page});
            await this.getSelect();
        },
        methods: {
            ...mapActions('wallet/crypto',['getSelect']),
            ...cryptoTransactionMethods,
            filter(filterField) {
                let params = {};
                for (let value of filterField) {
                    if (value.value)
                        params[value.name] = value.value;
                }

                this.getIndex({page: 1, params});
            }
        }
    };
</script>

<style scoped>

</style>