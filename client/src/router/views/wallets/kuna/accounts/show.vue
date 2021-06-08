<template>
    <layout>
        <page-header :title="title" :items="items" />
        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <b-alert show variant="danger" dismissible v-if="data.proxy.is_working === 0">Выбранный проски не работает</b-alert>
                    <div class="card mt-2">
                        <div class="card-body">
                            <b-table v-if="data" class="mt-2" :items="[data]" :fields="fields" stacked>
                                <template v-slot:cell(created_at)="data">
                                    <template v-if="data.item.created_at">
                                        {{ getHumanDate(data.item.created_at) }}
                                    </template>
                                </template>
                                <template v-slot:cell(active)="data">
                                    <template v-if="data.item.active">
                                        {{ data.item.active == true ? 'Активный' : 'Неактивный' }}
                                    </template>
                                </template>
                                <template v-slot:cell(proxy)="data">
                                    <div>
                                        <template v-if="data.item.proxy">
                                            {{data.item.proxy.ip}}:{{data.item.proxy.port}} - {{data.item.proxy.username}}
                                        </template>
                                    </div>
                                  <template v-if="data.item.proxy">
                                    <b-badge v-if="data.item.proxy.is_working === 0" variant="danger">
                                        Не работает
                                    </b-badge>
                                    <b-badge v-else variant="success">
                                        Работает
                                    </b-badge>
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
        title: 'Просмотр Kuna-аккаунта',
        meta: [{ name: 'description', content: appConfig.description }],
    },
    components: { Layout, PageHeader },
    data() {
        return {
            getHumanDate,
            data: null,
            title: 'Просмотр Kuna-аккаунта',
            items: [
                {
                    text: 'Главная',
                    to: '/',
                },
                {
                    text: 'Kuna-аккаунты',
                    to: '/kuna-accounts/',
                },
                {
                    text: 'Просмотр Kuna-аккаунта',
                    active: true,
                },
            ],
            fields: [
                {
                    key: 'number',
                    label: 'ID',
                },
                {
                    key: 'name',
                    label: 'Название',
                },
                {
                    key: 'created_at',
                    label: 'Создан',
                },
                {
                    key: 'public_key',
                    label: 'Публичный ключ',
                },
                {
                    key: 'private_key',
                    label: 'Приватный ключ',
                },
                {
                    key: 'proxy',
                    label: 'Прокси'
                },
                {
                    key: 'active',
                    label: 'Статус',
                },
                {
                    key: 'comment',
                    label: 'Комментарий',
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
                    '/api/kuna/accounts/' + this.$route.params.id
                )
                this.data = res.data
            } catch (e) {
               // console.log(e)
            }
        },
    },
}
</script>

<style scoped>
</style>