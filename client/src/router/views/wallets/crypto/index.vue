<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Table
                :crypto="crypto"
                :spiner="spiner_crypto"
                @delete-crypto="tryDeleteCrypto"
                @filter="filter"
        />
    </Layout>
</template>

<script>

    import {cryptoWalletMethods, cryptoWalletComputed} from "@state/helpers";
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import Table from "@views/wallets/crypto/partials/table";

    export default {
        name: "index",
        page: {
            title: 'Крипто-кошельки',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, Table},
        computed: {
            ...cryptoWalletComputed
        },
        data() {
            return {
                title: 'Крипто-кошельки',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Крипто-кошельки',
                        active: true,
                    },
                ],
                page: 1,
            };
        },
        async created() {
            await this.getIndex({page: this.page});
        },
        methods: {
            ...cryptoWalletMethods,
            async tryDeleteCrypto(id) {
                try {
                    await this.deleteCrypto(id);
                    await this.getIndex({page: this.page});
                } catch (err) {
                    //console.log(err);
                    this.$bvToast.toast(err.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }
            },
            filter(filterField){
                let params = {...this.$route.query};
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