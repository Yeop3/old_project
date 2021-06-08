<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="header-title">
                    <router-link class="btn btn-primary btn-xs" tag="a" to="/discounts/create/">Добавить скидку
                    </router-link>
                </h4>
                <b-overlay :show="spiner" rounded="sm" no-center>
                    <template v-slot:overlay>
                        <div class="centered">
                            <b-spinner variant="secondary"></b-spinner>
                        </div>
                    </template>
                    <div class="table-responsive">
                        <b-table
                                id="discounts-table"
                                :items="discounts.data"
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


                            <template v-slot:cell(name)="data">
                                <div>
                                    {{data.item.name}}
                                </div>
                                <div class="text-warning small">
                                    Приоритет: {{data.item.discount_priority}}
                                </div>
                                <div class="text-muted small" v-if="data.item.description">
                                    {{data.item.description}}
                                </div>
                            </template>
                            <template v-slot:cell(status)="data">
                                <div class="text-success" v-if="data.item.active">
                                    ◉ Активна
                                </div>
                                <div class="text-danger" v-else>
                                    ◉ Не активна
                                </div>
                                <div class="small text-warning">
                                    действует
                                    <span v-if="!data.item.date_start && !data.item.date_end">
                                     постоянно
                                </span>
                                    <template v-if="data.item.date_start">
                                        c {{new Date(data.item.date_start).toLocaleString("ru-RU", {year: 'numeric',
                                        month:
                                        'long',
                                        day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                                    </template>
                                    <template v-if="data.item.date_end">
                                        по {{new Date(data.item.date_end).toLocaleString("ru-RU", {year: 'numeric',
                                        month:
                                        'long',
                                        day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                                    </template>

                                </div>
                            </template>
                            <template v-slot:cell(discount_value)="data">
                                {{data.item.discount_value}}%
                            </template>
                            <template v-slot:cell(spread)="data">
                                <div>
                                <span class="bold">
                                    Товары:
                                </span>
                                    <div v-for="product_type in data.item.product_types" :key="product_type.number">
                                        <router-link class="" tag="a"
                                                     :to="`/product-types/show/${product_type.number}`">
                                            {{product_type.name}}
                                        </router-link>
                                    </div>
                                </div>
                                <div>
                                <span class="bold">
                                    Локации:
                                </span>
                                    <div v-for="location in data.item.locations" :key="location.number">
                                        <router-link class="" tag="a"
                                                     :to="`/locations/show/${location.number}`">
                                            {{location.name}}
                                        </router-link>
                                    </div>
                                </div>
                                <div class="">
                                    Минимальное количество оплаченных заказов у
                                    клиента:{{data.item.client_min_paid_orders_count}}
                                </div>
                                <div class="">
                                    Минимальный приход у клиента, грн: {{data.item.client_min_income}}
                                </div>
                            </template>
                            <template v-slot:cell(actions)="data">
                                <div class="btn-group">
                                    <router-link class="btn btn-primary btn-xs" tag="a"
                                                 :to="`/discounts/show/${data.item.number}`">
                                        <b-icon icon="eye"></b-icon>
                                    </router-link>
                                    <router-link class="btn btn-warning btn-xs" tag="a"
                                                 :to="`/discounts/edit/${data.item.number}`">
                                        <b-icon icon="pencil"></b-icon>
                                    </router-link>
                                    <b-button class="btn" variant="danger" size="xs"
                                              @click="deleteDiscounts(data.item.number)">
                                        <b-icon icon="trash"></b-icon>
                                    </b-button>
                                </div>
                            </template>
                        </b-table>
                        <b-pagination
                                v-if="discounts.last_page > 1"
                                v-model="pageCurrent"
                                :total-rows="discounts.total"
                                :per-page="discounts.per_page"
                                aria-controls="discounts-table"
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
import {BIcon, BIconEye, BIconPencil, BIconTrash} from "bootstrap-vue";
import FilterField from '@views/components/FilterField';
import {modalConfirm} from "@state/helpers";
import { mapState } from 'vuex'

export default {
    name: "table-discounts",
    props: {
        discounts: {
            type: Object,
            default: () => {
            }
        },
        pageCurrent: {
            type: Number
        },
        spiner: {
            type: Boolean
        },
        filterParams: Object
    },
    components: {
        BIcon,
        BIconTrash,
        BIconPencil,
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
                    key: 'name',
                    label: 'Название',
                    sortable: true,
                    filter: {
                        value: null,
                        type: 'text'
                    }
                },
                {
                    key: 'discount_value',
                    label: 'Размер',
                    filter: {
                        value: null,
                        type: 'number'
                    }
                },
                {
                    key: 'status',
                    label: 'Статус',
                    filter: {
                        value: null,
                        type: 'select',
                        options: []
                    }
                },
                {
                    key: 'spread',
                    label: 'Распространение',
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
    created() {
        this.fields.find(f => f.key == 'status').filter.options = this.statusList
        this.initValueFilters()
    },
    computed: {
        ...mapState('discounts', {
            statusList: 'status_list'
        })
    },
    methods: {
        modalConfirm,
        paginate(pageNum) {
            this.$emit('paginate', pageNum);
        },
        async deleteDiscounts(id) {
            try {
                const value = await this.modalConfirm('Действительны ли вы хотите это удалить?')
                if (value) {
                    this.$emit('delete-by-id', id);
                }
            } catch (e) {
            }
        },
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