<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <template v-if="!spiner">
            <h4 class="header-title">
                <b-dropdown lazy id="dropdown-1" text="Добавить кошелек" variant="primary" class="m-2" size="xs">
                    <b-dropdown-item>Автоматический кошелек</b-dropdown-item>
                    <b-dropdown-item>
                        <router-link tag="a" to="/wallets/qiwi_manual/create/">Ручной кошелек
                        </router-link>
                    </b-dropdown-item>
                </b-dropdown>

                <router-link tag="a" class="btn btn-outline-warning btn-xs" to="/wallets/qiwi_manual/deleted/">
                    <b-icon-trash/>
                    Корзина кошельков
                </router-link>
            </h4>
            <b-card no-body>
                <b-tabs card content-class="mt-3">
                    <b-tab :key="item.number" :title="item.phone" v-for="item in qiwiManuals.data">
                        <b-card no-body>
                            <b-tabs pills card vertical>
                                <b-tab lazy>
                                    <template v-slot:title>
                                        <b-icon-eye/>
                                        Просмотр
                                    </template>
                                    <b-card-text>
                                        <b-card>
                                            <div class="inline-block">
                                                Добавлен: {{new Date(item.created_at).toLocaleString("ru-RU", {year:
                                                'numeric',
                                                month: 'long',
                                                day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                                            </div>

                                            <TableInfo :item="item"></TableInfo>

                                        </b-card>
                                    </b-card-text>
                                </b-tab>
                                <b-tab lazy>
                                    <template v-slot:title>
                                        <b-icon-pencil/>
                                        Редактировать
                                    </template>
                                    <b-card-text>
                                        <Form
                                                :qiwi-manual="item"
                                                type="Обновить"
                                                @sumbit-form="tryUpdate"
                                                :errors="errors"
                                                :is-update="true"
                                        ></Form>
                                    </b-card-text>
                                </b-tab>
                                <b-tab lazy>
                                    <template v-slot:title>
                                        <b-icon-trash/>
                                        Удалить
                                    </template>
                                    <b-card-text>
                                        <FormDelete
                                                :item="item"
                                                @delete-by-id="deleteWallet"
                                                @delete-forever="deleteForever"
                                        >
                                        </FormDelete>
                                    </b-card-text>
                                </b-tab>
                            </b-tabs>
                        </b-card>
                    </b-tab>
                </b-tabs>
            </b-card>
            <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    aria-controls="drivers-table"
                    align="left"
                    @change="paginate"
                    v-if="qiwiManuals.last_page > 1"
            ></b-pagination>
        </template>
        <div class="centered" v-else>
            <b-spinner></b-spinner>
        </div>
    </Layout>
</template>

<script>
    import {qiwiManualMethods, qiwiManualomputed} from "@state/helpers";
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';
    import TableInfo from "@views/wallets/qiwi-manual/partials/table";
    import Form from "@views/wallets/qiwi-manual/partials/form";
    import FormDelete from "@views/wallets/qiwi-manual/partials/form-delete";

    export default {
        name: "index-wallets-qiwi-manual",
        computed: {
            ...qiwiManualomputed,
        },
        page: {
            title: 'Qiwi-Кошельки для оплаты',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {
            Layout,
            PageHeader,
            BIcon,
            BIconTrash,
            BIconPencil,
            BIconEye,
            TableInfo,
            Form,
            FormDelete
        },
        async created() {
            await this.getIndex(this.$route.query.page);
            this.currentPage = this.qiwiManuals.current_page;
            this.perPage = this.qiwiManuals.per_page;
            this.totalRows = this.qiwiManuals.total;
        },
        methods: {
            ...qiwiManualMethods,
            async tryUpdate(field) {
                try {
                    await this.edit(field);
                    await this.getIndex(this.page);
                } catch (res) {
                    this.errors = res.response.data.errors;
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }
            },
            async paginate(pageNum) {
                await this.getIndex(pageNum);
                this.$router.push({path: `/wallets/qiwi_manual?page=${pageNum}`});
            },
            async deleteWallet(id) {
                try {
                    await this.deleteById(id);
                    await this.getIndex(this.page);
                } catch (res) {
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }
            },
            async deleteForever(id) {
                try {
                    await this.deleteForeverById(id);
                    await this.getIndex(this.page);
                } catch (res) {
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                }
            }
        },
        watch: {
            $route: 'getIndex'
        },
        data() {
            return {
                title: 'Qiwi-Кошельки для оплаты',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Qiwi-Кошельки для оплаты',
                        active: true,
                    },
                ],
                errors: {},
                currentPage: 1,
                perPage: 1,
                totalRows: 1
            };
        },
    };
</script>

<style scoped>

</style>