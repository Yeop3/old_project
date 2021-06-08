<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <b-overlay :show="spiner" rounded="sm" no-center>
                    <template v-slot:overlay>
                        <div class="centered">
                            <b-spinner variant="secondary"></b-spinner>
                        </div>
                    </template>
                    <div class="table-responsive">
                        <b-table
                                id="drivers-table"
                                :items="crypto.data"
                                :fields="fields"
                                small
                                :sort-by.sync="sortBy"
                                :sort-desc.sync="sortDesc"
                                sortDirection="desc"
                                fixed
                        >
                            <template v-slot:top-row="columns">
                                <td :key="`filter_${key}`" v-for="(filter, key) in filterField">
                                    <template v-if="filter.name === 'crypto_wallet'">
                                        <b-select :options="selectWallet"
                                                  @change="filterCol"
                                                  v-model="filter.value"
                                                  size="sm"
                                        >
                                        </b-select>
                                    </template>
                                    <template v-else>
                                        <b-form-input
                                                :name="filter.name"
                                                :type="filter.type"
                                                v-model="filter.value"
                                                :min="0"
                                                @blur="filterCol"
                                                size="sm"
                                        ></b-form-input>
                                    </template>
                                </td>
                            </template>

                            <template v-slot:cell(order)="data">
                                <router-link :to="`/orders/show/${data.item.order.number}`">
                                    {{data.item.order.number}}
                                </router-link>
                            </template>

                            <template v-slot:cell(crypto_wallet)="data">
                                <router-link :to="`/crypto-wallet/show/${data.item.crypto_wallet.number}`">
                                    {{data.item.crypto_wallet.name}}
                                </router-link>
                            </template>

                            <template v-slot:cell(address)="data">
                                <div v-if="data.item.hash">
                                    {{data.item.address}}
                                </div>
                                <div v-else>
                                    Создана вручную
                                </div>
                            </template>

                        </b-table>
                    </div> <!-- end table-responsive-->
                </b-overlay>
            </div> <!-- end card-box -->
        </div> <!-- end col -->
    </div>
</template>

<script>
    import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';

    export default {
        name: "table-crypto-tr",
        props: {
            crypto: {
                type: Object,
            },
            spiner: {
                type: Boolean,
            },
            selectWallet: {
                type: Array,
            }
        },
        components: {BIcon, BIconTrash, BIconPencil, BIconEye},
        mounted() {
            // this.filterField.find((value) => value.name === 'crypto_wallet').options = [...this.selectWallet];
        },
        methods: {
            filterCol() {
                this.filterField.forEach((value) => {
                    if (value.value) {
                       // console.log(value.value);
                        if (typeof value.value === "string")
                            value.value = value.value.trim();
                    }
                });
                this.$emit('filter', this.filterField);
            }
        },
        data() {
            return {
                sortBy: "number",
                sortDesc: true,
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                        sortable: true
                    },
                    {
                        key: 'order',
                        label: 'Заказ',
                        sortable: true
                    },
                    {
                        key: 'crypto_wallet',
                        label: 'Кошелек',
                        sortable: true
                    },
                    {
                        key: 'address',
                        label: 'Адрес',
                        sortable: true
                    },
                    {
                        key: 'format_around',
                        label: 'Сумма',
                        sortable: true
                    },
                    {
                        key: 'created_at',
                        label: 'Создана',
                        sortable: true
                    },
                ],
                filterField: [
                    {
                        name: "number",
                        value: "",
                        type: "number"
                    },
                    {
                        name: "order",
                        value: "",
                        type: "number"
                    },
                    {
                        name: "crypto_wallet",
                        value: null,
                        type: "select",
                        options: [{text: "Все", value: null}],
                    },
                    {
                        name: "address",
                        value: "",
                        type: "text"
                    },
                ]
            };
        }
    };
</script>

<style scoped>

</style>