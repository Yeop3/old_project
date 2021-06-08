<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form :qiwi-manual="qiwiManual"
              :errors="errors"
              type="Создать"
              :order-select="order_select"
              @sumbit-form="tryCreate"
              :order-number="order_number"
        />
    </Layout>
</template>

<script>
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import PageHeader from '@components/page-header';
    import {
        paymentQiwiManualDeletedMethods,
        paymentqiwiManualDeletedComputed,
        orderComputed
    } from '@state/helpers';
    import Form from '@views/payments/qiwi-manual/partials/form';

    export default {
        name: "qiwi-create",
        components: {PageHeader, Layout, Form},
        page: {
            title: 'Создание qiwi',
            meta: [{name: 'description', content: appConfig.description}],

        },
        computed: {
            ...paymentqiwiManualDeletedComputed,
            ...orderComputed
        },
        created() {
            this.order_number = this.$route.query.order || null;
        },
        data() {
            return {
                order_number: null,
                title: 'Создать ручную Qiwi-Оплату',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Ручные Qiwi-Оплаты',
                        to: '/payments/qiwi_manua ',
                    },
                    {
                        text: 'Создать ручную Qiwi-Оплату',
                        active: true,
                    },
                ],
                errors: {}
            };
        },
        methods: {
            ...paymentQiwiManualDeletedMethods,
            async tryCreate(field) {
                try {
                    await this.create(field);
                    this.$router.push(this.$route.query.redirectFrom || {name: 'index-payments-wallets-qiwi-manual'});
                }catch (res) {
                    this.errors = res.response.data.errors;
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }
            }
        },

    };
</script>

<style lang="scss" module></style>