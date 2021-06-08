<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <b-overlay :show="loading" rounded="sm" no-center>
                    <template v-slot:overlay>
                        <div class="centered">
                            <b-spinner variant="secondary"></b-spinner>
                        </div>
                    </template>
                    <div class="table-responsive">
                        <b-table
                                :items="data"
                                :fields="fields"
                                small
                                :sort-by.sync="sortBy"
                                :sort-desc.sync="sortDesc"
                                sortDirection="desc"
                        >
                            <template v-slot:top-row="columns">
                                <td></td>
                                <td></td>
                                <td>
                                    <b-form-input
                                            :name="filterField.find((value) => value.name === 'code').name"
                                            v-model="filterField.find((value) => value.name === 'code').value"
                                            type="text"
                                            trim
                                            :debounce="1000"
                                            size="sm"
                                    ></b-form-input>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </template>
                            <template v-slot:cell(created_at)="data">
                                <template v-if="data.item.created_at">
                                   {{ getHumanDate(data.item.created_at) }}
                                </template>
                            </template>
                            <template v-slot:cell(amount)="data">
                                <template v-if="data.item.amount">
                                   {{ `${data.item.amount.amount} ${data.item.amount.currency}` }}
                                </template>
                            </template>
                            <template v-slot:cell(kuna_account)="data">
                                <template v-if="data.item.kuna_account">
                                    <b-link :to="`/kuna-account/show/${data.item.kuna_account.number}`">{{ data.item.kuna_account.name }}</b-link>
                                </template>
                            </template>
                           <template v-slot:cell(shift)="data">
                                <template v-if="data.item.shift">
                                    <b-link :to="`/shifts/show/${data.item.shift.number}`">{{ data.item.shift.number }}</b-link>
                                </template>
                            </template>
                           <template v-slot:cell(order)="data">
                                <template v-if="data.item.order">
                                    <b-link :to="`/orders/show/${data.item.order.number}`">{{ data.item.order.number }}</b-link>
                                </template>
                            </template>
                            <template v-slot:cell(actions)="data">
                                <router-link class="btn btn-primary btn-xs mr-1" tag="a"
                                                :to="`/kuna-codes/show/${data.item.number}`">
                                    <b-icon icon="eye"></b-icon>
                                </router-link>
                            </template>
                        </b-table>
                    </div>
                </b-overlay>
            </div>
        </div>
    </div>
</template>

<script>
    import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';
    import { getHumanDate } from "@state/helpers";

    export default {
        props: {
            data: {
                type: Array,
            },
            loading: {
                type: Boolean,
            }
        },
        components: {BIcon, BIconTrash, BIconPencil, BIconEye},
        watch: {
            filterField: {
                handler() {
                    this.$emit('filter', this.filterField);
                },
                deep: true
            },
        },
        methods: {
            onDelete(id){
                this.$emit('on-delete', id);
            },
        },
        data() {
            return {
                getHumanDate,
                sortBy: "number",
                sortDesc: true,
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                        sortable: true
                    },
                    {
                        key: 'created_at',
                        label: 'Создан',
                        sortable: true
                    },
                    {
                        key: 'code',
                        label: 'Kuna-код',
                        sortable: true
                    },
                    {
                        key: 'amount',
                        label: 'Сумма',
                        sortable: true
                    },
                    {
                        key: 'kuna_account',
                        label: 'Kuna-аккаунт',
                        sortable: true
                    },
                    {
                        key: 'shift',
                        label: 'Смена',
                        sortable: true
                    },
                    {
                        key: 'order',
                        label: 'Заказ',
                        sortable: true
                    },
                    {
                        key: 'actions',
                        label: 'Просмотр',
                    },
                ],
                filterField: [
                    {
                        name: "code",
                        value: null,
                    },
                ]
            };
        }
    };
</script>

<style scoped>

</style>