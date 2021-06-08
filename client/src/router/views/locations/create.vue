<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <Form :location="location"
              :errors="errors"
              :locations-select="locations_root_select"
              :drivers-select="drivers_select"
              type="Создать"
              @sumbit-location="tryToCreateLocation"
              :is-edit="false"
        />

    </Layout>
</template>

<script lang="js">
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import PageHeader from '@components/page-header';
    import {driversComputed, locationsComputed, locationsMethods} from '@state/helpers';
    import Form from '@views/locations/form';

    export default {
        name: "locations-create",
        components: {PageHeader, Layout, Form},
        page: {
            title: 'Создание локации',
            meta: [{name: 'description', content: appConfig.description}],

        },
        computed: {
            ...locationsComputed,
            ...driversComputed
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
                        text: 'Локации товаров',
                        to: '/locations',
                    },
                    {
                        text: 'Создание',
                        active: true,
                    },
                ],
                errors: {},
            };
        },
        methods: {
            ...locationsMethods,
          tryToCreateLocation: function (field) {
            this.create(field).then((res) => {
              this.$router.push(this.$route.query.redirectFrom || {name: 'index-location'});
            }).catch((res) => {
              this.errors = res.response.data.errors;
              this.$bvToast.toast(res.response.data.message, {
                title: 'Errors',
                variant: 'danger',
                autoHideDelay: 5000,
              });
            });
          }
        }
    };
</script>

<style lang="scss" module></style>