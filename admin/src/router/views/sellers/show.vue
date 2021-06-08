<template>
    <Layout>
        <PageHeader :title="title" :items="items" />
        <div class="row">
            <div class="col-md-12">
                <div class="card-box row justify-content-center">
                    <b-form class="col-lg-5 col-md-8 col-sm-12">
                        <b-form-group
                                label="Имя"
                                label-for="input-seller-name"
                        >
                            <b-form-input
                                    id="input-seller-name"
                                    v-model="name"
                                    type="text"
                                    required
                                    placeholder="Введите имя"
                                    disabled
                            ></b-form-input>
                        </b-form-group>

                        <b-form-group
                                label="Домен"
                                label-for="input-seller-domain"
                        >
                            <b-form-input
                                    id="input-seller-domain"
                                    v-model="domain"
                                    type="text"
                                    required
                                    placeholder="Введите домен"
                                    disabled
                            ></b-form-input>
                        </b-form-group>

                        <b-form-group id="button-group" class="mt-4 column">
                            <div class="row justify-content-center">
                                <b-button @click="tryToChangeSeller(id)" variant="primary" class="btn-block col-lg-4 col-md-5 col-sm-5">
                                    Редактировать
                                </b-button>
                            </div>
                        </b-form-group>
                    </b-form>
                </div>
            </div>
        </div>
    </Layout>
    
</template>

<script>
    import appConfig from '@src/app.config'
    import Layout from '@layouts/main'
    import PageHeader from '@components/page-header'
    import { sellerMethods } from '@state/helpers'
    import {sellerComputed} from "@state/helpers";

    export default {
        name: "CardSeller",
        page: {
            title: 'Карточка продавца',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader},
        data() {
            return {
                title: 'Карточка продавца',
                items: [
                    {
                        text: 'Главная',
                        href: '/',
                    },
                    {
                        text: 'Продавцы',
                        href: '/sellers',
                    },
                    {
                        text: 'Карточка продавца',
                        active: true,
                    },
                ],
                name: '',
                domain: '',
                id: '',
            }

        },
        computed: {
            ...sellerComputed,

        },

        mounted() {
            this.getCardSeller({id:this.$route.params.id}).then((seller) => {
                this.name = this.seller.name;
                this.domain = this.seller.domain;
                this.id = this.seller.id;
            });
        },
        methods: {
            ...sellerMethods,
            tryToChangeSeller(id) {
                return this.$router.push({ name: 'UpdateSeller', params: id })

            },
        }
    }
</script>

<style lang="scss" module></style>