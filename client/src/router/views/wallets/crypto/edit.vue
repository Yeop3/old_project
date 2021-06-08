<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form
                :errors="errors"
                :crypto="crypt"
                :proxy-select="proxySelect"
                type="Обновить"
                :is-update="true"
                @submit-form="submitForm"
                v-if="!spiner_crypto"
        ></Form>
        <div class="centered" v-else>
            <b-spinner></b-spinner>
        </div>
    </Layout>
</template>

<script>

    import {cryptoWalletMethods, cryptoWalletComputed,} from "@state/helpers";
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import Form from "@views/wallets/crypto/partials/form";
    import {mapActions, mapState} from "vuex";

    export default {
        name: "edit-crypto",
        page: {
            title: 'Редактировать Крипто-кошелек',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, Form},
        computed: {
            ...cryptoWalletComputed,
            ...mapState('proxy', {
                proxySelect: (state) => state.proxySelect,
            })
        },
        data() {
            return {
                title: 'Редактировать Крипто-кошелек',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Крипто-кошельки',
                        to: '/crypto-wallet/',
                    },
                    {
                        text: 'Редактировать Крипто-кошелек',
                        active: true,
                    },
                ],
                errors: {},
            };
        },
        async created() {
            await this.getCrypt(this.$route.params.id);
            await this.getSelectProxy();
        },
        methods: {
            ...cryptoWalletMethods,
            ...mapActions('proxy', ['getSelectProxy']),
            async submitForm(field) {
                try {
                    await this.update({id: this.$route.params.id, field});
                    this.$router.push({name: "crypto-wallet.index"});
                } catch (err) {
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