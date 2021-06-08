<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box" v-if="!spiner">
                    <div class="card mt-2">
                        <div class="card-body row justify-content-center">
                            <b-form @submit.prevent="tryToUpdateProxy" class="col-5">
                                <form-select
                                        name="type"
                                        label="Тип"
                                        v-model="updateProxyForm.proxy_type"
                                        :options="proxies"
                                        :errors="errors"
                                        :size="'sm'"
                                ></form-select>
                                <b-row>
                                    <b-col lg="8">
                                        <form-input
                                                name="ip"
                                                label="IP/Host"
                                                v-model="updateProxyForm.ip"
                                                :errors="errors"
                                                :size="'sm'"
                                        ></form-input>
                                    </b-col>
                                    <b-col lg="4">
                                        <form-input
                                                name="port"
                                                label="Порт"
                                                v-model="updateProxyForm.port"
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
                                                v-model="updateProxyForm.username"
                                                :errors="errors"
                                                :size="'sm'"
                                        ></form-input>
                                    </b-col>
                                    <b-col lg="6">
                                        <form-input
                                                name="password"
                                                type="password"
                                                label="Password"
                                                v-model="updateProxyForm.password"
                                                :errors="errors"
                                                :size="'sm'"
                                        ></form-input>
                                    </b-col>
                                </b-row>
                                <form-input
                                        name="note"
                                        label="Заметка"
                                        v-model="updateProxyForm.note"
                                        description="* Можете вписать любой комментарий для своего удобства. "
                                        :size="'sm'"
                                ></form-input>
                                <b-button type="submit" variant="primary">
                                    <i class="remixicon-save-3-line"></i>
                                    Сохранить
                                </b-button>
                            </b-form>
                        </div>
                    </div>
                </div>
                <div class="centered" v-else>
                    <b-spinner></b-spinner>
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


    export default {
        name: "proxies.edit",
        page: {
            title: 'Прокси',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {FormInput, Layout, PageHeader, FormSelect},
        data() {
            return {
                title: 'Изменение прокси',
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
                        text: 'Изменить прокси',
                        active: true,
                    },
                ],
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
                updateProxyForm: {},
                errors: {},
            }
        },
        computed:{
            ...proxyComputed,
        },
        mounted() {
            this.loadProxy(this.$route.params.number).then((res) => {
                this.updateProxyForm = Object.assign({}, this.proxy);
            });
        },
        methods:{
            ...proxyMethods,
            tryToUpdateProxy(){
                this.errors = {};
                this.updateProxy(this.updateProxyForm)
                    .then(() => {
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