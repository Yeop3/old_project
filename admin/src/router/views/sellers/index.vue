<template>
    <Layout>
        <PageHeader :title="title" :items="items" />

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card mt-2">
                        <div class="card-body">
                            <b-button class="btn mb-2" size="xs" :to="'/sellers/create'" variant="primary">
                                Создать продавца
                            </b-button>
                            <template v-if="sellers">
                                <b-table
                                        id="seller-table"
                                        :items="sellers.data"
                                        :fields="fields"
                                        small
                                        sortDirection="desc"
                                        responsive
                                >
                                    <template v-slot:cell(actions)="row">
                                        <b-button v-b-tooltip="'Профиль'" @click="tryToShowSeller(sellers.data[row.index].id)" size="xs" variant="primary">
                                            <b-icon icon="eye"></b-icon>
                                        </b-button>
                                        <b-button v-b-tooltip="'Редактировать'" @click="tryToChangeSeller(sellers.data[row.index].id)" class="ml-1" size="xs" variant="warning">
                                            <b-icon icon="pencil"></b-icon>
                                        </b-button>
                                        <b-button v-if="row.item.banned === 0" v-b-tooltip="'Заблокировать'" @click="tryToBanSeller(row.item.id)" class="ml-1" size="xs" variant="success">
                                            <b-icon icon="lock" class="mr-1"></b-icon>
                                            <span>Заблокировать</span>
                                        </b-button>
                                        <b-button v-else v-b-tooltip="'Разблокировать'" @click="tryToUnBanSeller(row.item.id)" class="ml-1" size="xs" variant="secondary">
                                            <b-icon icon="unlock" class="mr-1"></b-icon>
                                            <span>Разблокировать</span>
                                        </b-button>
                                        <b-button v-b-tooltip="'Удалить'" size="xs" class="ml-1" variant="danger">
                                            <b-icon icon="trash"></b-icon>
                                        </b-button>
                                    </template>
                                </b-table>

                                <b-pagination
                                        v-model="currentPage"
                                        :total-rows="sellers.data.length"
                                        :per-page="perPage"
                                        aria-controls="news-table"
                                        class="float-right"
                                ></b-pagination>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
    import appConfig from '@src/app.config'
    import Layout from '@layouts/main'
    import PageHeader from '@components/page-header'
    import {sellerMethods} from "@state/helpers";
    import store from '@state/store';
    import {sellerComputed} from "@state/helpers";
    import {BIcon, BIconLock, BIconUnlock, BIconEye, BIconTrash, BIconPencil} from "bootstrap-vue";


    export default {
        name: "IndexSellers",
        page: {
            title: 'Продавцы',
            meta: [{ name: 'description', content: appConfig.description }],
        },
        components: { Layout, PageHeader, BIcon,  BIconUnlock, BIconEye, BIconLock, BIconTrash, BIconPencil},
        data() {
            return {
                title: 'Продавцы',
                items: [
                    {
                        text: 'Главная',
                        href: '/',
                    },
                    {
                        text: 'Продавцы',
                        active: true,
                    },
                ],
                fields: [
                    {
                        key: 'name',
                        label: 'Имя'
                    },
                    {
                        key: 'domain',
                        label: 'Домен'
                    },
                    {
                        key: 'actions',
                        label: 'Действия'
                    },
                ],
                perPage: 20,
                currentPage: 1,
            }
        },
        computed:{
            ...sellerComputed,
        },
        mounted() {
            this.loadSellersList();
        },
        methods:{
            ...sellerMethods,
            tryToShowSeller(id) {
                return this.$router.push({ name: 'CardSeller', params: {id} });
            },
            tryToChangeSeller(id) {
                return this.$router.push({ name: 'UpdateSeller', params: {id} });
            },
            tryToBanSeller(id){
                return this.banSeller(id).then((res) =>{
                    this.loadSellersList();
                });
            },
            tryToUnBanSeller(id){
                return this.unBanSeller(id).then((res) =>{
                    this.loadSellersList();
                });
            }
        }
    }
</script>

<style>
    .button-icon{
        display: inline-block !important;
        text-align: center;
    }
    .button-text{
        font-size: 14px;
        vertical-align: 1px;
    }
</style>