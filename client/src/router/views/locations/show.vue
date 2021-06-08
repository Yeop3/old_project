<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>
        <router-link class="btn btn-primary btn-sm mr-2" tag="a"
                     :to="`/locations/edit/${location.number}`">
            <b-icon icon="pencil"></b-icon>
        </router-link>
        <b-button class="btn" variant="danger" size="sm"
                  @click="deleteLocation(location.number)">
            <b-icon icon="trash"></b-icon>
        </b-button>

        <b-table
                stacked
                :items="[location]"
                :fields="fields"
                :busy.sync="spiner"
        >
            <template v-slot:table-busy>
                <div class="centered">
                    <b-spinner></b-spinner>
                </div>
            </template>
            <template v-slot:cell(commission)="data">
                {{data.item.commission_value}} {{commission_types.find((value) => data.item.commission_type
                === value.value).text}}
            </template>
            <template v-slot:cell(parent)="data">
                <router-link v-if="data.item.parent" class="" tag="a"
                             :to="`/locations/show/${data.item.number}`">
                    {{data.item.parent.name}}
                </router-link>
                <div v-else>
                    Нет
                </div>
            </template>
        </b-table>
    </Layout>
</template>

<script>
    import appConfig from '@src/app.config';
    import Layout from '@layouts/main';
    import PageHeader from '@components/page-header';
    import {locationsComputed, commissionComputed, locationsMethods} from '@state/helpers';
    import {BIcon, BIconPencil, BIconTrash} from "bootstrap-vue";

    export default {
        name: "locations-show",
        components: {
            PageHeader,
            Layout,
            BIcon,
            BIconTrash,
            BIconPencil,
        },
        computed: {
            ...locationsComputed,
            ...commissionComputed,
        },
        page: {
            title: 'Просмотр ',
            meta: [{name: 'description', content: appConfig.description}],
        },
        async created() {
            await this.getById(this.$route.params.id);
            this.title += this.location.name;
            this.items[2].text += this.location.name;
        },
        data() {
            return {
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                    },
                    {
                        key: 'priority',
                        label: 'Приоритет',
                    },
                    {
                        key: 'name',
                        label: 'Имя',
                    },
                    {
                        key: 'commission',
                        label: 'Комиссия',
                    },
                    {
                        key: 'parent',
                        label: 'Родительская локация',
                    },


                ],
                title: 'Просмотр ',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Товары',
                        to: '/product-types',
                    },
                    {
                        text: 'Товар: ',
                        active: true,
                    },
                ],
            };
        },
        methods: {
            ...locationsMethods,
            async deleteLocation(id) {
                const value = await this.$bvModal.msgBoxConfirm('Действительны ли вы хотите это удалить?', {
                    title: 'Пожалуйста подтвердите',
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'danger',
                    okTitle: 'Удалить',
                    cancelTitle: 'Отмена',
                    footerClass: 'p-2',
                    hideHeaderClose: false,
                    centered: true
                });
                if (value) {
                    try {
                        await this.deleteById(id);
                        this.$router.push(this.$route.query.redirectFrom || {name: 'index-location'});
                    } catch (err) {
                        this.$bvToast.toast(err.response.data.message, {
                            title: 'Errors',
                            variant: 'danger',
                            autoHideDelay: 5000,
                        });
                    }
                }

            },
        }
    };
</script>

<style lang="scss" module></style>