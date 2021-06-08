<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form :product="product"
              :errors="errors"
              :locations-select="locations_child_select"
              :drivers-select="drivers_select"
              :product-types-select="product_types_select.filter(pt => pt.delivery_type === 1)"
              :status-list="status_list"
              type="Обновить"
              @sumbit-form="tryToEditProduct"
              v-if="!spiners_all"
              :submitting="submitting"
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
        locationsComputed,
        productComputed,
        productMethods,
        driversComputed,
        productTypesComputed
    } from '@state/helpers';
    import Form from '@views/products/partials/form';

    export default {
        name: "product-create",
        components: {PageHeader, Layout, Form},
        page: {
            title: 'Редактирование товара',
            meta: [{name: 'description', content: appConfig.description}],

        },
        computed: {
            ...locationsComputed,
            ...productComputed,
            ...driversComputed,
            ...productTypesComputed,
        },
        mounted() {
            this.spiners_all = true;
            Promise.all([
                this.$store.dispatch('product/getById', this.$route.params.id),
                this.$store.dispatch('location/getSelect'),
                this.$store.dispatch('drivers/getSelectDriver'),
                this.$store.dispatch('productTypes/getSelectProductType'),
                this.$store.dispatch('product/getStatusList'),
            ]).finally(() => this.spiners_all = false);
        },
        data() {
            return {
                title: 'Редактирование',
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
                        text: 'Редактирование',
                        active: true,
                    },
                ],
                errors: {},
                spiners_all: false,
                submitting: false,
            };
        },
        methods: {
            ...productMethods,
            tryToEditProduct(field) {
                this.submitting = true;
                this.edit(field).then((res) => {
                    this.$router.push(this.$route.query.redirectFrom || {name: 'index-products'});
                }).catch((res) => {
                    this.errors = res.response.data.errors || {};
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }).finally(() => {
                  this.submitting = false;
                });
            }
        },

    };
</script>

<style lang="scss" module></style>