<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <Table
                v-if="shifts"
                :shifts="shifts"
                :shift-spiner="shifts_spiner"
                @filters="filters"
                @sort="sort"
                :filter-params="filterParams"
        />

    </Layout>
</template>

<script>
    import { shiftsComputed, shiftsMethods, getFilters, saveFilters} from "@state/helpers";
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import Table from "@views/shifts/partials/table";

    export default {
        computed: {
            ...shiftsComputed,
        },
        page: {
            title: 'Смены',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, Table},
        async created() {
            this.filterParams = getFilters(this.filterStorageKey)
            this.currentPage = this.filterParams.page || 1
            await this.getIndex({params: this.filterParams});
        },
        methods: {
            ...shiftsMethods,
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
        data() {
            return {
                filterParams: {},
                filterStorageKey: 'shift_filters',
                currentPage: 1,
                title: 'Смены',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Смены',
                        active: true,
                    },
                ],
            };
        },
    };
</script>

<style scoped>

</style>