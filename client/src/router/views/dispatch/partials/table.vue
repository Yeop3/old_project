<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="header-title mb-2">
                    <router-link class="btn mr-1 btn-primary btn-xs" tag="a" to="/dispatchers/create/"> Создать рассылку
                    </router-link>
                    <b-dropdown text="Создать рассылку по наличию" class="adaptive-btn-dispatch" size="xs" variant="warning">
                        <b-dropdown-item :to="`/dispatchers/create-by-exist/${item.value}`" :key="item.value"
                                         v-for="item in botList.filter(b => b.value)">
                            {{item.text}}
                        </b-dropdown-item>
                    </b-dropdown>
                </h4>
                <b-overlay :show="spiner" rounded="sm" no-center>
                    <template v-slot:overlay>
                        <div class="centered">
                            <b-spinner variant="secondary"></b-spinner>
                        </div>
                    </template>
                    <div class="table-responsive">
                        <b-table
                            id="drivers-table"
                            :items="dispatchers.data"
                            :fields="fields"
                            small
                            no-local-sorting
                            @sort-changed="onSorting"
                            fixed
                            sortDirection="desc"
                        >
                            <template v-slot:top-row="columns">
                                <td :key="`filter_${field.key}`" v-for="field in fields">
                                    <filter-field v-if="field.filter" :field="field" @on-input="onFilterInput" />
                                </td>
                            </template>

                            <template v-slot:cell(bot_id)="data">
                                <router-link v-if="data.item.bot" :to="`bots/card/${data.item.bot.number}`">
                                    {{data.item.bot.name}}
                                </router-link>
                            </template>

                            <template v-slot:cell(messages)="data">
                                {{data.item.messages}}
                            </template>

                        </b-table>
                        <b-pagination
                            v-if="dispatchers.last_page > 1"
                            v-model="pageCurrent"
                            :total-rows="dispatchers.total"
                            :per-page="dispatchers.per_page"
                            aria-controls="dispatch-table"
                            align="left"
                            @change="paginate"
                        ></b-pagination>
                    </div> <!-- end table-responsive-->
                </b-overlay>
            </div> <!-- end card-box -->
        </div> <!-- end col -->
    </div>
</template>

<script>
    import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';
    import FilterField from '@views/components/FilterField';
    import { mapState } from 'vuex'

    export default {
        components: { FilterField },
        props: {
            pageCurrent: Number,
            dispatchers: Object,
            spiner: Boolean,
            filterParams: Object
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
                        key: 'bot_id',
                        label: 'Бот',
                        sortable: true,
                        filter: {
                            value: null,
                            type: 'select',
                            options: []
                        }
                    },
                    {
                        key: 'messages',
                        label: 'Сообщение',
                        sortable: true,
                        filter: {
                            value: null,
                            type: 'text'
                        }
                    },
                ],
            };
        },
        async created() {
           // console.log(this.dispatchers)
            this.initValueFilters()
            await this.$store.dispatch('bots/getSelect')
            this.fields.find((value) => value.key === 'bot_id').filter.options = this.botList;
        },
        computed: {
            ...mapState('bots', {
                botList: 'select_bots'
            })
        },
        methods: {
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
    @media (max-width: 501px) {

        .adaptive-btn-dispatch{
            margin-top: 0.5rem;
        }

    }
</style>