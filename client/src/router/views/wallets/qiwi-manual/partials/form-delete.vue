<template>
    <div>
        <b-table
                :items="[item]"
                stacked
                :fields="fields"
                bordered
        >
            <template v-slot:cell(status)="data">
                <div class="text-success" v-if="item.active">
                    Активен (новые заказы на кошелек поступают, кошелек выдается клиентам)
                </div>
                <div class="text-danger" v-else>
                    Выключен (новые заказы на кошелек не поступают, кошелек не выдается клиентам)
                </div>
            </template>
            <template v-slot:cell(check_orders)="date">
                {{item.orders_awaiting_payment.length}} / {{item.orders_partially_paid.length}}
            </template>
            <template v-slot:cell(note)="data">
                <div class="text-danger" v-if="item.note">
                    {{item.note}}
                </div>
                <template v-else>
                    -
                </template>
            </template>
        </b-table>
        <div class="text-danger">
            * Перед удалением кошелька убедитесь, что он находится в состоянии «Выключен», а также на нем нет ожидающих
            заказов и не выполняется никаких операций.

        </div>
        <div class="mt-3">
            <b-button variant="warning" @click="deleteById">
                <b-icon-trash/>
                Удалить в корзину
            </b-button>
            <b-button variant="danger" class="ml-2" @click="deleteForever">
                <b-icon-trash/>
                Удалить безвозвратно
            </b-button>
        </div>
    </div>
</template>

<script>

    import {BIcon, BIconTrash} from "bootstrap-vue";

    export default {
        methods: {
            async deleteById() {
                const value = await this.$bvModal.msgBoxConfirm('Действительны ли вы хотите это удалить в корзину ?',
                    {
                        title: 'Пожалуйста подтвердите',
                        size: 'sm',
                        buttonSize: 'sm',
                        okVariant: 'danger',
                        okTitle: 'Удалить',
                        cancelTitle: 'Отмена',
                        footerClass: 'p-2',
                        hideHeaderClose: false,
                        centered: true
                    });
                if (value)
                    this.$emit('delete-by-id', this.item.number);
            },
            async deleteForever() {
                const value = await this.$bvModal.msgBoxConfirm('Действительны ли вы хотите это удалить безвозвратно?',
                    {
                        title: 'Пожалуйста подтвердите',
                        size: 'sm',
                        buttonSize: 'sm',
                        okVariant: 'danger',
                        okTitle: 'Удалить',
                        cancelTitle: 'Отмена',
                        footerClass: 'p-2',
                        hideHeaderClose: false,
                        centered: true
                    });
                if (value){
                    this.$emit('delete-forever', this.item.number);
                }
            }
        },
        name: "table-wallets-qiwi-manual-delete",
        computed: {},
        props: {
            item: {
                type: Object,
                default: () => {
                }
            },

        },
        components: {
            BIcon,
            BIconTrash,
        },
        data() {
            return {
                page: 1,
                sortBy: "priority",
                sortDesc: true,
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                    },
                    {
                        key: 'status',
                        label: 'Состояние',
                    },
                    {
                        key: 'check_orders',
                        label: 'Ожидающие заказы',
                    },
                    {
                        key: 'note',
                        label: 'Заметка',
                    },
                ],
            };
        },
        mounted() {
        },
    };
</script>

<style scoped>

</style>