<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Table
            :operators="operators"
            :spiner="spiner"
            @delete-by-id="deleteOperators"
            @filters="filters"
        >

        </Table>
    </Layout>
</template>

<script>
    import {operatorComputed, operatorMethods,} from "@state/helpers";
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import Table from "@views/operators/partials/table";

    export default {
        name: "index-operators",
        computed: {
            ...operatorComputed,
        },
        page: {
            title: 'Операторы',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, Table},
        created() {
            this.$store.commit('product/SET_PAGE_PRODUCTS', this.$route.query.page || 1);
            this.getIndex(this.$route.query.page || 1);
        },
        methods: {
            ...operatorMethods,
            async pagination(page) {
                await this.getIndex({page:this.page});
                this.$router.push({path: `/operators/?page=${page}`});
            },
            deleteOperators(id) {
                this.deleteById(id)
                    .then((res) => {
                        this.getIndex({page: this.page});
                    }).catch((res) => {
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                });
            },
            async filters(filterField){
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
                title: 'Операторы',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Операторы',
                        active: true,
                    },
                ],
            };
        },
    };
</script>

<style scoped>

</style>