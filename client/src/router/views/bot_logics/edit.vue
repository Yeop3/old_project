<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box" v-if="!spiner">
                    <div class="card mt-2">
                        <div class="card-body">
                            <b-tabs v-if="updateForm" content-class="mt-3">
                                <b-tab active>
                                    <template v-slot:title>
                                        <i class="remixicon-information-line"></i>
                                        Информация
                                    </template>
                                    <b-form @submit.prevent="tryToUpdateBotLogic">
                                        <b-row>
                                            <b-col lg="4" sm="6">
                                                <form-input
                                                        name="name"
                                                        label="Название логики"
                                                        v-model="updateForm.name"
                                                        description="* Любое уникальное название"
                                                >
                                                </form-input>
                                                <form-textarea
                                                        name="description"
                                                        label="Описание логики"
                                                        v-model="updateForm.description"
                                                        description="* Описание логики или ее особенностей"
                                                >

                                                </form-textarea>
                                            </b-col>
                                        </b-row>
                                        <b-button type="submit" variant="primary">
                                            <i class="remixicon-save-3-line"></i>
                                            Сохранить
                                        </b-button>
                                    </b-form>
                                </b-tab>
                                <b-tab>
                                    <template v-slot:title>
                                        <i class="remixicon-chat-3-line"></i>
                                        Команды бота
                                    </template>
                                    <b-form @submit.prevent="tryToUpdateBotLogic">
                                        <b-row>
                                            <b-col v-for="command in updateForm.commands" lg="4" sm="6">
                                                <b-card no-body>
                                                    <b-card-header class="text-center">{{command.title}} -
                                                        {{command.keys.join(' | ')}}
                                                    </b-card-header>
                                                    <b-card-body>
                                                        <b-tabs>
                                                            <b-tab v-for="(templates, tab) in getItemsWithTabs(command.templates)"
                                                                   :title=tab>
                                                                <b-card-text v-for="template in templates">
                                                                    <form-textarea
                                                                            name="description"
                                                                            :label=template.title
                                                                            v-model=template.content
                                                                            :description=template.description
                                                                    >
                                                                    </form-textarea>
                                                                </b-card-text>
                                                            </b-tab>
                                                        </b-tabs>
                                                        <b-card-text v-for="template in command.templates"
                                                                     v-if="template.tab === null">
                                                            <form-textarea
                                                                    name="description"
                                                                    :label=template.title
                                                                    v-model=template.content
                                                                    :description=template.description
                                                                    :rows="rows"
                                                            >
                                                            </form-textarea>
                                                        </b-card-text>
                                                    </b-card-body>
                                                </b-card>
                                            </b-col>
                                        </b-row>
                                        <b-button type="submit" variant="primary">
                                            <i class="remixicon-save-3-line"></i>
                                            Сохранить
                                        </b-button>
                                    </b-form>
                                </b-tab>
                                <b-tab>
                                    <template v-slot:title>
                                        <i class="remixicon-notification-line"></i>
                                        Уведомленния событий
                                    </template>
                                    <b-form @submit.prevent="tryToUpdateBotLogic">
                                        <b-row>
                                            <b-col lg="4" sm="6">
                                                <b-tabs>
                                                    <b-tab v-for="(events, tab) in getItemsWithTabs(updateForm.events)"
                                                           :title=tab>
                                                        <b-card-text v-for="event in events">
                                                            <form-textarea
                                                                    v-model="event.content"
                                                                    :label="event.title"
                                                                    :description="event.description"
                                                                    :rows="rows"
                                                            >
                                                            </form-textarea>
                                                        </b-card-text>
                                                    </b-tab>
                                                </b-tabs>
                                            </b-col>
                                            <b-col lg="8" sm="6">
                                                <b-row>
                                                    <b-col v-for="event in updateForm.events" lg="6">
                                                        <form-textarea
                                                                v-if="event.tab == null"
                                                                :label=event.title
                                                                v-model=event.content
                                                                :description=event.description
                                                        >

                                                        </form-textarea>
                                                    </b-col>
                                                </b-row>
                                            </b-col>
                                        </b-row>
                                        <b-button type="submit" variant="primary">
                                            <i class="remixicon-save-3-line"></i>
                                            Сохранить
                                        </b-button>
                                    </b-form>
                                </b-tab>
                                <b-tab>
                                    <template v-slot:title>
                                        <i class="remixicon-notification-line"></i>
                                        Уведомленния при действиях оператора
                                    </template>
                                    <b-form @submit.prevent="tryToUpdateBotLogic">
                                        <b-row>
                                            <b-col v-for="notification in updateForm.operator_notifications" lg="4"
                                                   sm="6">
                                                <form-textarea
                                                        name="description"
                                                        :label=notification.title
                                                        v-model=notification.content
                                                        :description=notification.description
                                                        :rows="rows"
                                                >

                                                </form-textarea>
                                            </b-col>
                                        </b-row>
                                        <b-button type="submit" variant="primary">
                                            <i class="remixicon-save-3-line"></i>
                                            Сохранить
                                        </b-button>
                                    </b-form>
                                </b-tab>
                                <b-tab>
                                    <template v-slot:title>
                                        <i class="remixicon-forbid-2-line"></i>
                                        Антиспам
                                    </template>
                                    <b-form @submit.prevent="tryToUpdateBotLogic">
                                        <b-row>
                                            <b-col v-for="antispam in updateForm.antispams" lg="4" sm="6">
                                                <b-card no-body>
                                                    <b-card-header>{{antispam.title}}</b-card-header>
                                                    <b-card-body v-for="option in antispam.options">
                                                        <form-input
                                                                v-if="option.key === 'limit'"
                                                                type="number"
                                                                :label="option.title"
                                                                v-model="option.value"
                                                                :description="option.description"
                                                        >
                                                        </form-input>
                                                        <b-form-checkbox
                                                                v-if="option.value === true || option.value === false"
                                                                :unchecked-value="false"
                                                                v-model="option.value"
                                                        >{{option.title}}
                                                        </b-form-checkbox>
                                                        <b-form-text
                                                                v-if="option.value === true || option.value === false">
                                                            <br>
                                                            {{option.description}}
                                                        </b-form-text>
                                                        <form-input
                                                                v-else-if="option.key === 'send_notice'"
                                                                type="text"
                                                                :label="option.title"
                                                                v-model="option.value"
                                                                :description="option.description"
                                                        >
                                                        </form-input>
                                                        <form-textarea
                                                                v-if="option.key === 'notice_text'"
                                                                v-model="option.value"
                                                                :label="option.title"
                                                                :description="option.description"
                                                        ></form-textarea>
                                                        <form-textarea
                                                                v-if="option.key === 'ban_text'"
                                                                v-model="option.value"
                                                                :label="option.title"
                                                                :description="option.description"
                                                        ></form-textarea>
                                                        <form-input
                                                                v-if="option.key === 'ban_duration'"
                                                                v-model="option.value"
                                                                type="number"
                                                                :label="option.title"
                                                                :description="option.description"
                                                        ></form-input>
                                                    </b-card-body>
                                                </b-card>
                                            </b-col>
                                        </b-row>
                                        <b-button type="submit" variant="primary">
                                            <i class="remixicon-save-3-line"></i>
                                            Сохранить
                                        </b-button>
                                    </b-form>
                                </b-tab>
                                <b-tab>
                                    <template v-slot:title>
                                        <i class="remixicon-notification-line"></i>
                                        Напоминания об оплате
                                    </template>
                                    <b-form @submit.prevent="tryToUpdateBotLogic">
                                        <b-row>
                                            <b-col v-for="reminder in updateForm.reminders" lg="6" sm="6">
                                                <b-card no-body>
                                                    <b-card-header>{{reminder.title}}</b-card-header>
                                                    <b-card-body>
                                                        <form-input
                                                                v-for="optionReminder in reminder.options"
                                                                v-if="optionReminder.tab === null"
                                                                type="text"
                                                                :label="optionReminder.title"
                                                                v-model="optionReminder.value"
                                                                :description="optionReminder.description"
                                                        >
                                                        </form-input>
                                                        <b-tabs>
                                                            <b-tab v-for="(optionsReminder, tab) in getItemsWithTabs(reminder.options)"
                                                                   :title=tab>
                                                                <b-card-text  v-for="optionReminder in optionsReminder">
                                                                    <form-textarea
                                                                            v-model="optionReminder.value"
                                                                            :label="optionReminder.title"
                                                                            :description="optionReminder.description"
                                                                            :rows="rows"
                                                                    >
                                                                    </form-textarea>
                                                                </b-card-text>
                                                            </b-tab>
                                                        </b-tabs>
                                                    </b-card-body>
                                                </b-card>
                                            </b-col>
                                        </b-row>
                                        <b-button type="submit" variant="primary">
                                            <i class="remixicon-save-3-line"></i>
                                            Сохранить
                                        </b-button>
                                    </b-form>
                                </b-tab>
                                <b-tab>
                                    <template v-slot:title>
                                        <i class="remixicon-telegram-line"></i>
                                        Генерация рассылки по наличию
                                    </template>
                                    <b-form @submit.prevent="tryToUpdateBotLogic">
                                        <b-row>
                                            <b-col v-for="distribution in updateForm.distributions" lg="6" sm="6">
                                                <form-textarea
                                                        v-if="distribution.key !== 'max_depth_locations'"
                                                        v-model="distribution.content"
                                                        :label="distribution.title"
                                                        :description="distribution.description"
                                                ></form-textarea>
                                                <form-input
                                                        v-else
                                                        v-model="distribution.content"
                                                        :label="distribution.title"
                                                        :description="distribution.description"
                                                        type="number"
                                                        :min="0"
                                                >

                                                </form-input>
                                            </b-col>
                                        </b-row>
                                        <b-button type="submit" variant="primary">
                                            <i class="remixicon-save-3-line"></i>
                                            Сохранить
                                        </b-button>
                                    </b-form>
                                </b-tab>
                                <b-tab>
                                    <template v-slot:title>
                                        <i class="remixicon-question-line"></i>
                                        Помощь
                                    </template>
                                    <b-button class="mb-2" variant="primary">
                                        Копировать настройки методов оплаты
                                    </b-button>
                                    <b-card no-body>
                                        <b-card-header header-bg-variant="success">
                                            <span class="text-white">Система макросов</span>
                                        </b-card-header>
                                        <b-card-body>
                                            <b-table
                                                    responsive
                                                    :items="macros"
                                                    :fields="macrosFields"
                                                    small
                                            >

                                            </b-table>
                                        </b-card-body>
                                    </b-card>
                                </b-tab>
                            </b-tabs>
                        </div>
                    </div>
                </div>
                <div class="centered" v-else>
                    <b-spinner></b-spinner>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import PageHeader from '@components/page-header';
    import {botLogicsMethods, botLogicsComputed} from "@state/helpers";
    import FormInput from "@components/ui/form/FormInput";
    import FormTextarea from "@components/ui/form/FormTextarea";
    import FormCheckbox from "@components/ui/form/FormCheckbox";

    export default {
        name: "bot-logics.edit",
        page: {
            title: 'Логика ботов',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {FormCheckbox, FormTextarea, FormInput, Layout, PageHeader},
        data() {
            return {
                title: 'Логика ботов',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Логика ботов',
                        to: '/bot_logics',
                    },
                    {
                        text: '',
                        active: true,
                    }
                ],
                rows: 9,
                updateForm: {},
                macros: [
                    {
                        macros: '{order-number}',
                        description: 'Номер заказа'
                    },
                    {
                        macros: '{order-price}',
                        description: 'Стоимость заказа'
                    },
                    {
                        macros: '{order-amount-paid}',
                        description: 'Оплаченная сумма (сумма, которую клиент уже оплатил), грн'
                    },
                    {
                        macros: '{order-amount-unpaid}',
                        description: 'Неоплаченная сумма (сумма, которую клиент должен еще заплатить), грн'
                    },
                    {
                        macros: '{order-purse-phone}',
                        description: 'Номер телефона QIWI-кошелька для оплаты'
                    },
                    {
                        macros: '{wallet-number}',
                        description: 'Номер кошелька GlobalMoney'
                    },
                    {
                        macros: '{order-crypto-address}',
                        description: 'Кошелек для оплаты криптовалютой'
                    },
                    {
                        macros: '{order-crypto-amount-paid}',
                        description: 'Оплаченная сумма (сумма, которую клиент уже оплатил) криптовалютой'
                    },
                    {
                        macros: '{order-crypto-amount-unpaid}',
                        description: 'Неоплаченная сумма (сумма, которую клиент еще должен заплатить) криптовалютой'
                    },
                    {
                        macros: '{order-product-content}',
                        description: 'Адрес заказанного товара'
                    },
                    {
                        macros: '{order-product-product_type-name}',
                        description: 'Наименование заказанного товара'
                    },
                    {
                        macros: '{order-product-location}',
                        description: 'Локация заказанного товара'
                    },
                    {
                        macros: '{order-reserve-left}',
                        description: 'Время, оставшееся до конца резерва товара (таймаута)'
                    },
                    {
                        macros: '{antispam-limit-cancels}',
                        description: 'Лимит подряд идущих неоплаченных заказов'
                    },
                    {
                        macros: '{client-left-cancels}',
                        description: 'Количество оставшихся у клиента попыток оплатить заказ'
                    },
                    {
                        macros: '{item-location-name}',
                        description: 'Название района'
                    },
                    {
                        macros: '{item-location-discount}',
                        description: 'Скидка в данном районе'
                    },
                    {
                        macros: '{item-location-number}',
                        description: 'Номер района'
                    },
                    {
                        macros: '{item-product_type-name}',
                        description: 'Название товара'
                    },
                    {
                        macros: '{item-product_type-packing}',
                        description: 'Количество упаковок'
                    },
                    {
                        macros: '{item-product_type-unit}',
                        description: 'Количество товара'
                    },
                    {
                        macros: '{item-product_type-discount}',
                        description: 'Скидка на данный товар'
                    },
                    {
                        macros: '{item-product_type-price}',
                        description: 'Стоимость товара'
                    },
                    {
                        macros: '{item-product_type-number}',
                        description: 'Номер товара'
                    },
                    {
                        macros: '{item-payment}',
                        description: 'Способ оплаты'
                    },
                    {
                        macros: '{item-payment-title}',
                        description: 'Заголовок способа оплаты'
                    },
                    {
                        macros: '{product_type-number}',
                        description: 'Номер товара'
                    },
                    {
                        macros: '{product_type-name}',
                        description: 'Название товара'
                    },
                    {
                        macros: '{product_type-price}',
                        description: 'Стоимость товара'
                    },
                    {
                        macros: '{location-number}',
                        description: 'Номер района'
                    },
                    {
                        macros: '{location-name}',
                        description: 'Название района'
                    },
                    {
                        macros: '{location-caption}',
                        description: 'Полное название района'
                    },
                    {
                        macros: '{ban-duration}',
                        description: 'Продолжительность блокировки'
                    },
                    {
                        macros: '{list}',
                        description: 'Список'
                    },
                ],
                macrosFields: [
                    {
                        key: 'macros',
                        label: 'Макрос'
                    },
                    {
                        key: 'description',
                        label: 'Описание'
                    }
                ]
            };
        },
        created() {
        },
        computed: {
            ...botLogicsComputed,
        },
        mounted() {
            this.loadBotLogic({
                type: this.$route.params.type,
                number: this.$route.params.number
            }).then((res) => {
                this.items[2].text = this.botLogic.name;
                this.updateForm = Object.assign({}, this.botLogic);
            });
        },
        methods: {
            ...botLogicsMethods,
            tryToUpdateBotLogic() {
                return this.updateBotLogic(this.updateForm)
                    .then((res) => {
                        this.$router.push({name: 'bot-logics.index'});
                    });
            },
            getItemsWithTabs(items) {
                return (items || []).reduce((tabs, item) => {
                    if (!item.tab) {
                        return tabs;
                    }

                    if (!tabs[item.tab]) {
                        tabs[item.tab] = [];
                    }

                    tabs[item.tab].push(item);

                    return tabs;
                }, {});
            },
        }
    };
</script>