<template>
    <div>
        <div class="mt-3 mb-2">
            <b-row>
                <b-col class="mb-2" lg="6">
                    <b-select
                            :options="filterField.find((value) => value.name === 'seller_id').options"
                            v-model="filterField.find((value) => value.name === 'seller_id').value"
                            @change="filter"
                            size="sm"
                    >
                    </b-select>
                </b-col>
            </b-row>
            <b-row>
                <b-col lg="3" md="6">
                    <label for="date_start">С:</label>
                    <b-form-datepicker
                            id="date_start"
                            @input="filter"
                            label-reset-button="Сбросить"
                            label-no-date-selected="Дата не выбрана"
                            reset-button
                            placeholder="Дата не выбрана"
                            v-model="filterField.find((value) => value.name === 'date_start').value"
                            size="sm"
                            >
                    </b-form-datepicker>
                </b-col>
                <b-col class="mb-2" lg="3" md="6">
                    <label for="date_end">По:</label>
                    <b-form-datepicker
                            id="date_end"
                            label-reset-button="Сбросить"
                            reset-button
                            label-no-date-selected="Дата не выбрана"
                            placeholder="Дата не выбрана"
                            @input="filter"
                            v-model="filterField.find((value) => value.name === 'date_end').value"
                            size="sm"
                            >
                    </b-form-datepicker>
                </b-col>
            </b-row>
            <b-row>
                <b-col lg="4">
                    <b-button variant="primary" size="xs" class="mb-2" @click="clear">Сбросить</b-button>
                </b-col>
            </b-row>
            <b-row>
                <b-col lg="12" class="adaptive-filter">
                    <b-form-checkbox-group
                            buttons
                            button-variant="outline-primary"
                            :options="filterField.find((value) => value.name === 'statuses').options"
                            v-model="filterField.find((value) => value.name === 'statuses').value"
                            @input="filter"
                            size="xs"

                    >

                    </b-form-checkbox-group>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>

    export default {
        name: "statistic.filter",
        props: {
            statusesCounter: {
                type: Object,
                required: true,
                default: () => ({})
            },
            statisticStatus: {
                type: Object
            },
            statisticSellers: {
                type: Array
            },
            filterFields: {
                type: Array,
                default: () => ([])
            }
        },
        created() {
            this.filterField = [...this.filterFields];
            this.filterField = this.filterField.map((value) => {
                switch (value.name) {
                    case "statuses":
                        value.options = [];
                        for (let item in this.statisticStatus) {
                            if (this.statisticStatus.hasOwnProperty(item))
                                value.options.push({text: this.statisticStatus[item]+'(0)', value: item});
                        }
                        value.value = this.$route.query.statistic_status || null;
                        break;
                    case "seller_id":
                        value.options = [];
                        this.statisticSellers.forEach((item) => {
                            value.options.push({text: item.domain, value: item.id});
                        });
                        value.options.unshift({text: "", value: null});
                        value.value = this.$route.query.statistic_sellers || null;
                        break;

                    case 'date_start':
                        value.value = this.$route.query.date_start || null;
                        break;
                    case 'date_end':
                        value.value = this.$route.query.date_end || null;
                        break;
                }
                return value;
            });
        },
        data() {
            return {
                filterField: [],
            };
        },
        watch: {
            statusesCounter() {
                this.filterField = [...this.filterFields];
                this.filterField = this.filterField.map((value) => {
                    switch (value.name) {
                        case "statuses":
                            value.options = [];
                            for (let item in this.statisticStatus) {
                                if (this.statisticStatus.hasOwnProperty(item))
                                    value.options.push({
                                        text: this.statisticStatus[item] + `(${this.statusesCounter[item] || 0})`,
                                        value: item
                                    });
                            }
                            // value.value = this.$route.query.statistic_status || null;
                            break;
                    }
                    return value;
                });
            }
        },
        computed: {
        },
        methods: {
            filter() {
                this.$emit('filters', this.filterField);
            },
            clear(){
                this.filterField.find((value) => value.name === 'statuses').value = [];
            }
        }
    };
</script>

<style scoped>
    @media (max-width: 1140px) {
        .adaptive-filter > div {
            display: flex;
            flex-flow: row wrap;
        }
    }
</style>