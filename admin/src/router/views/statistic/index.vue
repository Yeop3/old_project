<template>
    <Layout>
        <PageHeader :title="title" :items="items" />

        <StatisticFilter
                :statistic-status="statistic_status"
                :statistic-sellers="statistic_sellers"
                :statuses-counter="statusesCount"
                :filter-fields="filterField"
                @filters="filters"
        >
        </StatisticFilter>



        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card mt-2">
                        <div class="card-body">
                            <b-overlay :show="spinerCounter" rounded="sm" center>
                                <template v-slot:overlay>
                                    <div class="centered">
                                        <b-spinner variant="secondary"></b-spinner>
                                    </div>
                                </template>

                                <b-table
                                        v-if="statistic"
                                        small
                                        :fields="fields"
                                        :items="[statistic]"
                                >
                                    <template v-slot:cell(total_sum)="data">
                                        {{data.item.total_sum}} руб
                                    </template>

                                    <template v-slot:cell(paid_sum)="data">
                                        {{data.item.paid_sum}} руб
                                    </template>

                                    <template v-slot:cell(unpaid_sum)="data">
                                        {{data.item.unpaid_sum}} руб
                                    </template>
                                </b-table>
                            </b-overlay>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
    import appConfig from '@src/app.config'
    import Layout from '@layouts/main'
    import PageHeader from '@components/page-header'
    import StatisticFilter from '@views/statistic/partials/filter'
    import {statisticComputed, statisticMethods} from "@state/helpers";

    export default {
        name: "statistic.index",
        page: {
            title: 'Статистика',
            meta: [{ name: 'description', content: appConfig.description }],
        },
        components: {StatisticFilter, Layout, PageHeader },
        data() {
            return {
                title: 'Статистика',
                items: [
                    {
                        text: 'Главная',
                        href: '/',
                    },
                    {
                        text: 'Статистика',
                        active: true,
                    },
                ],
                filterField: [
                    {
                        name: "date_start",
                        value: "",
                        type: "date"

                    },
                    {
                        name: "date_end",
                        value: "",
                        type: "date"

                    },
                    {
                        name: "seller_id",
                        value: null,
                        type: "select",
                        options: [],
                    },
                    {
                        name: "statuses",
                        value: null,
                        type: "checkbox",
                        options: [],
                    },
                ],
                fields: [
                    {
                        key: 'total_sum',
                        label: 'Общаяя сумма'
                    },
                    {
                        key: 'paid_sum',
                        label: 'Оплачено'
                    },
                    {
                        key: 'unpaid_sum',
                        label: 'Не оплачено'
                    },
                ],
                statusesCount: {},
                spinerCounter: false,
            }
        },
        computed:{
            ...statisticComputed,
        },
        mounted() {
            this.spinerCounter = true;
            this.getIndex({}).then((res) => {
                this.statusesCount = Object.assign({}, this.statistic.statuses_counts);
                this.spinerCounter = false;

            })
        },
        methods:{
            ...statisticMethods,
            filters: async function (fieldFilters) {
                let params = {};
                for (let value of fieldFilters) {
                    if (value.value) {
                        params[value.name] = value.value;
                    }
                }
                this.spinerCounter = true;
                    this.getIndex({
                    params
                }).then((res) => {
                    this.statusesCount = Object.assign({}, this.statistic.statuses_counts);
                        this.spinerCounter = false;
                    });
            },
        }
    }
</script>

<style scoped>

</style>