<template>
    <Layout>
        <PageHeader :title="title" :items="items" />

        <div class="row">
            <div class="col-md-12">
                <div class="card-box row justify-content-center">
                    <b-form @submit.prevent="tryToUpdateSeller" class="col-lg-5 col-md-8 col-sm-12">
                        <form-input
                                name="name"
                                label="Имя"
                                v-model="form.name"
                                :errors="errors"
                                placeholder="Введите имя"
                                :size="'sm'"
                        />

                        <form-input
                                name="domain"
                                label="Домен"
                                v-model="form.domain"
                                :errors="errors"
                                placeholder="Введите домен"
                                :size="'sm'"
                        />

                        <form-input
                                name="password"
                                label="Пароль"
                                v-model="form.password"
                                :errors="errors"
                                placeholder="Введите пароль"
                                type="password"
                                :size="'sm'"
                        />

                        <form-input
                                name="password_confirmation"
                                label="Подтверждение пароля"
                                v-model="form.password_confirmation"
                                :errors="errors"
                                placeholder="Подтвердите пароль"
                                type="password"
                                :size="'sm'"
                        />

                        <b-form-group id="button-group" class="mt-4 column">
                            <div class="row justify-content-center">
                                <b-button type="submit" variant="primary" class="btn-block col-lg-4 col-md-5 col-sm-5">
                                    Изменить
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
    import FormInput from '@components/ui/form/FormInput';


    export default {
        name: "UpdateSeller",
        page: {
            title: 'Изменение данных',
            meta: [{ name: 'description', content: appConfig.description }],
        },
        components: { Layout, PageHeader, FormInput},
        data() {
            return {
                title: 'Изменение данных',
                items: [
                    {
                        text: 'Главная',
                        href: '/',
                    },
                    {
                        text: 'Продавцы',
                        href: '/sellers'
                    },
                    {
                        text: 'Изменение данных',
                        active: true
                    }
                ],
                form:{
                    id: '',
                    name: '',
                    domain: '',
                    password: '',
                    password_confirmation: '',
                },
                errors: {},
            }

        },
        computed:{
            ...sellerComputed,

        },
        mounted() {
            this.getCardSeller({id:this.$route.params.id}).then((seller) => {
                this.form.name = this.seller.name;
                this.form.domain = this.seller.domain;
                this.form.id = this.seller.id;
            });
        },
        methods: {
            ...sellerMethods,
            tryToUpdateSeller() {
                this.errors = {};
                return this.updateSeller(this.form)
                .then(() =>{
                    this.$router.push({ name: 'IndexSellers' })
                })
                .catch((res) => {
                    this.errors = res.response.data.errors;
                })
            }
        }
    }
</script>

<style lang="scss" module></style>