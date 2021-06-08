<template>
    <b-table
            :items="[item]"
            stacked
            :fields="fields"
            bordered
            sortDirection="desc"
    >
        <template v-slot:cell(status)="data">
            <div class="text-success" v-if="item.active">
                Активен (новые заказы на кошелек поступают, кошелек выдается клиентам)
            </div>
            <div class="text-danger" v-else>
                Выключен (новые заказы на кошелек не поступают, кошелек не выдается клиентам)
            </div>
        </template>
        <template v-slot:cell(all_orders)="date">
            {{item.orders.length + item.orders_awaiting_payment.length + item.orders_partially_paid.length}}
        </template>
        <template v-slot:cell(check_orders)="date">
            {{item.orders_awaiting_payment.length}} / {{item.orders_partially_paid.length}}
        </template>
        <template v-slot:cell(min_paid_orders_count)="data">
            <div :class="[item.active ? 'text-success' : '']" v-if="item.min_paid_orders_count === 0">
                {{item.min_paid_orders_count}} - кошелек может выдаваться любым клиентам
            </div>
            <div :class="[item.active ? 'text-success' : '']" v-else>
                {{item.min_paid_orders_count}} - кошелек выдается только тем клиентам, на счету которых есть минимум
                {{item.min_paid_orders_count}} оплаченных заказов
            </div>

        </template>
    </b-table>
</template>

<script>
    export default {
        name: "table-wallets-qiwi-manual-info",
        computed: {},
        props: {
            item: {
                type: Object,
                default: () => {
                }
            },

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
                        key: 'all_orders',
                        label: 'Все заказы',
                    },
                    {
                        key: 'check_orders',
                        label: 'Ожидающие заказы',
                    },
                    {
                        key: 'min_paid_orders_count',
                        label: 'Минимальное количество оплаченных заказов на счету клиентов для выдачи им данного кошелька',
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