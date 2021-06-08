<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <b-overlay :show="shiftSpiner" rounded="sm" no-center>
                    <template v-slot:overlay>
                        <div class="centered">
                            <b-spinner variant="secondary"></b-spinner>
                        </div>
                    </template>
                    <div class="table-responsive">
                        <b-table
                                id="shifts-table"
                                :items="shifts"
                                :fields="fields"
                                small
                                no-local-sorting
                                @sort-changed="onSorting"
                                :sortDirection="filterParams.sortDirection || 'desc'"
                                :sort-by="filterParams.sortField || null"
                        >

                            <template v-slot:top-row="columns">
                                <td :key="`filter_${field.key}`" v-for="field in fields">
                                    <filter-field v-if="field.filter" :field="field" @on-input="onFilterInput" @on-click-reset-button="onClickResetFiltersButton" />
                                </td>
                            </template>

                            <template v-slot:cell(operator)="data">
                                <template v-if="data.item.operator">
                                    {{data.item.operator.name}}
                                </template>
                                <div class="text-warning" v-else>
                                    Оператор от отсутствует
                                </div>
                            </template>
                            <template v-slot:cell(started_at)="data">
                                {{new Date(data.item.started_at).toLocaleString("ru-RU", {year: 'numeric', month:
                                'long',
                                day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                            </template>

                            <template v-slot:cell(ended_at)="data">
                                <template v-if="data.item.ended_at">
                                    {{new Date(data.item.ended_at).toLocaleString("ru-RU", {year: 'numeric', month:
                                    'long',
                                    day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                                </template>
                                <div class="text-danger" v-else>
                                    Смена еще не закончилась
                                </div>
                            </template>
                            <template v-slot:cell(actions)="data">
                                <b-button @click="$router.push({name: 'show-shifts', params:{number: data.item.number}})" class="btn btn-xs" variant="primary" tag="a">
                                    <b-icon icon="eye"></b-icon>
                                </b-button>
                            </template>
                        </b-table>
                    </div> <!-- end table-responsive-->
                </b-overlay>
            </div> <!-- end card-box -->
        </div> <!-- end col -->
    </div>
</template>

<script>
    import {BIcon, BIconEye} from 'bootstrap-vue';
    import FilterField from '@views/components/FilterField';
    import { mapActions, mapState } from 'vuex';

    export default {
        computed: {
            ...mapState('operator', {
                operatorsList: 'operators_select'
            })
        },
        props: {
            shifts: Array,
            shiftSpiner: Boolean,
            filterParams: Object
        },
        components: {
            BIcon,
            BIconEye,
            FilterField
        },
        data() {
            return {
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                        sortable: true,
                        filter: {
                            value: null,
                            type: 'number'
                        }
                    },
                    {
                        key: 'operator',
                        label: 'Оператор',
                        sortable: true,
                        filter: {
                            value: null,
                            type: 'select',
                            options: []
                        }
                    },
                    {
                        key: 'started_at',
                        label: 'Начало смены',
                        sortable: true
                    },
                    {
                        key: 'ended_at',
                        label: 'Конец смены',
                        sortable: true
                    },
                    {
                        key: 'actions',
                        label: 'Действия',
                        filter: {
                            type: 'reset-button'
                        }
                    },
                ],
            };
        },
        async created() {
            await this.getOperatorsList()
            this.fields.find(f => f.key == 'operator').filter.options = this.operatorsList
        },
        methods: {
            ...mapActions('operator', {
                getOperatorsList: 'getSelect'
            }),
            onSorting(header) {
                if (!header.sortBy) return;

                const params = {};
                params['sortField'] = header.sortBy;
                params['sortDirection'] = header.sortDesc ? 'desc' : 'asc';
                
                this.$emit('sort', params);
            },
            onFilterInput() {
                let params = {};
                for (let field of this.fields) {
                    if (field.filter)
                        params[field.key] = field.filter.value;
                }
                this.$emit('filters', params)
            },
            clearFilters() {
                this.fields.forEach(f => {
                    if (f.filter)
                        f.filter.value = null
                })
            },
            onClickResetFiltersButton() {
                this.clearFilters()
                this.onFilterInput()
            },
            initValueFilters() {
                this.fields.forEach(field => {
                    if (!field.filter) return
                        const filterValue = this.filterParams[field.key]
                    if (filterValue) {
                        field.filter.value = filterValue
                    }
                })
            },
        }
    };
</script>

<style scoped>

</style>