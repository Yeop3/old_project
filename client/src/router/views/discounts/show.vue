<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <div class="row">
            <div class="col-lg-12">
                <div v-if="!spiner" class="card-box">
                    <div class="table-responsive">
                        <b-table
                                id="drivers-table"
                                stacked=""
                                :items="[discount]"
                                :fields="fields"
                                small

                        >
                            <template v-slot:cell(status)="data">
                                <div class="text-success" v-if="data.item.active">
                                    Активирована
                                </div>
                                <div class="text-danger" v-else>
                                    Деактивирована
                                </div>

                            </template>
                            <template v-slot:cell(product_types)="data">
                                <div v-for="product_type in data.item.product_types">
                                    -
                                    <router-link class="" tag="a"
                                                 :to="`/product-types/show/${product_type.number}`">
                                        {{product_type.name}}
                                    </router-link>
                                </div>
                            </template>
                            <template v-slot:cell(locations)="data">
                                <div v-for="location in data.item.locations">
                                    -
                                    <router-link class="" tag="a"
                                                 :to="`/product-types/show/${location.number}`">
                                        {{location.name_chain}}
                                    </router-link>
                                </div>
                            </template>

                            <template v-slot:cell(created_at)="data">
                                {{new Date(data.item.created_at).toLocaleString("ru-RU", {year: 'numeric', month:
                                'long',
                                day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                            </template>

                            <template v-slot:cell(date_start)="data">
                                <template v-if="data.item.date_start">
                                    {{new Date(data.item.date_start).toLocaleString("ru-RU", {year: 'numeric', month:
                                    'long',
                                    day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                                </template>
                                <span class="text-warning" v-else>
                                    (не задано)
                                </span>
                            </template>
                            <template v-slot:cell(date_end)="data">
                                <template v-if="data.item.date_end">
                                    {{new Date(data.item.date_end).toLocaleString("ru-RU", {year: 'numeric', month:
                                    'long',
                                    day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                                </template>
                                <span class="text-warning" v-else>
                                    (не задано)
                                </span>
                            </template>

                            <template v-slot:cell(count_product_active)="data">
                                {{getCountProducts}}
                            </template>
                        </b-table>
                    </div> <!-- end table-responsive-->
                </div> <!-- end card-box -->
                <div class="centered" v-else>
                    <b-spinner></b-spinner>
                </div>
            </div> <!-- end col -->
        </div>
    </Layout>
</template>

<script>
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import PageHeader from '@components/page-header';
    import {discountsComputed, discountsMethods} from '@state/helpers';

    export default {
        name: "show-discounts",
        components: {PageHeader, Layout},
        computed: {
            ...discountsComputed,
        },
        page: {
            title: 'Просмотр ',
            meta: [{name: 'description', content: appConfig.description}],
        },
        async created() {
            await this.getById(this.$route.params.id);
            this.title += this.discount.name;
            this.items[2].text += this.discount.name;
        },
        data() {
            return {
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                        sortable: true
                    },
                    {
                        key: 'name',
                        label: 'Название',
                        sortable: true
                    },
                    {
                        key: 'status',
                        label: 'Состояние',
                        sortable: true
                    },
                    {
                        key: 'discount_value',
                        label: 'Размер скидки, %',
                        sortable: true
                    },
                    {
                        key: 'discount_priority',
                        label: 'Приоритет',
                        sortable: true
                    },
                    {
                        key: 'description',
                        label: 'Описание',
                        sortable: true
                    },
                    {
                        key: 'created_at',
                        label: 'Создана',
                        sortable: true
                    },
                    {
                        key: 'date_start',
                        label: 'Начало действия',
                        sortable: true
                    },
                    {
                        key: 'date_end',
                        label: 'Конец действия',
                        sortable: true
                    },
                    {
                        key: 'client_min_paid_orders_count',
                        label: 'Минимальное количество оплаченных заказов у клиента',
                        sortable: true
                    },
                    {
                        key: 'client_min_income',
                        label: 'Минимальный приход у клиента, грн',
                        sortable: true
                    },
                    {
                        key: 'count_product_active',
                        label: 'Количество доступных товаров',
                        sortable: true
                    },
                    {
                        key: 'product_types',
                        label: 'Товары',
                        sortable: true
                    },
                    {
                        key: 'locations',
                        label: 'Локации',
                        sortable: true
                    },

                ],
                title: 'Просмотр ',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Скидки',
                        to: '/discounts',
                    },
                    {
                        text: 'Скидка: ',
                        active: true,
                    },
                ],
            };
        },
        methods: {
            ...discountsMethods,
        }
    };
</script>

<style lang="scss" module></style>