<template>
    <Layout>
        <PageHeader :title="title" :items="items"/>

        <div class="row">
            <div class="col-md-12">
              <template v-if="!loading">
                    <b-form @submit.prevent="sumbitForm">
                        <div class="row justify-content-between">
                            <div class="col-md-6 col-sm-12">
                                <div class="card-box">
                                    <b-form-group label="Включение-отключение способов оплаты">
                                      <b-form-checkbox
                                        v-for="key in Object.keys(this.settings.payment_method_enable_toggle)"
                                        v-model="selected[key]"
                                        :key="key"
                                        :value="1"
                                        :unchecked-value="0"
                                        switches
                                        stacked
                                      >
                                        {{ key }}
                                      </b-form-checkbox>
                                    </b-form-group>
                                    <b-form-group id="button-group" class="mt-4 column">
                                        <div class="row justify-content-center">
                                            <b-button type="submit" variant="primary" class="btn-block col-4">
                                                Сохранить
                                            </b-button>
                                        </div>
                                    </b-form-group>
                                </div>
                            </div>
                        </div>
                    </b-form>
                  </template>
                <div class="centered" v-else>
                    <b-spinner></b-spinner>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
    import appConfig from "@src/app.config.json";
    import Layout from "@layouts/main";
    import PageHeader from "@components/page-header";
    import FormInput from "@components/ui/form/FormInput";
    import FormSelect from "@components/ui/form/FormSelect";
    import FormCheckbox from "@components/ui/form/FormCheckbox";
    import {mapActions, mapGetters, mapState} from 'vuex';

    export default {
        computed: {
            ...mapGetters('settings', ['settings']),
            ...mapState('settings', {
                loading: (state) => state.spiner,
            }),
        },
        page: {
            title: 'Настройки способов оплаты',
            meta: [{name: 'description', content: appConfig.description}],
        },
        data() {
            return {
                title: 'Настройки способов оплаты',
                errors: {},
                items: [],
                selected: {},
            };
        },
        components: {Layout, PageHeader, FormInput, FormSelect, FormCheckbox},
        async created() {
          await this.loadSettings(['payment_method_enable_toggle'])
          this.selected = {...this.settings.payment_method_enable_toggle}
        },
        methods: {
            ...mapActions('settings', ['loadSettings', 'updateSettings']),
            async sumbitForm() {
              try {
                await this.updateSettings({sections: {payment_method_enable_toggle: this.selected}})
                this.$bvToast.toast('Сохранено', {
                    variant: 'success',
                    autoHideDelay: 5000,
                });
              } catch (e) {
                this.errors = res.response.data.errors;
                this.$bvToast.toast(res.response.data.message, {
                    title: 'Errors',
                    variant: 'danger',
                    autoHideDelay: 5000,
                });
              }
            },
        },
    };
</script>