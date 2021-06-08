<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <Form :nameParent="product_type" :errors="errors" type="Создать" @sumbit-product-type="tryToCreateProductType"/>

    </Layout>
</template>

<script>
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import PageHeader from '@components/page-header';
    import {productTypesMethods, productTypesComputed} from '@state/helpers';
    import Form from '@views/product-types/form';

    export default {
        name: "CreateProductType",
        components: {PageHeader, Layout, Form},
        computed: {
            ...productTypesComputed,
        },
        page: {
            title: 'Создание товара',
            meta: [{name: 'description', content: appConfig.description}],
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
                        text: 'Товары',
                        to: '/product-types',
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
            ...productTypesMethods,
            tryToCreateProductType(field) {
                this.create(field).then((res) => {
                    this.$router.push(this.$route.query.redirectFrom || {name: 'index-product-types'});
                }).catch((res) => {
                    this.errors = res.response.data.errors;
                });
            }
        }

    };
</script>

<style lang="scss" module></style>