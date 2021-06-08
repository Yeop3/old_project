<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form :discount="discount"
              :errors="errors"
              :location-select="locations_select"
              :product-types-select="product_types_select"
              type="Обновить"
              @sumbit-form="tryToEdit"
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
        discountsMethods,
        discountsComputed,
        locationsComputed,
        productTypesComputed
    } from '@state/helpers';
    import Form from '@views/discounts/partials/form';

    export default {
        name: "product-edit",
        components: {PageHeader, Layout, Form},
        page: {
            title: 'Изменить скидку',
            meta: [{name: 'description', content: appConfig.description}],

        },
        computed: {
            ...discountsComputed,
            ...locationsComputed,
            ...productTypesComputed,
        },
        mounted() {
            this.spinerAll = true;
            Promise.all([
                this.getById(this.$route.params.id),
                this.$store.dispatch('location/getSelect'),
                this.$store.dispatch('productTypes/getSelectProductType')
            ]).finally(() => this.spinerAll = false);
            this.$store.commit('productTypes/PRODUCT_TYPES_SELECT_SHIFT');
            this.$store.commit('location/LOCATIONS_SELECT_PRODUCT_SHIFT');

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
                        text: 'Скидки',
                        to: '/discounts',
                    },
                    {
                        text: 'Редактирование',
                        active: true,
                    },
                ],
                errors: {},
                spinerAll: false,
            };
        },
        methods: {
            ...discountsMethods,
            tryToEdit(field) {
                this.edit(field).then((res) => {
                    this.$router.push(this.$route.query.redirectFrom || {name: 'index-discounts'});
                }).catch((res) => {
                    this.errors = res.response.data.errors || {};
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