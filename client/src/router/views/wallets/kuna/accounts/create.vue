<template>
    <layout>
        <page-header :title="title" :items="items"/>
        <k-form
                :errors="errors"
                button-text="Создать"
                :data="formData"
                @submit-form="submitForm"
                :loading="loading"
        ></k-form>
    </layout>
</template>

<script>

    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import KForm from "@views/wallets/kuna/accounts/partials/form";
    import { mapActions, mapGetters } from "vuex";
    import axios from 'axios';

    export default {
        page: {
            title: 'Добавить Kuna-аккаунт',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, KForm},
        data() {
            return {
                loading: false,
                formData: {
                    proxy_id: null,
                    name: null,
                    public_key: null,
                    private_key: null,
                    comment: null,
                    active: true
                },
                title: 'Добавить Kuna-аккаунт',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Kuna-аккаунты',
                        to: '/kuna-accounts/',
                    },
                    {
                        text: 'Добавить Kuna-аккаунт',
                        active: true,
                    },
                ],
                errors: {},
            };
        },
        methods: {
            async submitForm(form) {
                try {
                    this.loading = true
                    await axios.post('/api/kuna/accounts', form)
                    this.$router.push({name: "kuna-accounts.index"});
                } catch (err) {
                    this.errors = err.response.data.errors;
                    this.$bvToast.toast(err.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                } finally {
                    this.loading = false
                }
            }
        }
    };
</script>

<style scoped>

</style>