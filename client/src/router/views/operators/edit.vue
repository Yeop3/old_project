<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form :operator="operator"
              :errors="errors"
              type="Обновить"
              @sumbit-form="tryToUpdate"
        />

    </Layout>
</template>

<script>
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import PageHeader from '@components/page-header';
    import {
        operatorMethods,
        operatorComputed
    } from '@state/helpers';
    import Form from '@views/operators/partials/form';

    export default {
        name: "operator-edit",
        components: {PageHeader, Layout, Form},
        page: {
            title: 'Редактирование оператора',
            meta: [{name: 'description', content: appConfig.description}],
        },
        computed: {
            ...operatorComputed,
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
                        text: 'Операторы',
                        to: '/operators',
                    },
                    {
                        text: 'Обновление',
                        active: true,
                    },
                ],
                errors: {}
            };
        },
        methods: {
            ...operatorMethods,
            tryToUpdate(field) {
                this.edit(field).then((res) => {
                    this.$router.push(this.$route.query.redirectFrom || {name: 'index-operators'});
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