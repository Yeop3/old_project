<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form :discount="discount"
              :errors="errors"
              :location-select="locations_select"
              :product-types-select="product_types_select"
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
        discountsMethods,
        discountsComputed,
        locationsComputed,
        productTypesComputed
    } from '@state/helpers';
    import Form from '@views/discounts/partials/form';

    export default {
        name: "discount-create",
        components: {PageHeader, Layout, Form},
        page: {
            title: 'Создание скидки',
            meta: [{name: 'description', content: appConfig.description}],

        },
        computed: {
            ...discountsComputed,
            ...locationsComputed,
            ...productTypesComputed,
        },
        mounted() {

            //console.log();
        },
        data() {
            return {
                title: 'Создание',
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
                        text: 'Создание',
                        active: true,
                    },
                ],
                errors: {}
            };
        },
        methods: {
            ...discountsMethods,
            tryToCreate(field) {
                this.create(field).then((res) => {
                    this.$router.push(this.$route.query.redirectFrom || {name: 'index-discounts'});
                }).catch((res) => {
                    this.errors = res.response.data.errors || null;
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