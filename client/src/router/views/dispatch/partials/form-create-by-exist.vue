<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box row justify-content-center">
                <b-form class="col-6" @submit.prevent="sumbitForm">
                    <b-input-group size="lg" prepend="Бот">
                        <b-form-input v-model="bot.username" disabled></b-form-input>
                    </b-input-group>
                    <input hidden v-model="field.bot_id">
                    <FormTextarea
                            label="Сообщение"
                            name="message"
                            :description="`Текст рассылки по наличию автоматически генерируется исходя из настроек логики
                             «${bot.logic.name}», по которой работает бот «${bot.name}».
                             При необходимости вы можете отредактировать данное сообщение`"
                            v-model="field.messages"
                            :errors="errors"
                    >

                    </FormTextarea>

                    <b-form-group id="button-group" class="mt-4 column">
                        <div class="row justify-content-center">
                            <b-button type="submit" variant="success" class="btn-block col-4">
                                Поставить в очередь
                            </b-button>
                        </div>
                    </b-form-group>
                </b-form>
                <div class="col-6">
                    <b-card>
                        <div>
                            <p>
                                В сообщениях для ботов типа <b>«Стандартный Telegram»</b>
                                разрешены следующие HTML-теги:
                            </p>
                            <ul>
                                <li><<b>b</b>><<b>/b</b>> - жирный</li>
                                <li><<b>strong</b>><<b>/strong</b>> - жирный</li>
                                <li><<b>i</b>><<b>/i</b>> - курсив</li>
                                <li><<b>em</b>><<b>/em</b>> - курсив</li>
                                <li><<b>a</b> href="URL"><<b>/a</b>> - ссылка</li>
                                <li><<b>code</b>><<b>/code</b>> - код с фиксированной шириной</li>
                                <li><<b>pre</b>><<b>/pre</b>> - предварительно отформатированный блок кода с
                                    фиксированной шириной
                                </li>
                            </ul>
                            <p>Для ботов типа <b>«Клиентский Telegram»</b>
                                HTML-теги работать не будут!</p>
                        </div>
                    </b-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import FormInput from "@components/ui/form/FormInput";
    import FormTextarea from "@components/ui/form/FormTextarea";

    export default {
        name: "dispatcher-form-create-by-exist",
        components: {FormInput, FormTextarea},
        props: {
            dispatch: {
                type: Object,
            },
            bot: {
                type: Object,
            },
            errors: {
                type: Object
            }
        },
        computed: {},
        data() {
            return {
                field: {},
            };
        },
        mounted() {
            this.field = {...this.dispatch};
            this.field.bot_id = this.bot.number;
            //console.log(this.bot);
        },
        methods:{
            sumbitForm(){
                this.$emit('sumbit-form', this.field);
            }
        }
    };
</script>

<style scoped>

</style>