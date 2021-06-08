<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="mb-10 text-muted">
                        Отображены клиенты, у которых нет оплаченных заказов, но у которых есть отмененные заказы
                    </div>
                    <div class="row mb-20 mt-2">
                        <b-form inline class="row">
                            <form-select
                                    v-for="(filter, key) in filterField"
                                    :key="`filter_${key}`"
                                    :label="filter.label"
                                    v-if="filter.type==='select'"
                                    :options="filter.options"
                                    v-model="filter.value"
                                    class="mb-2 mr-sm-2 mb-sm-0"
                                    @change="filters"
                            >
                            </form-select>
                            <form-input
                                    :label="filterField.find(value => value.name === 'spam_reserve_cancel_count').label"
                                    type="number"
                                    :min="1"
                                    v-model="filterField.find(value => value.name === 'spam_reserve_cancel_count').value"
                                    class="mb-2 mr-sm-2 mb-sm-0 "
                                    @blur="filters"
                            >
                            </form-input>
                        </b-form>
                    </div>
                    <div class="card mt-2">
                        <div class="card-body">
                            <b-overlay :show="spiner" rounded="sm" no-center>
                                <template v-slot:overlay>
                                    <div class="centered">
                                        <b-spinner variant="secondary"></b-spinner>
                                    </div>
                                </template>
                                <b-form inline class="mb-2">
                                    <b-form-select
                                            :options="checkboxActionSelection"
                                            v-model="checkboxAction"
                                    >
                                    </b-form-select>
                                    <b-button variant="primary" class="ml-2" @click="checkboxActionMethod">Подтвердить
                                    </b-button>
                                </b-form>
                                <b-table
                                        :items="client_spam_reserve"
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

                                    <template v-slot:head(checkbox)="state">
                                        <b-form-checkbox
                                                name="all_products"
                                                v-model="selectAll"
                                        >
                                        </b-form-checkbox>
                                    </template>


                                    <template v-slot:cell(checkbox)="data">
                                        <b-form-checkbox
                                                :name="`product_${data.item.number}`"
                                                :value="data.item.number"
                                                v-model="checkboxesIds"
                                        >

                                        </b-form-checkbox>
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

                                    <template v-slot:cell(count_canceled)="row">
                                        {{row.item.count_canceled}}/{{filterField.find(value =>
                                        value.name==='spam_reserve_cancel_count').value}}
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

                                <b-form inline class="mb-2">
                                    <b-form-select
                                            :options="checkboxActionSelection"
                                            v-model="checkboxAction"
                                    >
                                    </b-form-select>
                                    <b-button variant="primary" class="ml-2" @click="checkboxActionMethod">Подтвердить
                                    </b-button>
                                </b-form>
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
    import FormSelect from "@components/ui/form/FormSelect";
    import FormInput from "@components/ui/form/FormInput";

    export default {
        name: "clients.index.spam-reserve",
        page: {
            title: 'Клиенты заподозренные в спаме резервовap',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {FormInput, FormSelect, Layout, PageHeader},
        data() {
            return {
                title: 'Клиенты заподозренные в спаме резервов',
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
                        text: 'Клиенты заподозренные в спаме резервов',
                        active: true,
                    },
                ],
                fields: [
                    {
                        key: 'checkbox',
                        label: '',
                    },
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
                        key: 'ban_expires_at',
                        label: 'Забанен',
                        sortable: true,
                    },
                    {
                        key: 'note',
                        label: 'Заметка',
                    },
                    {
                        key: 'count_canceled',
                        label: 'Отмены',
                    },
                    {
                        key: 'actions',
                        label: 'Действия',
                    },


                ],
                filterField: [
                    {
                        name: "name",
                        value: "",
                        type: "text"
                    },
                    {
                        label: 'Отмены зафиксированы за период:',
                        name: "period_cancle",
                        value: 24 * 7,
                        type: "select",
                        options: [
                            {text: 'За последний час', value: 1},
                            {text: 'За последние 24 часа', value: 24},
                            {text: 'За последние 7 дней', value: (24 * 7)},
                            {text: 'За последние 30 дней', value: (24 * 30)},
                            {text: 'За последние 60 дней', value: (24 * 60)},
                        ]
                    },
                    {
                        label: 'Клиенты добавлены за период:',
                        name: "period_add_client",
                        value: null,
                        type: "select",
                        options: [
                            {text: 'За весь период', value: null},
                            {text: 'За последний час', value: 1},
                            {text: 'За последние 24 часа', value: 24},
                            {text: 'За последние 7 дней', value: (24 * 7)},
                            {text: 'За последние 30 дней', value: (24 * 30)},
                            {text: 'За последние 60 дней', value: (24 * 60)},
                        ]
                    },
                    {
                        label: 'Отмененных заказов от:',
                        name: "spam_reserve_cancel_count",
                        value: 2,
                        type: "number"
                    },
                ],
                checkboxAction: null,
                checkboxActionSelection: [
                    {
                        text: "Действие с выбранными:",
                        value: null,
                    },
                    {
                        text: "Удалить клиентов",
                        value: {type: "delete_select", value: true},
                    },
                    {
                        text: "Внести в черный список",
                        value: {type: "add_black_list", value: true},
                    },
                    {
                        text: "Забанить на сутки",
                        value: {"value": 1, type: "ban"},
                    },
                    {
                        text: "Забанить на неделю",
                        value: {"value": 7, type: "ban"},
                    },
                    {
                        text: "Забанить на месяц",
                        value: {"value": 30, type: "ban"},
                    },
                    {
                        text: "Забанить на год",
                        value: {"value": 365, type: "ban"},
                    },

                ],
                checkboxesIds: [],
            };
        },
        computed: {
            ...clientsComputed,
            selectAll: {
                get: function () {
                    return this.client_spam_reserve ? this.checkboxesIds.length === this.client_spam_reserve.length : false;
                },
                set: function (value) {
                    let selected = [];

                    if (value) {
                        this.client_spam_reserve.forEach(function (product) {
                            selected.push(product.number);
                        });
                    }

                    this.checkboxesIds = selected;
                }
            },
        },
        async mounted() {
            let params = {...this.$route.query};
            for (let value of this.filterField) {
                if (value.value)
                    params[value.name] = value.value;
            }
            await this.loadSpamReserve(params);
            console.log(this.client_spam_reserve);
        },
        methods: {
            ...clientsMethods,
            tryToDeleteClient(number) {
                return this.deleteClient(number).then((res) => {
                    this.loadSpamReserve(this.getParams());
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
                    await this.loadSpamReserve(this.getParams());
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
                    await this.loadSpamReserve(this.getParams());
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
                    await this.loadSpamReserve(this.getParams());
                }
            },
            async filters() {
                await this.loadSpamReserve(this.getParams());
            },
            async sort(sortProduct) {
                let params = this.getParams();

                params['sortDirection'] = sortProduct.sortDesc === true ? 'desc' : 'asc';
                params['sortField'] = sortProduct.sortBy;
                await this.loadSpamReserve(params);
            },
            async checkboxActionMethod() {
                try {
                    switch (this.checkboxAction.type) {
                        case "ban":
                            await this.multiBan({
                                numbers: this.checkboxesIds,
                                period: this.checkboxAction.value
                            });
                            break;
                        case "add_black_list":
                            await this.multiBlackList({
                                numbers: this.checkboxesIds,
                            });
                            break;
                        case "delete_select":
                            await this.multiDelete({
                                numbers: this.checkboxesIds,
                            });
                            break;
                    }
                } catch (err) {
                    this.$bvToast.toast(err.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }
                await this.loadSpamReserve(this.getParams());
            },
            getParams() {
                let params = {};
                for (let value of this.filterField) {
                    if (value.value)
                        params[value.name] = value.value;
                }
                return params;
            }
        }
    };
</script>

<style>

</style>