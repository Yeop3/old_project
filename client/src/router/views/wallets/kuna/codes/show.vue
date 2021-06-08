<template>
    <layout>
        <page-header :title="title" :items="items" />
        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card mt-2">
                        <div class="card-body">
                    <b-table v-if="data" class="mt-2" :items="[data]" :fields="fields" stacked>
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
                    </b-table>
                <div class="centered" v-else>
                    <b-spinner />
                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
import appConfig from '@src/app.config.json'
import Layout from '@layouts/main'
import PageHeader from '@components/page-header'
import { mapActions, mapState } from 'vuex'
import axios from 'axios'
import { getHumanDate } from '@state/helpers'

export default {
    page: {
        title: 'Просмотр Kuna-кода',
        meta: [{ name: 'description', content: appConfig.description }],
    },
    components: { Layout, PageHeader },
    data() {
        return {
            getHumanDate,
            data: null,
            title: 'Просмотр Kuna-кода',
            items: [
                {
                    text: 'Главная',
                    to: '/',
                },
                {
                    text: 'Kuna-коды',
                    to: '/kuna-codes/',
                },
                {
                    text: 'Просмотр Kuna-кода',
                    active: true,
                },
            ],
            fields: [
                {
                    key: 'number',
                    label: 'ID',
                },
                {
                    key: 'created_at',
                    label: 'Создан',
                },
                {
                    key: 'code',
                    label: 'Kuna-код',
                },
                {
                    key: 'amount',
                    label: 'Сумма',
                },
                {
                    key: 'kuna_account',
                    label: 'Kuna-аккаунт',
                },
                {
                    key: 'shift',
                    label: 'Смена',
                },
                {
                    key: 'order',
                    label: 'Заказ',
                },
            ],
        }
    },
    async created() {
        await this.fetch()
    },
    methods: {
        async fetch() {
            try {
                const res = await axios.get(
                    '/api/kuna/codes/' + this.$route.params.id
                )
                this.data = res.data
            } catch (e) {
                console.log(e)
            }
        },
    },
}
</script>

<style scoped>
</style>