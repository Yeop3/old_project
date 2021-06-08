<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="card-box">
                <h4 class="header-title">
                    <router-link class="btn btn-primary mb-2 btn-xs" tag="a" to="/kuna-accounts/create">Добавить
                    </router-link>
                </h4>
                <b-overlay :show="loading" rounded="sm" no-center>
                    <template v-slot:overlay>
                        <div class="centered">
                            <b-spinner variant="secondary"></b-spinner>
                        </div>
                    </template>
                    <div class="table-responsive">
                        <b-table
                                :items="data"
                                :fields="fields"
                                small
                                :sort-by.sync="sortBy"
                                :sort-desc.sync="sortDesc"
                                sortDirection="desc"
                        >
                            <template v-slot:top-row="columns">
                                <td></td>
                                <td>
                                    <b-form-input
                                            :name="filterField.find((value) => value.name === 'name').name"
                                            v-model="filterField.find((value) => value.name === 'name').value"
                                            type="text"
                                            trim
                                            :debounce="1000"
                                            size="sm"
                                    ></b-form-input>
                                </td>
                                <td></td>
                                <td></td>
                                <td>
                                    <b-form-select size="sm" v-model="filterField.find((value) => value.name === 'active').value">
                                        <b-form-select-option :value="null">Все</b-form-select-option>
                                        <b-form-select-option :value="1">Активный</b-form-select-option>
                                        <b-form-select-option :value="0">Неактивный</b-form-select-option>
                                    </b-form-select>
                                </td>
                                <td>
                                    <b-form-input
                                            :name="filterField.find((value) => value.name === 'comment').name"
                                            v-model="filterField.find((value) => value.name === 'comment').value"
                                            type="text"
                                            trim
                                            :debounce="1000"
                                            size="sm"
                                    ></b-form-input>
                                </td>
                            </template>
                            <template v-slot:cell(created_at)="data">
                                <template v-if="data.item.created_at">
                                   {{ getHumanDate(data.item.created_at) }}
                                </template>
                            </template>
                            <template v-slot:cell(active)="data">
                                <template v-if="data.item.active">
                                   {{ data.item.active == true ? 'Активный' : 'Неактивный' }}
                                </template>
                            </template>
                            <template v-slot:cell(proxy)="data">
                                <div>
                                    <template v-if="data.item.proxy">
                                        {{data.item.proxy.ip}}:{{data.item.proxy.port}} - {{data.item.proxy.username}}
                                    </template>
                                </div>
                              <template v-if="data.item.proxy">
                                <b-badge v-if="data.item.proxy.is_working === 0" variant="danger">
                                    Не работает
                                </b-badge>
                                <b-badge v-else variant="success">
                                    Работает
                                </b-badge>
                              </template>
                            </template>

                            <template v-slot:cell(actions)="data">
                                <div class="btn-group">
                                    <router-link class="btn btn-primary btn-xs" tag="a"
                                                 :to="`/kuna-accounts/show/${data.item.number}`">
                                        <b-icon icon="eye"></b-icon>
                                    </router-link>
                                    <router-link class="btn btn-warning btn-xs" tag="a"
                                                 :to="`/kuna-accounts/edit/${data.item.number}`">
                                        <b-icon icon="pencil"></b-icon>
                                    </router-link>
                                    <b-button
                                            class="btn"
                                            variant="danger"
                                            size="xs"
                                            @click="onDelete(data.item.number)"
                                    >
                                        <b-icon icon="trash"></b-icon>
                                    </b-button>
                                </div>
                            </template>
                        </b-table>
                    </div>
                </b-overlay>
            </div>
        </div>
    </div>
</template>

<script>
    import {BIcon, BIconTrash, BIconPencil, BIconEye} from 'bootstrap-vue';
    import { getHumanDate } from "@state/helpers";

    export default {
        props: {
            data: {
                type: Array,
            },
            loading: {
                type: Boolean,
            }
        },
        components: {BIcon, BIconTrash, BIconPencil, BIconEye},
        watch: {
            filterField: {
                handler() {
                    this.$emit('filter', this.filterField);
                },
                deep: true
            },
        },
        methods: {
            onDelete(id){
                this.$emit('on-delete', id);
            },
        },
        data() {
            return {
                getHumanDate,
                sortBy: "number",
                sortDesc: true,
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                        sortable: true
                    },
                    {
                        key: 'name',
                        label: 'Название',
                        sortable: true
                    },
                    {
                        key: 'created_at',
                        label: 'Создан',
                        sortable: true
                    },
                    {
                        key: 'proxy',
                        label: 'Прокси',
                        sortable: true
                    },
                    {
                        key: 'active',
                        label: 'Статус',
                        sortable: true
                    },
                    {
                        key: 'comment',
                        label: 'Комментарий',
                        sortable: true
                    },
                    {
                        key: 'actions',
                        label: 'Действия',
                        sortable: true
                    },
                ],
                filterField: [
                    {
                        name: "name",
                        value: null,
                    },
                    {
                        name: "active",
                        value: null,
                    },
                    {
                        name: "comment",
                        value: null,
                    },
                ]
            };
        }
    };
</script>

<style scoped>

</style>