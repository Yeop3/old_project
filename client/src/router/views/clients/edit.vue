<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
                <div class="card-box" v-if="!spiner">
                    <div class="card mt-2">
                        <div class="card-body">
                            <b-row>
                                <b-col lg="4">
                                    <b-form @submit.prevent="tryToUpdateClient">
                                        <form-input
                                                name="note"
                                                description="* Можете вписать любой комментарий для своего удобства. "
                                                label="Заметка"
                                                type="text"
                                                v-model="clientForm.note"
                                                :size="'sm'"
                                        ></form-input>
                                        <form-input
                                                name="disc_value"
                                                type="number"
                                                description="* Допускаются значения от 0 до 99,9. Если скидки нет - укажите 0. "
                                                label="Размер персональной скидки, %"
                                                v-model="clientForm.discount_value"
                                                :size="'sm'"
                                        ></form-input>
                                        <form-input
                                                name="disc_property"
                                                type="number"
                                                description="* Приоритет персональной скидки сравнивается с приоритетами общих скидок. Для клиента будет выбрана скидка с максимальным приоритетом. При равных приоритетах будет выбрана скидка с максимальным размером. По умолчанию приоритет всех скидок равен 100. Вы можете повысить либо понизить данное значение. "
                                                label="Приоритет персональной скидки"
                                                v-model="clientForm.discount_priority"
                                                :size="'sm'"
                                        ></form-input>
                                        <b-button type="submit" variant="primary">
                                            <i class="remixicon-save-3-line"></i>
                                            Сохранить
                                        </b-button>
                                    </b-form>
                                </b-col>
                            </b-row>
                        </div>
                    </div>
                </div >
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
    import {clientsMethods, clientsComputed} from "@state/helpers";
    import FormInput from "@components/ui/form/FormInput";

    export default {
        name: "clients.edit",
        page: {
            title: 'Клиенты',
            meta: [{name: 'description', content: appConfig.description}],
        },
        components: {Layout, PageHeader, FormInput},
        data() {
            return {
                title: 'Клиенты',
                items: [
                    {
                        text: 'Главная',
                        to: '/',
                    },
                    {
                        text: 'Клиенты',
                        to: '/clients',
                    },
                    {
                        text: 'Изменение данных',
                        active: true
                    }
                ],
                clientForm: {
                    note: '',
                    discount_value: '',
                    discount_priority: '',
                },
            }
        },
        computed:{
            ...clientsComputed,
        },
        mounted() {
            this.loadClient(this.$route.params.number).then((res) => {
                console.log(this.client);
                this.clientForm = Object.assign({}, this.client);
            });
        },
        methods:{
            ...clientsMethods,
            tryToUpdateClient(){
                return this.updateClient(this.clientForm).then((res) => {
                    this.$router.push({name: 'clients.index'});
                });
            }

        }
    };
</script>

<style scoped>

</style>