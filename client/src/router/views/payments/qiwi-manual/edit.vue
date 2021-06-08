<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form :qiwi-manual="qiwiManual"
              :errors="errors"
              type="Обновить"
              :order-select="order_select"
              @sumbit-form="tryUpdate"
              v-if="!spinerAll"
        />
        <div class="centered" v-else>
            <b-spinner></b-spinner>
        </div>
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
    import store from "@state/store";

    export default {
        name: "qiwi-edit",
        components: {PageHeader, Layout, Form},
        page: {
            title: 'Редактирование qiwi',
            meta: [{name: 'description', content: appConfig.description}],

        },
        computed: {
            ...paymentqiwiManualDeletedComputed,
            ...orderComputed
        },
        mounted() {
            this.spinerAll = true;
            Promise.all([
                this.$store.dispatch('payments/qiwiManual/getById', this.$route.params.id),
                this.$store.dispatch('order/getSelect'),
            ]).finally(() => this.spinerAll = false);
        },
        data() {
            return {
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
                errors: {},
                spinerAll: false,
            };
        },
        methods: {
            ...paymentQiwiManualDeletedMethods,
            async tryUpdate(field) {
                try {
                    await this.edit(field);
                    this.$router.push(this.$route.query.redirectFrom || {name: 'index-payments-wallets-qiwi-manual'});
                } catch (res) {
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