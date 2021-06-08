<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Table
                :drivers="drivers"
                :spiner="spiner"
                :page-current="currentPage"
                :filterParams="filterParams"
                @paginate="pagination"
                @delete-by-id="deleteDriver"
                @filters="filters"
                @sort="sort"
        />
    </Layout>
</template>

<script>
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import Table from '@views/driver/table';
    import PageHeader from '@components/page-header';
    import {driversMethods, driversComputed, getFilters, saveFilters} from "@state/helpers";

    export default {
        computed: {
            ...driversComputed
        },
        name: "index-drivers",
        page: {
            title: 'Курьеры',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, Table},
        data() {
            return {
                currentPage: 1,
                filterStorageKey: 'driver_filters',
                filterParams: {},
                title: 'Курьеры',
                items: [
                    {
                        text: 'Главаня',
                        to: '/',
                    },
                    {
                        text: 'Курьеры',
                        active: true,
                    },
                ],
            };
        },
        created() {
            this.filterParams = getFilters(this.filterStorageKey)
            this.currentPage = this.filterParams.page || 1;
            this.getIndex({params: this.filterParams, page: this.page});
        },
        methods: {
            ...driversMethods,
            pagination(pageNum) {
                this.currentPage = page;
                this.filterParams.page = page;
                saveFilters(this.filterStorageKey, this.filterParams)
                this.getIndex({params: this.filterParams});
            },
            deleteDriver(id) {
                this.deleteById(id)
                    .then((res) => {
                        this.getIndex({page: this.page});
                    }).catch((err) => {
                    this.$bvToast.toast(err.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
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
    };
</script>

<style lang="scss" module></style>