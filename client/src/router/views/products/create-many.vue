<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form :product="product"
              :errors="errors"
              :locations-select="locations_child_select"
              :drivers-select="drivers_select"
              :product-types-select="product_types_select"
              :status-list="status_list"
              type="Создать"
              @sumbit-form="tryToCreateProduct"
              :is-create-many="true"
        />

    </Layout>
</template>

<script>
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import PageHeader from '@components/page-header';
    import {
        locationsComputed,
        productComputed,
        productMethods,
        driversComputed,
        productTypesComputed
    } from '@state/helpers';
    import Form from '@views/products/partials/form';

    export default {
        name: "product-create-many",
        components: {PageHeader, Layout, Form},
        page: {
            title: 'Создание товара',
            meta: [{name: 'description', content: appConfig.description}],

        },
        computed: {
            ...locationsComputed,
            ...productComputed,
            ...driversComputed,
            ...productTypesComputed,
        },
        mounted() {
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
                        text: 'Клады',
                        to: '/products',
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
            ...productMethods,
            tryToCreateProduct(field) {
                this.createMany(field).then((res) => {
                    this.$router.push(this.$route.query.redirectFrom ? this.$route.query.redirectFrom : {name: 'index-products'});
                   // console.log(res);
                }).catch((res) => {
                   // console.log(res.response.data.errors);
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