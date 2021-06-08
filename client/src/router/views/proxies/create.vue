<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card mt-2">
                        <div class="card-body row justify-content-center">
                            <b-form @submit.prevent="tryToCreateProxy" class="col-lg-5 col-md-7 col-sm-12">
                                <form-select
                                        name="type"
                                        label="Тип"
                                        v-model="createProxyForm.proxy_type"
                                        :options="proxies"
                                        :errors="errors"
                                        :size="'sm'"
                                ></form-select>
                                <b-row>
                                    <b-col lg="8">
                                        <form-input
                                                name="ip"
                                                label="IP/Host"
                                                v-model="createProxyForm.ip"
                                                :errors="errors"
                                                :size="'sm'"
                                        ></form-input>
                                    </b-col>
                                    <b-col lg="4">
                                        <form-input
                                                name="port"
                                                label="Порт"
                                                v-model="createProxyForm.port"
                                                :errors="errors"
                                                :size="'sm'"
                                        ></form-input>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col lg="6">
                                        <form-input
                                                name="username"
                                                label="Username"
                                                v-model="createProxyForm.username"
                                                :errors="errors"
                                                :size="'sm'"
                                        ></form-input>
                                    </b-col>
                                    <b-col lg="6">
                                        <form-input
                                                name="password"
                                                type="password"
                                                label="Password"
                                                v-model="createProxyForm.password"
                                                :errors="errors"
                                                :size="'sm'"
                                        ></form-input>
                                    </b-col>
                                </b-row>
                                <form-input
                                        name="note"
                                        label="Заметка"
                                        v-model="createProxyForm.note"
                                        description="* Можете вписать любой комментарий для своего удобства. "
                                        :size="'sm'"
                                ></form-input>
                                <b-form-group id="button-group" class="mt-4 column">
                                    <div class="row justify-content-center">
                                        <b-button type="submit" variant="primary" class="btn-block col-4">
                                            Сохранить
                                        </b-button>
                                    </div>
                                </b-form-group>
                            </b-form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
    import appConfig from '@src/app.config'
    import Layout from '@layouts/main'
    import PageHeader from '@components/page-header'
    import {proxyMethods, proxyComputed} from "@state/helpers";
    import FormInput from "@components/ui/form/FormInput";
    import FormSelect from "@components/ui/form/FormSelect";
    import {BIcon} from "bootstrap-vue";


    export default {
        name: "proxies.create",
        page: {
            title: 'Прокси',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {FormInput, Layout, PageHeader, FormSelect, BIcon},
        data() {
            return {
                title: 'Добавление прокси',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Прокси',
                        to: '/proxies',
                    },
                    {
                        text: 'Добавить прокси',
                        active: true,
                    },
                ],
                createProxyForm: {
                    proxy_type: '',
                    ip: '',
                    port: '',
                    username: '',
                    password: '',
                    note: '',
                },
                proxies: [
                    {
                        text: 'socks5',
                        value: 'socks5'
                    },
                    {
                        text: 'http',
                        value: 'http'
                    },
                    {
                        text: 'https',
                        value: 'https'
                    }
                ],
                errors: {},
            }
        },
        computed:{
            ...proxyComputed,
        },
        mounted() {
        },
        methods:{
            ...proxyMethods,
            tryToCreateProxy(){
                this.errors = {};
                this.createProxy(this.createProxyForm)
                    .then((res) => {
                        this.$router.push({name: 'proxies.index'})
                    })
                    .catch(err => {
                        this.errors = err.response.data.errors;
                        this.$bvToast.toast(err.response.data.message, {
                            title: 'Errors',
                            variant: 'danger',
                            autoHideDelay: 5000,
                        });
                    });
            }
        }
    };
</script>

<style scoped>

</style>