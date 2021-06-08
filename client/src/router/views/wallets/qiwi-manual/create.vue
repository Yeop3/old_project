<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form :product="qiwiManual"
              :errors="errors"
              type="Создать"
              @sumbit-form="tryToCreate"
        />

    </Layout>
</template>

<script>
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import PageHeader from '@components/page-header';
    import {
        qiwiManualMethods,
        qiwiManualomputed
    } from '@state/helpers';
    import Form from '@views/wallets/qiwi-manual/partials/form';

    export default {
        name: "create-wallets-qiwi-manual",
        components: {PageHeader, Layout, Form},
        page: {
            title: 'Создание товара',
            meta: [{name: 'description', content: appConfig.description}],

        },
        computed: {
            ...qiwiManualomputed,
        },
        mounted() {
        },
        data() {
            return {
                title: 'Добавить Qiwi-Кошелек (ручной)',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Qiwi-Кошельки для оплаты',
                        to: '/wallets/qiwi_manual',
                    },
                    {
                        text: 'Добавить Qiwi-Кошелек (ручной)',
                        active: true,
                    },
                ],
                errors: {}
            };
        },
        methods: {
            ...qiwiManualMethods,
            tryToCreate(field) {
                this.create(field).then((res) => {
                    this.$router.push(this.$route.query.redirectFrom || {name: 'index-wallets-qiwi-manual'});
                }).catch((res) => {
                    this.errors = res.response.data.errors;
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                });
            }
        },

    };
</script>

<style lang="scss" module></style>