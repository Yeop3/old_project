<template>
    <layout>
        <page-header :title="title" :items="items"/>
        <k-form v-if="formData"
                :errors="errors"
                button-text="Применить изменения"
                :data="formData"
                @submit-form="submitForm"
                :loading="loading"
        ></k-form>
        <div v-else class="centered">
            <b-spinner/>
        </div>
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
                apiPath: '/api/kuna/accounts/',
                loading: false,
                formData: null,
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
        async created() {
            const res = await axios.get(this.apiPath + this.$route.params.id)
            this.formData = res.data
        },
        methods: {
            async submitForm(form) {
                try {
                    this.loading = true
                    await axios.put(this.apiPath + this.$route.params.id, form)
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