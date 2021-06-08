<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box row justify-content-center">
                <b-form @submit.prevent="fetch" class="col-6">
                    <form-select
                            name="proxy_id"
                            label="Прокси"
                            v-model="form.proxy_id"
                            :errors="errors"
                            :options="proxyList"
                            :size="'sm'"
                    >
                    </form-select>
                    <form-input
                            name="name"
                            label="Название"
                            v-model="form.name"
                            :errors="errors"
                            description="* Введите любое название (для отображения)."
                            :size="'sm'"
                    >
                    </form-input>

                    <form-input
                            name="public_key"
                            label="Public Key"
                            v-model="form.public_key"
                            :errors="errors"
                            description="* Введите публичный ключ."
                            :size="'sm'"
                    >
                    </form-input>

                    <form-input
                            name="private_key"
                            label="Private Key"
                            v-model="form.private_key"
                            :errors="errors"
                            description="* Введите приватный ключ."
                            :size="'sm'"
                    >
                    </form-input>

                    <form-textarea
                            name="comment"
                            label="Комментарий"
                            v-model="form.comment"
                            :errors="errors"
                            description="Можно указать любой комментарий."
                    >
                    </form-textarea>

                    <form-select
                            name="active"
                            label="Статус"
                            v-model="form.active"
                            :errors="errors"
                            :options="activeStatusList"
                            :size="'sm'"
                    >
                    </form-select>

                    <b-form-group id="button-group" class="mt-4 column">
                        <div class="row justify-content-center">
                            <b-button :disabled="loading" type="submit" variant="primary" class="btn-block col-4">
                                <b-spinner v-if="loading" small />
                                {{ buttonText }}
                            </b-button>
                        </div>
                    </b-form-group>
                </b-form>
            </div>
        </div>
    </div>
</template>

<script>
    import FormInput from "@components/ui/form/FormInput";
    import FormTextarea from "@components/ui/form/FormTextarea";
    import FormSelect from "@components/ui/form/FormSelect";
    import { mapActions, mapGetters } from "vuex"

    export default {
        components: {FormSelect, FormTextarea, FormInput},
        props: {
            loading: {
                type: Boolean
            },
            buttonText: {
                type: String,
            },
            data: {
                type: Object,
            },
            errors: {
                type: Object
            },
        },
        data() {
            return {
                form: {},
                activeStatusList: [
                    {
                        text: "Активный", value: true
                    },
                    {
                        text: "Неактивный", value: false
                    },
                ]
            };
        },
        async created() {
            await this.getSelectProxy()
            this.form = {...this.data}
            this.setProxyId()
        },
        computed: {
            ...mapGetters('proxy', ['proxyList'])
        },
        methods: {
            ...mapActions('proxy', ['getSelectProxy']),
            fetch() {
                this.$emit('submit-form', this.form)
            },
            setProxyId() {
                const proxy = this.proxyList.find(p => this.form.proxy && p.value == this.form.proxy.number)
                this.form.proxy_id = proxy ? proxy.value : null
            }
        },
    };
</script>

<style scoped>

</style>