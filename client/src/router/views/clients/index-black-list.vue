<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card mt-2">
                        <div class="card-body">
                            <b-overlay :show="spiner" rounded="sm" no-center>
                                <template v-slot:overlay>
                                    <div class="centered">
                                        <b-spinner variant="secondary"></b-spinner>
                                    </div>
                                </template>
                                <b-table
                                        :items="clients"
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
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </template>

                                    <template v-slot:cell(client)="row">
                                        <a>@{{row.item.username}}</a>
                                        <br>
                                        {{row.item.name}}
                                        <br>
                                        Телеграм ID: {{row.item.telegram_id}}
                                    </template>

                                    <template v-slot:cell(ban_expires_at)="row">
                                        <div v-if="row.item.ban_expires_at">
                                            {{row.item.ban_expires_at}}
                                            <div>
                                                <b-button
                                                        variant="warning"
                                                        size="sm"
                                                        @click="tryToUnBanClient(row.item.number)"
                                                >Разбанить
                                                </b-button>
                                            </div>
                                        </div>
                                        <div v-else>
                                            Нет
                                            <div>
                                                <b-dropdown
                                                        text="Забанить"
                                                        size="sm"
                                                        variant="primary"
                                                        id="dropdown-left"
                                                >
                                                    <b-dropdown-item @click="tryToBanClient(1, row.item.number)">
                                                        Забанить на
                                                        сутки
                                                    </b-dropdown-item>
                                                    <b-dropdown-item @click="tryToBanClient(7, row.item.number)">
                                                        Забанить на
                                                        неделю
                                                    </b-dropdown-item>
                                                    <b-dropdown-item @click="tryToBanClient(30, row.item.number)">
                                                        Забанить
                                                        на месяц
                                                    </b-dropdown-item>
                                                    <b-dropdown-item @click="tryToBanClient(365, row.item.number)">
                                                        Забанить
                                                        на год
                                                    </b-dropdown-item>
                                                </b-dropdown>
                                            </div>
                                        </div>
                                    </template>

                                    <template v-slot:cell(in_black_list)="row">
                                        <div v-if="row.item.in_black_list">
                                            <div>
                                                <b-button
                                                        variant="warning"
                                                        size="sm"
                                                        @click="tryToUnBlackListClient(row.item.number)"
                                                >Убрать из ЧС
                                                </b-button>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <div>
                                                <b-button
                                                        variant="primary"
                                                        size="sm"
                                                        @click="tryToBlackListClient(row.item.number)"
                                                >В ЧС
                                                </b-button>
                                            </div>
                                        </div>
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
                                        <a v-b-tooltip="'Оплачен'">{{row.item.paid}}</a>
                                        /
                                        <a v-b-tooltip="'Отдан оператором'">{{row.item.given_operator}}</a>
                                        /
                                        <a v-b-tooltip="'Переклад'">{{row.item.relocation}}</a>
                                        /
                                        <a v-b-tooltip="'Всего заказов'">{{row.item.orders.length}}</a>
                                        <br>
                                        <span v-b-tooltip="'Всего оплаченных заказов (за все время)'">{{row.item.paid}} заказов оплатил</span>
                                        <br>
                                        Приход: {{row.item.coming}}
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

                                        <b-button v-b-tooltip="'Удалить'" @click="tryToDeleteClient(row.item.number)"
                                                  size="sm" class="btn-danger">
                                            <i class="remixicon-close-line"></i>
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
    import {clientsMethods, clientsComputed} from "@state/helpers";

    export default {
        name: "clients.index.black.list",
        page: {
            title: 'Клиенты',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader},
        data() {
            return {
                title: 'Черный список клиентов',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Клиенты',
                        to: '/clients/',
                    },
                    {
                        text: 'Черный список клиентов',
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
                        key: 'created_at',
                        label: 'Добавился',
                        sortable: true,
                    },
                    {
                        key: 'visited_at',
                        label: 'Посещение',
                    },
                    {
                        key: 'ban_expires_at',
                        label: 'Забанен',
                        sortable: true,
                    },
                    {
                        key: 'in_black_list',
                        label: 'В черном списке',
                        sortable: true,
                    },
                    {
                        key: 'note',
                        label: 'Заметка',
                    },
                    {
                        key: 'discount_value',
                        label: 'Скидка',
                        sortable: true,
                    },
                    {
                        key: 'orders',
                        label: 'Заказы',
                    },
                    {
                        key: 'actions',
                        label: 'Действия',
                    },
                ],
                clientsTable: [],
                filterField: [
                    {
                        name: "name",
                        value: "",
                        type: "text"

                    },
                    {
                        name: "black_list",
                        value: null,
                        type: "text",
                        options: [
                            {text: "Все", value: null},
                            {text: "Не в черном списке", value: 1},
                            {text: "В черном списке", value: 2},
                        ]
                    },
                ],
            };
        },
        computed: {
            ...clientsComputed,
        },
        async mounted() {
            await this.loadClients({
                black_list: 2
            });
        },
        methods: {
            ...clientsMethods,
            tryToDeleteClient(number) {
                return this.deleteClient(number).then((res) => {
                    this.loadClients({
                        black_list: 2
                    });
                });
            },
            async tryToBanClient(days, number) {
                const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите забанить этого клиента?', {
                    title: 'Пожалуйста подтвердите',
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'warning',
                    okTitle: 'Да',
                    cancelTitle: 'Нет',
                    footerClass: 'p-2',
                    hideHeaderClose: false,
                    centered: true
                });
                if (value) {
                    await this.banClient({days: days, number: number});
                    await this.loadClients({
                        black_list: 2
                    });
                }
            },
            async tryToUnBanClient(number) {
                const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите разбанить этого клиента?', {
                    title: 'Пожалуйста подтвердите',
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'warning',
                    okTitle: 'Да',
                    cancelTitle: 'Нет',
                    footerClass: 'p-2',
                    hideHeaderClose: false,
                    centered: true
                });
                if (value) {
                    await this.unBanClient(number);
                    await this.loadClients({
                        black_list: 2
                    });
                }
            },
            async tryToBlackListClient(number) {
                const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите внести этого клиента в черный список?', {
                    title: 'Пожалуйста подтвердите',
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'warning',
                    okTitle: 'Да',
                    cancelTitle: 'Нет',
                    footerClass: 'p-2',
                    hideHeaderClose: false,
                    centered: true
                });
                if (value) {
                    await this.blackListClient(number);
                    await this.loadClients({
                        black_list: 2
                    });
                }
            },
            async tryToUnBlackListClient(number) {
                const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите удалить этого клиента из черного списка?', {
                    title: 'Пожалуйста подтвердите',
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'warning',
                    okTitle: 'Да',
                    cancelTitle: 'Нет',
                    footerClass: 'p-2',
                    hideHeaderClose: false,
                    centered: true
                });
                if (value) {
                    await this.unBlackListClient(number);
                    await this.loadClients({
                        black_list: 2
                    });
                }
            },
            async filters() {
                let params = {};
                for (let value of this.filterField) {
                    if (value.value)
                        params[value.name] = value.value;
                }
                params.black_list = 2;
                await this.loadClients(params);
            },
            async sort(sortProduct) {
                let params = {...this.$route.query};

                params['sortDirection'] = sortProduct.sortDesc === true ? 'desc' : 'asc';
                params['sortField'] = sortProduct.sortBy;
                params.black_list = 2;
                await this.loadClients(params);
            },
        }
    };
</script>

<style>

</style>