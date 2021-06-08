<template>
    <div>
        <h4 class="header-title">
            <b-button
                    variant="warning"
                    @click="clearTrash"
                    v-if="qiwiManualsDeleted.data.length"
            >
                <b-icon-trash/>
                Очистить корзину ({{qiwiManualsDeleted.data.length}})
            </b-button>
        </h4>
        <div class="table-responsive">
            <b-table
                    id="drivers-table"
                    :items="qiwiManualsDeleted.data"
                    :fields="fields"
                    small
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    sortDirection="desc"
            >
                <template v-slot:cell(orders)="data">
                    {{data.item.orders.length + data.item.orders_awaiting_payment.length +
                    data.item.orders_partially_paid.length}}
                </template>
                <template v-slot:cell(created_at)="data">
                    {{new Date(data.item.created_at).toLocaleString("ru-RU", {year:
                    'numeric',
                    month: 'long',
                    day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                </template>
                <template v-slot:cell(deleted_at)="data">
                    {{new Date(data.item.deleted_at).toLocaleString("ru-RU", {year:
                    'numeric',
                    month: 'long',
                    day: 'numeric', hour:'numeric', minute: 'numeric'})}}
                </template>

                <template v-slot:cell(actions)="data">
                    <div class="btn-group">
                        <b-button class="btn" variant="outline-success" size="sm"
                                  @click="restoreQiwi(data.item.number)"
                        >
                            <b-icon-hand-thumbs-up></b-icon-hand-thumbs-up> Восстановить
                        </b-button>
                        <b-button class="btn" variant="outline-danger" size="sm"
                                  @click="deleteById(data.item.number)"
                        >
                            <b-icon-trash></b-icon-trash>
                        </b-button>
                    </div>
                </template>
            </b-table>
            <b-pagination
                    v-model="currentPage"
                    :total-rows="totalRows"
                    :per-page="perPage"
                    aria-controls="drivers-table"
                    align="left"
                    @change="paginate"
                    v-if="qiwiManualsDeleted.last_page > 1"
            ></b-pagination>
        </div> <!-- end table-responsive-->
    </div>
</template>

<script>
    import {BIcon, BIconTrash, BIconEye, BIconHandThumbsUp} from 'bootstrap-vue';

    export default {
        name: "table-wallets-qiwi-manual-info",
        components: {
            BIcon,
            BIconTrash,
            BIconEye,
            BIconHandThumbsUp
        },
        computed: {},
        props: {
            qiwiManualsDeleted: {
                type: Object,
                default: () => ({}),
            }
        },

        data() {
            return {
                page: 1,
                sortBy: "deleted_at",
                sortDesc: true,
                fields: [
                    {
                        key: 'number',
                        label: 'ID',
                        sortable: true
                    },
                    {
                        key: 'phone',
                        label: 'Телефон',
                        sortable: true
                    },
                    {
                        key: 'created_at',
                        label: 'Добавлен',
                        sortable: true
                    },
                    {
                        key: 'deleted_at',
                        label: 'Удален',
                        sortable: true
                    },
                    {
                        key: 'orders',
                        label: 'Заказы',
                    },
                    {
                        key: 'note',
                        label: 'Заметка',
                    },
                    {
                        key: 'actions',
                        label: 'Действие',
                        sortable: true
                    },

                ],
                perPage: 1,
                currentPage: 1,
                totalRows: 20,
            };
        },
        mounted() {
            this.currentPage = this.qiwiManualsDeleted.current_page;
            this.perPage = this.qiwiManualsDeleted.per_page;
            this.totalRows = this.qiwiManualsDeleted.total;
        },
        methods: {
            paginate(pageNum) {
                this.$emit('paginate', pageNum);
            },
            async restoreQiwi(id) {
                const value = await this.$bvModal.msgBoxConfirm('Вы действительно хотите восстановить этот кошелек?',
                    {
                        title: 'Пожалуйста подтвердите',
                        size: 'sm',
                        buttonSize: 'sm',
                        okVariant: 'success',
                        okTitle: 'Восстановить',
                        cancelTitle: 'Отмена',
                        footerClass: 'p-2',
                        hideHeaderClose: false,
                        centered: true
                    });
                if (value)
                    this.$emit('restore-qiwi', id);
            },
            async deleteById(id) {
                const value = await this.$bvModal.msgBoxConfirm('Вы действительно хотите безвозвратно удалить этот кошелек?',
                    {
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
                if (value)
                    this.$emit('delete-by-id', id);
            },
            async clearTrash() {
                const value = await this.$bvModal.msgBoxConfirm('Вы действительно хотите безвозвратно удалить' +
                    ' все кошельки, находящиеся в корзине?',
                    {
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
                if (value)
                    this.$emit('clear-trash');
            },
        }
    };
</script>

<style scoped>

</style>