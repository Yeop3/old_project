<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form
                :dispatch="dispatcher"
                :select-bots="select_bots"
                :errors="errors"
                @sumbit-form="sumbitForm"
        />
    </Layout>
</template>

<script>
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import appConfig from "@src/app.config.json";
    import {botsComputed, dispatcherComputed, dispatcherMethods} from "@state/helpers";
    import Form from "@views/dispatch/partials/form";

    export default {
        name: "create-dispatch",
        components: {Layout, PageHeader, Form},
        computed: {
            ...botsComputed,
            ...dispatcherComputed
        },
        page: {
            title: 'Создать задание на рассылку',
            meta: [{name: 'description', content: appConfig.description}],
        },
        data() {
            return {
                title: 'Создать задание на рассылку',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Рассылки сообщений',
                        to: "/dispatchers",
                    },
                    {
                        text: 'Создать задание на рассылку',
                        active: true,
                    },
                ],
                errors: {},
            };
        },
        methods: {
            ...dispatcherMethods,
            async sumbitForm(fields) {
                try {
                    await this.create(fields);
                    this.$router.push({name:'dispatch.index'});
                }catch (err) {
                    //console.log(err);
                    this.errors = err.response.data.errors;
                    this.$bvToast.toast(err.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }
            }
        }
    };
</script>

<style scoped>

</style>