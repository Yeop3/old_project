<template>
    <layout>
        <page-header :title="title" :items="items"/>
        <k-table v-if="data"
                :data="data.data"
                :loading="loading"
                @on-delete="onDelete"
                @filter="filter"
        />
        <div v-else class="centered">
            <b-spinner/>
        </div>
    </layout>
</template>

<script>
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import KTable from "@views/wallets/kuna/codes/partials/table";
    import axios from 'axios';

    export default {
        page: {
            title: 'Kuna-коды',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, KTable},
        data() {
            return {
                loading: true,
                apiPath: '/api/kuna/codes/',
                data: null,
                title: 'Kuna-коды',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Kuna-коды',
                        active: true,
                    },
                ],
            };
        },
        async created() {
            await this.fetch()
        },
        methods: {
            async fetch(params) {
                    this.loading = true
                    const res = await axios.get(this.apiPath, {params: {...params, page: 1}});
                    this.data = res.data
                    this.loading = false
            },
            async onDelete(id) {
                try {
                    this.loading = true
                    await axios.delete(this.apiPath + id);
                    await this.fetch()
                } catch (e) {
                   // console.log(e);
                    this.$bvToast.toast(e.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    });
                } finally {
                    this.loading = false
                }
            },
            async filter(filterField){
                let params = {};
                for (let value of filterField) {
                    if (value.value !== null)
                        params[value.name] = value.value;
                }

                await this.fetch(params)
            }
        }
    };
</script>

<style scoped>

</style>