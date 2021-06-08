<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Table
                :locations="locations"
                :page-current="page"
                @paginate="pagination"
                @delete-by-id="deleteLocation"
                v-if="!spiner"
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
    import Table from '@views/locations/table';
    import {locationsComputed, locationsMethods, commissionComputed} from "@state/helpers";

    export default {
        name: "index-locations",
        page: {
            title: 'Локации товаров',
            meta: [{name: 'description', content: appConfig.description}],
        },
        computed: {
            ...locationsComputed,
            ...commissionComputed,
        },
        components: {Layout, PageHeader, Table},
        data() {
            return {
                title: 'Локации товаров',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Локации товаров',
                        active: true,
                    },
                ],
            };
        },
        created() {
            this.$store.commit('location/SET_PAGE_LOCATIONS', this.$route.query.page || 1);
            this.getIndex(this.$route.query.page || 1);
        },
        methods: {
            ...locationsMethods,
            pagination(page){
                this.$store.commit('location/SET_PAGE_LOCATIONS', page);
                this.getIndex(this.page);
                this.$router.push({path: `/product-types/?page=${page}`})
            },
            deleteLocation(id){
                this.deleteById(id)
                    .then((res) => {
                        this.getIndex(this.page);
                    }).catch((res) => {
                    this.$bvToast.toast(res.response.data.message, {
                        title: 'Errors',
                        variant: 'danger',
                        autoHideDelay: 5000,
                    })
                });
            }
        },
        watch: {
            $route: 'getIndex'
        },
    };
</script>
<style lang="scss" module></style>