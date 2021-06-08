<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-md-3">
                            <b-button variant="primary" @click="downloadUsername">
                                <i class="fas fa-file-download"></i>
                                Скачать список @Username
                            </b-button>
                        </div>
                        <div class="col-md-9">
                            Показаны только клиенты, у которых заполнен [@username] в аккаунте Telegram,
                            не находящиеся
                            в
                            бане и черном списке и которые за последние [3 месяцев] получали товар.
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-body">
                            <b-overlay :show="spiner_actual" rounded="sm" no-center>
                                <template v-slot:overlay>
                                    <div class="centered">
                                        <b-spinner variant="secondary"></b-spinner>
                                    </div>
                                </template>
                                <b-table
                                        :items="clientActuals"
                                        :fields="fields"
                                        class="text-center"
                                        small
                                        responsive
                                        no-local-sorting
                                        @sort-changed="sort"
                                        sortDirection="desc"
                                >


                                    <template v-slot:top-row="columns">
                                        <td></td>
                                        <td>
                                            <b-form-input
                                                    :type="filterField.find((value) => value.name === 'name').type"
                                                    :name="filterField.find((value) => value.name === 'name').name"
                                                    v-model="filterField.find((value) => value.name === 'name').value"
                                                    @blur="filters"
                                            >

                                            </b-form-input>
                                        </td>
                                    </template>


                                    <template v-slot:cell(client)="row">
                                        <a>@{{row.item.username}}</a>
                                        <br>
                                        {{row.item.name}}
                                        <br>
                                        Телеграм ID: {{row.item.telegram_id}}
                                    </template>

                                    <template v-slot:cell(note)="row">
                                        <div v-if="row.item.note">
                                            {{row.item.note}}
                                        </div>
                                        <div v-else>
                                            -
                                        </div>
                                    </template>

                                    <template v-slot:cell(orders)="row">
                                        <div class="text-success">
                                            Приход: {{row.item.coming}}
                                        </div>
                                        <div class="small text-muted nowrap">
                                            {{row.item.paid}} заказов оплатил
                                        </div>
                                    </template>

                                    <template v-slot:cell(last_order)="row">
                                        <div>{{moment(row.item.last_order.created_at).locale('ru').format('DD.MM.Y')}}
                                        </div>
                                        <div class="small text-muted">
                                            {{moment(row.item.last_order.created_at).locale('ru').format('HH:mm:ss')}}
                                        </div>
                                        <div>
                                            <router-link
                                                    :to="`/orders/show/${row.item.last_order.number}`"
                                            >
                                                #{{row.item.last_order.number}}
                                            </router-link>
                                        </div>
                                    </template>

                                    <template v-slot:cell(actions)="row">
                                        <b-button v-b-tooltip="'Посмотреть'" @click="$router.push({ name: 'clients.show', params: {
                                                    number: row.item.number
                                                    }})" size="sm" class="btn-info mr-1">
                                            <i class="remixicon-eye-line"></i>
                                        </b-button>

                                        <b-button v-b-tooltip="'Изменить'" @click="$router.push({ name: 'clients.edit', params: {
                                                    number: row.item.number
                                                    }})" size="sm" class="btn-warning mr-1">
                                            <i class="remixicon-pencil-line"></i>
                                        </b-button>
                                    </template>
                                </b-table>
                            </b-overlay>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import PageHeader from '@components/page-header';
    import {clientsActualDispatchMethods, clientsActualDispatchComputed} from "@state/helpers";

    export default {
        name: "index-hand-dispatch-actual-telegram",
        page: {
            title: 'Клиенты',
            meta: [{name: 'description', content: appConfig.description}],
        },
        computed: {
            ...clientsActualDispatchComputed,
        },
        components: {Layout, PageHeader},
        methods: {
            ...clientsActualDispatchMethods,
            async sort(sortProduct) {
                console.log(sortProduct);
                let params = {...this.$route.query};

                params['sortDirection'] = sortProduct.sortDesc === true ? 'desc' : 'asc';
                params['sortField'] = sortProduct.sortBy;

                await this.loadHandDispatchActualTelegram(params);
            },
            downloadUsername() {
                this.downloadUsernameActual();
            },
            async filters() {
                this.filterField.find((value) => value.name === 'name').value = this.filterField.find((value) =>
                    value.name === 'name').value.trim();
                let params = {};
                for (let value of this.filterField) {
                    if (value.value) {
                        value.name = value.name.trim();
                        params[value.name] = value.value;
                    }
                }
                await this.loadHandDispatchActualTelegram(params);
            }
        },
        mounted() {
            this.loadHandDispatchActualTelegram();
        },
        data() {
            return {
                title: 'Актуальные клиенты для ручной Telegram рассылки',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Клиенты',
                        to: '/clients',
                    },
                    {
                        text: 'Актуальные клиенты для ручной Telegram рассылки',
                        active: true,
                    },
                ],
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                        sortable: true,
                    },
                    {
                        key: 'client',
                        label: 'Клиент',
                    },
                    {
                        key: 'orders',
                        label: 'Приход',
                    },
                    {
                        key: 'note',
                        label: 'Заметка',
                    },
                    {
                        key: 'last_order',
                        label: 'Последний заказ',
                    },
                    {
                        key: 'created_at',
                        label: 'Добавился',
                        sortable: true,
                    },
                    {
                        key: 'actions',
                        label: 'Действияі',
                    },
                ],
                filterField: [
                    {
                        name: "name",
                        value: "",
                        type: "text"

                    },
                ],
            };
        },
    };
</script>

<style scoped>

</style>