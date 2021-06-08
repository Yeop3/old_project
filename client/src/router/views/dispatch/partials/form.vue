<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box row justify-content-center">
                <b-form class="col-sm-12 col-lg-6" @submit.prevent="sumbitForm">

                    <FormMultiselect label="Боты" name="bots_numbers" :close-on-select="false" option-label="text" multiple track-by="value" :options="selectBots" v-model="fields.bots_numbers" class="mb-2" />

                    <FormTextarea
                            label="Сообщение"
                            name="message"
                            v-model="fields.messages"
                            :errors="errors"
                    >
                    </FormTextarea>

                    <b-form-group id="button-group" class="mt-4 column">
                        <div class="row justify-content-center">
                            <b-button type="submit" variant="success" class="btn-block col-lg-4 col-md-5 col-sm-6">
                                Поставить в очередь
                            </b-button>
                        </div>
                    </b-form-group>
                </b-form>
                <div class="col-sm-12 col-lg-6">
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
    import FormSelect from "@components/ui/form/FormSelect";
    import FormMultiselect from '@components/ui/form/FormMultiSelect';
    export default {
        components: {FormInput, FormTextarea, FormSelect, FormMultiselect},
        props:{
            selectBots: {
                type: Array
            },
            errors: {
                type: Object
            }
        },
        data(){
            return{
                fields:{
                    bots_numbers: [],
                    messages: ''
                }

            }
        },
        created() {
            this.fields.bots_numbers = this.selectBots
        },
        methods: {
            sumbitForm(){
                this.$emit('sumbit-form', {
                    ...this.fields, 
                    bots_numbers: this.fields.bots_numbers.filter(b => b.value).map(b => b.value)
                });
            }
        }
    };
</script>

<style scoped>

</style>