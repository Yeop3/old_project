<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="header-title">
                    <router-link class="btn btn-success btn-xs" tag="a" to="/payments/qiwi_manual/create">
                        <b-icon-hand-thumbs-up/>
                        Создать Qiwi-Оплату
                    </router-link>
                </h4>
                <b-overlay :show="spiner" rounded="sm">
                    <div class="table-responsive">
                        <b-table
                                id="drivers-table"
                                :items="qiwiManuals.data"
                                :fields="fields"
                                small
                                :sort-by.sync="sortBy"
                                :sort-desc.sync="sortDesc"
                                sortDirection="desc"
                        >

                            <template v-slot:top-row="columns">
                                <td>
                                    <b-form-input
                                            :type="filterField.find((value) => value.name === 'number').type"
                                            :name="filterField.find((value) => value.name === 'number').name"
                                            v-model="filterField.find((value) => value.name === 'number').value"
                                            @blur="filters"
                                            size="sm"
                                    >

                                    </b-form-input>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <b-form-select
                                            :name="filterField.find((value) => value.name === 'phone').name"
                                            :options="filterField.find((value) => value.name === 'phone').options"
                                            v-model="filterField.find((value) => value.name === 'phone').value"
                                            @change="filters"
                                            size="sm"
                                    >

                                    </b-form-select>
                                </td>
                            </template>

                            <template v-slot:cell(created_at)="data">
                                {{new Date(data.item.created_at).toLocaleString("ru-RU", {year: 'numeric', month:
                                'long',
                                day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                            </template>
                            <template v-slot:cell(shift)="data">
                                <template v-if="data.item.order.shift.operator">
                                    {{data.item.order.shift.operator.name}}
                                </template>
                                <template v-else>
                                    Удален
                                </template>
                            </template>
                            <template v-slot:cell(summ)="data">
                                {{data.item.sum}} {{data.item.amount.currency}}
                            </template>
                            <template v-slot:cell(wallet)="data">
                                {{data.item.wallet.phone}}
                            </template>
                            <template v-slot:cell(actions)="data">
                                <router-link class="btn btn-warning btn-xs mr-2" tag="a"
                                             :to="`/payments/qiwi_manual/edit/${data.item.number}`">
                                    <b-icon-pencil/>
                                </router-link>
                            </template>
                        </b-table>
                        <b-pagination
                                v-model="currentPage"
                                :total-rows="totalRows"
                                :per-page="perPage"
                                align="left"
                                @change="paginate"
                                v-if="qiwiManuals.last_page > 1"
                        ></b-pagination>
                    </div> <!-- end table-responsive-->
                </b-overlay>
            </div> <!-- end card-box -->
        </div> <!-- end col -->
    </div>
</template>

<script>
    import {BIconHandThumbsUp, BIconTrash, BIconPencil} from 'bootstrap-vue';

    export default {
        name: "table-qiwi-payment",
        components: {
            BIconHandThumbsUp,
            BIconTrash,
            BIconPencil
        },
        computed: {},
        props: {
            qiwiManuals: {
                type: Object
            },
            qiwiManualPhone: {
                type: Array
            },
            spiner: {
                type: Boolean
            }
        },
        data() {
            return {
                page: 1,
                sortBy: "priority",
                sortDesc: true,
                filterField: [
                    {
                        name: "number",
                        value: "",
                        type: "number"

                    },
                    {
                        name: "phone",
                        value: null,
                        type: "select",
                        options: [
                            {text: "Все", value: null}
                        ]

                    },
                ],
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                        sortable: true
                    },
                    {
                        key: 'created_at',
                        label: 'Время',
                        sortable: true
                    },
                    {
                        key: 'shift',
                        label: 'Сменна',
                    },
                    {
                        key: 'summ',
                        label: 'Сумма',
                    },
                    {
                        key: 'wallet',
                        label: 'Кошелек',
                    },
                    {
                        key: 'order.number',
                        label: 'Заказ',
                    },
                    {
                        key: 'order.client.name',
                        label: 'Клиент',
                    },
                    {
                        key: 'comment',
                        label: 'Инфо',
                    },
                    {
                        key: 'client_wallet',
                        label: 'Кошелек клиента',
                    },
                    {
                        key: 'actions',
                        label: 'Действия'
                    },
                ],
                perPage: 3,
                currentPage: 1,
                totalRows: 54,
            };
        },
        mounted() {
            this.currentPage = this.qiwiManuals.current_page;
            this.perPage = this.qiwiManuals.per_page;
            this.totalRows = this.qiwiManuals.total;
            this.filterField.find((value) => value.name === 'phone').options = [...this.qiwiManualPhone];
            this.filterField.find((value) => value.name === 'phone').options.unshift({text: "Все", value: null});
        },
        methods: {
            paginate(pageNum) {

            },
            filters() {
                this.$emit('filters', this.filterField);
            },
        }
    };
</script>

<style scoped>

</style>