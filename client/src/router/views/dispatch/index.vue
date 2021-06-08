<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Table  
            v-if="Object.keys(dispatchers).length"
            :dispatchers="dispatchers"
            @filters="filters"
            @sort="sort"
            :filterParams="filterParams"
            :page-current="currentPage"
            :spiner="spiner"
        />
    </Layout>
</template>

<script>
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import appConfig from "@src/app.config.json";
    import Table from "@views/dispatch/partials/table";
    import { getFilters, saveFilters } from "@state/helpers";
    import { mapActions, mapState } from 'vuex';

    export default {
        components: {Layout, PageHeader, Table},
        page: {
            title: 'Рассылки сообщений',
            meta: [{name: 'description', content: appConfig.description}],
        },
        async created() {
            this.filterParams = getFilters(this.filterStorageKey)
            this.currentPage = this.filterParams.page || 1
            await this.getDispatcher({page: this.currentPage, params: this.filterParams})
        },
        data() {
            return {
                filterStorageKey: 'dispatch_filters',
                currentPage: 1,
                filterParams: {},
                title: 'Рассылки сообщений',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Рассылки сообщений',
                        active: true,
                    },
                ],
            };
        },
        computed: {
            ...mapState('dispatcher', ['dispatchers', 'spiner'])
        },
        methods: {
            ...mapActions('dispatcher', {
                getDispatcher: 'getIndex'
            }),
            async sort(params) {

                this.filterParams = {...this.filterParams, ...params, page: 1};
                this.currentPage = 1;

                saveFilters(this.filterStorageKey, this.filterParams)

                await this.getDispatcher({page: this.currentPage, params: this.filterParams});

                },
            async filters(params) {

                this.filterParams = {...this.filterParams, ...params, page: 1};
                this.currentPage = 1;

                saveFilters(this.filterStorageKey, this.filterParams)

                await this.getDispatcher({page: this.currentPage, params: this.filterParams});

            },
        }
    };
</script>
