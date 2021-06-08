<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box" v-if="!spiner">
                    <div class="table-responsive">
                        <b-table
                                id="drivers-table"
                                stacked=""
                                :items="[product]"
                                :fields="fields"
                                small
                        >

                            <template v-slot:cell(product_type)="data">
                                <router-link class="" tag="a"
                                             v-if="data.item.product_type"
                                             :to="`/product-types/show/${data.item.product_type.number}`">
                                    {{data.item.product_type.name}}
                                </router-link>
                                <b-badge v-else variant="warning">
                                    Удален
                                </b-badge>
                            </template>
                            <template v-slot:cell(image_url)="data">
                                <img v-for="photo in data.item.photos" :src="photo.url" alt="" width="320" height="480">
                            </template>

                            <template v-if="product.video_url" v-slot:cell(video_url)="data">
                                <b-embed
                                        aspect="16by9"
                                        :src="product.video_url"
                                />
                            </template>

                            <template v-slot:cell(price)="data">
                                {{data.item.product_type.price.amount / 100}} {{data.item.product_type.price.currency}}
                            </template>
                            <template v-slot:cell(location)="data">
                                <router-link class="" tag="a"
                                             v-if="data.item.location"
                                             :to="`/locations/show/${data.item.location.number}`">
                                    {{data.item.location.name_chain}}
                                </router-link>
                                <b-badge v-else variant="warning">
                                    Удалена
                                </b-badge>
                            </template>
                            <template v-slot:cell(driver)="data">
                                <router-link class="" tag="a"
                                             v-if="data.item.driver"
                                             :to="`/drivers/edit/${data.item.driver.number}`">
                                    {{data.item.driver.name}}
                                </router-link>

                                <b-badge v-else variant="warning">
                                    Удален
                                </b-badge>
                            </template>
                            <template v-slot:cell(commission)="data">
                                {{data.item.commission_value}} {{commission_types.find((value) => data.item.commission_type
                                === value.value).text}}
                            </template>
                            <template v-slot:cell(created)="data">
                                {{new Date(data.item.created_at).toLocaleString("ru-RU", {year: 'numeric', month: 'long',
                                day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                            </template>
                            <template v-slot:cell(status)="data">
                              {{data.item.status_name}}
                            </template>
                        </b-table>
                    </div> <!-- end table-responsive-->
                </div> <!-- end card-box -->
                <div class="d-flex justify-content-center mb-3" v-else>
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
    import {commissionComputed, productComputed, productMethods} from '@state/helpers';

    export default {
        name: "show-product",
        components: {PageHeader, Layout},
        computed: {
            ...productComputed,
            ...commissionComputed,
        },
        page: {
            title: 'Просмотр ',
            meta: [{name: 'description', content: appConfig.description}],
        },
        async created() {
            await this.getById(this.$route.params.id);
            // this.title += this.product.address;
            this.items[2].text += this.product.address;
        },
        data() {
            return {
                spiner: false,
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                        sortable: true
                    },
                    {
                        key: 'image_url',
                        label: 'Фото',
                        sortable: false
                    },
                    {
                        key: 'video_url',
                        label: 'Видео'
                    },
                    {
                        key: 'coordinates',
                        label: 'Координаты',
                        sortable: false
                    },
                    {
                        key: 'product_type',
                        label: 'Товар'
                    },
                    {
                        key: 'price',
                        label: 'Цена'
                    },
                    {
                        key: 'location',
                        label: 'Локация'
                    },
                    {
                        key: 'driver',
                        label: 'Курьер'
                    },
                    {
                        key: 'commission',
                        label: 'Комиссия'
                    },
                    {
                        key: 'status',
                        label: 'Статус'
                    },
                    {
                        key: 'created',
                        label: 'Добавлен'
                    },
                ],
                title: 'Просмотр ',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Клады',
                        to: '/products',
                    },
                    {
                        text: 'Клад: ',
                        active: true,
                    },
                ],
            };
        },
        methods:{
            ...productMethods
        }
    };
</script>

<style lang="scss" module></style>