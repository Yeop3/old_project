<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Table
                :discounts="discounts"
                :page-current="currentPage"
                :spiner="spiner"
                @paginate="pagination"
                @delete-by-id="deleteProduct"
                @filters="filters"
                @sort="sort"
                :filter-params="filterParams"
        />
    </Layout>
</template>

<script>
    import {discountsComputed, discountsMethods} from "@state/helpers";
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import Table from "@views/discounts/partials/table";
    import {getFilters, saveFilters} from "@state/helpers";

    export default {
        computed: {
            ...discountsComputed,
        },
        page: {
            title: 'Скидки',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, Table},
        async created() {
            this.filterParams = getFilters(this.filterStorageKey)
            this.currentPage = this.filterParams.page || 1
            await this.getIndex({params: this.filterParams});
        },
        methods: {
            ...discountsMethods,
            async pagination(page) {
                this.currentPage = page;
                this.filterParams.page = page;

                saveFilters(this.filterStorageKey, this.filterParams)

                this.getIndex({params: this.filterParams});

            },
            deleteProduct(id) {
                this.deleteById(id)
                    .then((res) => {
                        this.getIndex({page: this.page});
                    });
            },
            async sort(params) {

                this.filterParams = {...this.filterParams, ...params, page: 1};
                this.currentPage = 1;

                saveFilters(this.filterStorageKey, this.filterParams)

                await this.getIndex({params: this.filterParams});

                },
            async filters(params) {

                this.filterParams = {...this.filterParams, ...params, page: 1};
                this.currentPage = 1;

                saveFilters(this.filterStorageKey, this.filterParams)

                await this.getIndex({params: this.filterParams});

            },
        },
        watch: {
            $route: 'getIndex'
        },
        data() {
            return {
                filterParams: {},
                filterStorageKey: 'discount_filters',
                currentPage: 1,
                title: 'Скидки',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Скидки',
                        active: true,
                    },
                ],
            };
        },
    };
</script>

<style scoped>

</style>