<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="card-box">
        <b-overlay :show="spiner" rounded="sm" no-center>
          <template v-slot:overlay>
            <div class="centered">
              <b-spinner variant="secondary"></b-spinner>
            </div>
          </template>
          <div class="table-responsive">
            <b-table
                id="drivers-table"
                :items="orders.data"
                :fields="fields"
                small
                no-local-sorting
                @sort-changed="sorting"
                :sortDirection="filterParams.sortDirection || 'asc'"
                :sort-by="filterParams.sortField || null"
                :sortDesc="filterParams.sortDirection !== 'asc'"
            >
              <template v-slot:top-row="columns">
                <td :key="`filter_${key}`" v-for="(filter, key) in filterField">
                  <template v-if="filter.type === 'select'">
                    <b-select :options="filter.options"
                              @change="onFilterEnter"
                              v-model="filter.value"
                              size="sm"
                    >
                    </b-select>
                  </template>
                  <template v-else-if="filter.type === 'date'">
                    <b-input-group size="sm">
                      <b-form-input
                          @change="onFilterEnter"
                          type="date"
                          v-model="filter.value"
                      />
                      <b-button-close
                          v-b-tooltip.hover.right
                          title="Сбросить дату"
                          class="ml-1"
                          @click="resetDate"
                      />
                    </b-input-group>
                  </template>
                  <template v-else-if="filter.type === 'skip'">

                  </template>
                  <template v-else>
                    <b-form-input
                        :name="filter.name"
                        :type="filter.type"
                        v-model="filter.value"
                        @keyup.enter="onFilterEnter"
                        size="sm"
                    ></b-form-input>
                  </template>
                </td>
                <td>
                  <b-button size="sm" @click="onClickResetFiltersButton">Сбросить</b-button>
                </td>
              </template>
              <template v-slot:cell(created_at)="data">
                {{
                  new Date(data.item.created_at).toLocaleString("ru-RU", {
                    year: 'numeric', month:
                        'numeric',
                    day: 'numeric'
                  })
                }}
                <div class="text-muted small">
                  {{
                    new Date(data.item.created_at).toLocaleString("ru-RU", {
                      hour: 'numeric', minute:
                          'numeric'
                    })
                  }}
                </div>
                <div class="text-muted small">
                  {{ data.item.created_at_diff }}
                </div>
              </template>
              <template v-slot:cell(client)="data">
                <div v-if="data.item.client">
                  <router-link
                      :to="`clients/${data.item.client.number}`"
                      tag="a"
                  >
                    {{ data.item.client.name }}
                  </router-link>
                  <div>
                    <b-button
                        variant="success"
                        size="xs"
                        @click="openMessageModal(data.item.client)"
                    >
                      <b-icon icon="chat"></b-icon>
                      Написать
                    </b-button>
                  </div>
                </div>

                <b-badge
                    v-else
                    variant="warning"
                >
                  Удален
                </b-badge>
              </template>
              <template v-slot:cell(product)="data">
                <router-link
                    v-if="data.item.product && data.item.product.deleted_at === null"
                    tag="a"
                    :to="`/products/show/${data.item.product.number}`"
                >
                  {{ data.item.name }}
                </router-link>

                <b-badge
                    v-else
                    variant="warning"
                >
                  Удален
                </b-badge>
                <div class="nowrap">
                  {{ data.item.price.amount / 100 }}
                  <span class="small text-danger" v-if="data.item.product && data.item.is_for_delivery">
                    x {{ data.item.product.count }} {{ data.item.readable_unit }}
                  </span>
                  <span class="small text-danger" v-if="data.item.discount_value">
                     - {{ data.item.discount_value }}%
                  </span>
                  <span class="small text-danger">
                     + {{ data.item.commission.amount / 100 }}
                  </span>
                  = {{ data.item.total_price.amount / 100 }} грн
                </div>
                <div class="small text-warning" v-if="data.item.discount">
                  Скидка:
                  <router-link :to="`discounts/show/${data.item.discount.number}`">
                    {{ data.item.discount.name }}
                  </router-link>
                </div>
                <div class="small text-warning">
                  Комиссия: {{ data.item.commission.amount / 100 }} грн
                </div>
                <div class="small">
                  <router-link
                      v-if="data.item.product && data.item.product.location"
                      tag="a"
                      :to="`locations/show/${data.item.product.location.number}`"
                      class="text-success"
                  >
                    {{ data.item.product.location.name_chain }}
                  </router-link>

                  <b-badge
                      v-else
                      variant="warning"
                  >
                    Локация удалена
                  </b-badge>
                </div>
              </template>

              <template v-slot:cell(account)="data">
                <template v-if="data.item.wallet_type === 'wallet_qiwi_manual'">
                  <div class="small text-center">

                    <div>QIWI</div>
                    <div class="text-muted">
                      ручной
                    </div>
                    <div>
                      <router-link
                          v-if="data.item.wallet"
                          :to="`wallets/qiwi_manual?phone=${data.item.wallet.number}`">
                        {{ data.item.wallet.phone }}
                      </router-link>
                      <b-badge
                          v-else
                          variant="warning"
                      >
                        Удален
                      </b-badge>
                    </div>
                    <div class="text-center">
                      Оплаты
                    </div>
                    <div v-for="manualPayment in data.item.transactions" :key="manualPayment.number">
                      {{ `${manualPayment.amount.amount / 100} ${manualPayment.amount.currency}` }}
                    </div>

                  </div>

                </template>

                <template v-if="data.item.wallet_type === 'wallet_crypto'">
                  <div class="text-center">
                    <div>
                      {{ data.item.wallet.currency.toUpperCase() }}
                    </div>
                    <template v-if="data.item.wallet">
                      <router-link
                          :to="`/crypto-wallet/show/${data.item.wallet.number}`">
                        {{ data.item.wallet.name }}
                      </router-link>

                      <b-link
                          v-b-tooltip.hover="copyText + ' кошелек'"
                          v-clipboard:copy="data.item.wallet.address"
                          v-clipboard:success="onCopyText"
                      >
                        <b-icon-clipboard/>
                      </b-link>
                    </template>
                    <b-badge
                        v-else
                        variant="warning"
                    >
                      Удален
                    </b-badge>
                  </div>
                  <div class="small" style="max-width:150px">
                    <div class="text-center">
                      Оплаты
                    </div>
                    <div v-for="transaction in data.item.transactions" :key="transaction.number">
                      <b-row align-h="center">
                        <b-col :cols="transaction.address ? '5' : '12'" class="pr-0">
                          {{ `${transaction.amount.amount / 100} ${transaction.amount.currency}` }}
                        </b-col>
                        <template v-if="transaction.address">
                          <b-col cols="6" class="text-truncate">
                            <router-link
                                :to="`crypto-transaction/show/${transaction.number}`">
                              {{ transaction.address }}
                            </router-link>
                          </b-col>
                        </template>
                      </b-row>
                    </div>
                  </div>
                </template>

                <template v-if="data.item.wallet_type === 'kuna_account'">
                  <div class="text-center">
                    Kuna-аккаунт
                    <router-link
                        v-if="data.item.wallet"
                        class="d-block"
                        :to="`kuna-accounts/show/${data.item.wallet.number}`">
                      {{ data.item.wallet.name }}
                    </router-link>
                    <b-badge
                        v-else
                        variant="warning"
                    >
                      Удален
                    </b-badge>

                  </div>
                  <div class="small" style="max-width:150px">
                    <div class="text-center">
                      kuna-коды
                    </div>
                    <div v-for="kunaCode in data.item.transactions" :key="kunaCode.number">
                      <b-row align-h="center">
                        <b-col :cols="kunaCode.code ? '5' : '12'" class="pr-0">
                          {{ `${kunaCode.amount.amount / 100} ${kunaCode.amount.currency}` }}
                        </b-col>
                        <template v-if="kunaCode.code">
                          <b-col cols="6" class="text-truncate">
                            <router-link
                                :to="`kuna-codes/show/${kunaCode.number}`">
                              {{ kunaCode.code }}
                            </router-link>
                          </b-col>
                          <b-col cols="1">
                            <b-link
                                v-b-tooltip.hover="copyText"
                                v-clipboard:copy="kunaCode.code"
                                v-clipboard:success="onCopyText"
                            >
                              <b-icon-clipboard/>
                            </b-link>
                          </b-col>
                        </template>
                      </b-row>
                    </div>
                  </div>

                </template>

                <template v-if="data.item.wallet_type === 'global_money'">
                  <div class="small text-center">
                    <div>{{ data.item.payment_method_readable }}</div>

                    <div>
                      <router-link
                          v-if="data.item.wallet"
                          :to="`/global-money/show/${data.item.wallet.number}`">
                        {{ data.item.wallet.name }}
                      </router-link>
                      <b-badge
                          v-else
                          variant="warning"
                      >
                        Удален
                      </b-badge>
                    </div>
                    <div class="text-center">
                      Оплаты
                    </div>
                    <div v-for="manualPayment in data.item.transactions" :key="manualPayment.number">
                      <router-link
                          :to="`global-money/transaction/show/${manualPayment.number}`"
                      >
                        {{ `${manualPayment.amount.amount / 100} ${manualPayment.amount.currency}` }}
                      </router-link>
                    </div>

                  </div>
                </template>

                <template v-if="data.item.wallet_type === 'easy_pay'">
                  <div class="small text-center">
                    <div>{{ data.item.payment_method_readable }}</div>

                    <div>
                      <router-link
                          v-if="data.item.wallet"
                          :to="`/easy-pay/wallet/show/${data.item.wallet.number}`">
                        {{ data.item.wallet.name }}
                      </router-link>
                      <b-badge
                          v-else
                          variant="warning"
                      >
                        Удален
                      </b-badge>
                    </div>
                    <div class="text-center">
                      Оплаты
                    </div>
                    <div v-for="manualPayment in data.item.transactions" :key="manualPayment.number">
                      <router-link
                          :to="`easy-pay/transaction/show/${manualPayment.number}`"
                      >
                        {{ `${manualPayment.amount.amount / 100} ${manualPayment.amount.currency}` }}
                      </router-link>
                    </div>

                  </div>
                </template>

                <div v-if="data.item.status === 8 && data.item.transactions && data.item.transactions.length"
                     class="text-center text-success">
                  Отдан за {{
                    data.item.transactions.map(t => parseInt(t.amount.amount)).reduce((first, next) => first + next, 0) / 100
                  }} {{ data.item.transactions[0].amount.currency }}
                </div>

              </template>

              <template v-slot:cell(source)="data">
                <template v-if="data.item.source_type === 'bot'">
                  <div class="text-center">Бот</div>
                  <div class="text-center small">
                    <router-link
                        v-if="data.item.source"
                        :to="`bots/card/${data.item.source.number}`">
                      {{ data.item.source.username }}
                    </router-link>

                    <b-badge
                        v-else
                        variant="warning"
                    >
                      Удален
                    </b-badge>
                  </div>
                </template>
              </template>

              <!--              <template v-slot:cell(shift)="data">-->
              <!--                <template v-if="data.item.shift.operator">-->
              <!--                  {{ data.item.shift.operator.name }}-->
              <!--                </template>-->
              <!--                <template v-else>-->
              <!--                  Удален-->
              <!--                </template>-->
              <!--              </template>-->

              <template v-slot:cell(status)="data">
                <div class="text-center">
                  <b-badge :variant="statusesColors[data.item.status]">
                    {{ orderStatus[data.item.status] }}
                  </b-badge>
                  <template v-if="data.item.status === 1 || data.item.status === 2">
                    <div class="mt-2 nowrap" v-if="data.item.wallet_type === 'wallet_qiwi_manual'">
                      <router-link
                          tag="a"
                          class="btn btn-info btn-xs"
                          :to="`/payments/qiwi_manual/create?order=${data.item.number}`"
                      >
                        Ручная qiwi-оплата
                      </router-link>
                    </div>

                    <div
                        v-if="!data.item.is_for_delivery"
                        class="mt-2 nowrap"
                    >
                      <b-button
                          class="btn-xs btn btn-success"
                          @click="openGiveModal(data.item)"
                      >
                        Отдать
                      </b-button>
                    </div>
                    <div class="mt-2 nowrap">
                      <b-button
                          class=" btn btn-danger"
                          @click="cancel(data.item.number)"
                          size="xs"
                      >
                        Отмена
                      </b-button>
                    </div>

                  </template>

                  <template
                      v-if="data.item.delivery_type !== 2 && (data.item.status === 4 || data.item.status === 5 || data.item.status === 6 || data.item.status === 7)"
                  >


                  </template>

                  <template v-if="data.item.delivery_type !== 2 && (data.item.status === 3 || data.item.status === 8)">

                    <!--                    <div class="mt-1 nowrap">-->
                    <!--                      <b-button-->
                    <!--                          class="btn"-->
                    <!--                          variant="warning"-->
                    <!--                          size="xs"-->
                    <!--                          @click="restorPaidOrder(data.item.number)"-->
                    <!--                      >-->
                    <!--                        Восстановить-->
                    <!--                      </b-button>-->
                    <!--                    </div>-->

                  </template>

                  <div>
                    {{
                      new Date(data.item.updated_at).toLocaleString("ru-RU", {
                        year: 'numeric',
                        month:
                            'numeric',
                        day: 'numeric'
                      })
                    }}
                  </div>
                  <div class="text-muted small">
                    {{
                      new Date(data.item.updated_at).toLocaleString("ru-RU", {
                        hour: 'numeric',
                        minute:
                            'numeric'
                      })
                    }}
                  </div>
                  <div class="text-muted small">
                    {{ data.item.updated_at_diff }}
                  </div>
                </div>
              </template>

              <template v-slot:cell(actions)="data">
                <div class="btn-group">
                  <router-link class="btn btn-primary btn-xs" tag="a"
                               :to="`/orders/show/${data.item.number}`">
                    <b-icon icon="eye"></b-icon>
                  </router-link>

                  <b-button
                      class="btn"
                      v-if="!data.item.is_for_delivery && (data.item.status === 4 || data.item.status === 5 || data.item.status === 6 || data.item.status === 7)"
                      variant="warning"
                      size="xs"
                      v-b-tooltip="'Восстановить'"
                      @click="restorCanceledOrder(data.item.number)"
                  >
                    <b-icon icon="arrow-clockwise"></b-icon>
                  </b-button>

                  <b-button
                      v-if="!data.item.is_for_delivery && (data.item.status === 3 || data.item.status === 8)"
                      class="btn"
                      variant="warning"
                      size="xs"
                      v-b-tooltip="'Восстановить'"
                      @click="restorPaidOrder(data.item.number)"
                  >
                    <b-icon icon="arrow-clockwise"></b-icon>
                  </b-button>
                </div>
              </template>
            </b-table>
            <b-pagination
                v-if="orders.last_page > 1"
                v-model="currentPage"
                :total-rows="orders.total"
                :per-page="orders.per_page"
                aria-controls="drivers-table"
                align="left"
                @change="paginate"
            ></b-pagination>
          </div> <!-- end table-responsive-->
        </b-overlay>
      </div> <!-- end card-box -->
    </div> <!-- end col -->
    <!--<b-modal
            id="message"
            ref="message"
            :title="titleMessageModal"
            @ok="handleMessage"
    >
      <form ref="form" @submit.stop.prevent="handleSendMessage">
        <form-textarea
                name="message"
                description="* Введите сообщение."
                v-model="message"
                label="Сообщение, которое хотите отправить."
                :size="'sm'"
        ></form-textarea>
      </form>
    </b-modal>-->
    <ModalMessages
        :title-modal="titleMessageModal"
        @handle-submit="handleSendMessage"
        :item="client"
        :errors="errorsModal"
        v-if="showModal"
        id="message"
    />
    <b-modal
        id="give"
        ref="give"
        :title="titleGiveModal"
        @ok="handleOkGive"
    >
      <template v-if="item">
        <div class="small text-right text-success">
          Оплачено за заказ: {{ (item.paid_amount.amount / 100).toFixed(2) }} грн
        </div>
        <div class="small text-right text-danger">
          Осталось доплатить: {{ item.unpaid_amount.amount / 100 }} грн
        </div>
      </template>
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <form-input
            name="price"
            description="* Введите 0, если клиент вообще ничего за него не заплатил."
            v-model="price"
            label="Сумма, за которую Вы отдаете заказ"
            :size="'sm'"
        ></form-input>
      </form>
    </b-modal>
  </div>
</template>

<script>
import FormInput from "@components/ui/form/FormInput";
import FormTextarea from "@components/ui/form/FormTextarea";
import {BIcon, BIconArrowClockwise, BIconChat, BIconClipboard, BIconEye} from 'bootstrap-vue';
import {mapActions, mapState} from "vuex";
import ModalMessages from '@views/clients/modal-messages'

export default {
  name: "table-orders",
  components: {FormInput, BIcon, BIconEye, BIconClipboard, BIconChat, FormTextarea, ModalMessages, BIconArrowClockwise},
  props: {
    orders: {
      type: Object
    },
    spiner: {
      type: Boolean
    },
    orderStatus: {
      type: [Object],
      default: () => ({})
    },
    filterFields: {
      type: Array,
      default: () => ([])
    },
    errorsModal: {
      type: Object
    },
    filterParams: {
      type: Object
    },
  },
  computed: {
    ...mapState('bots', {
      select_bots: state => state.select_bots,
    }),
    ...mapState('paymentMethod', ['paymentMethods']),
    ...mapState('productTypes', {
      productTypes: state => state.product_types_select
    })
  },
  async created() {
    await this.loadPaymentMethods()
    await this.getSelectProductType()
    await this.getSelect();

    this.updateData()
    // await this.loadBots();
  },
  update() {
    this.updateData()
  },
  data() {
    return {
      copyText: 'Скопировать',
      sortBy: "number",
      titleGiveModalInit: 'Отдать заказ #',
      titleGiveModal: '',
      titleMessageModalInit: 'Отправить сообщение @',
      titleMessageModal: '',
      message: '',
      sortDesc: true,
      price: 0,
      fields: [
        {
          key: 'number',
          label: 'ID',
          class: 'id_column',
          sortable: true
        },
        {
          key: 'created_at',
          label: 'Создан',
          sortable: true

        },
        {
          key: 'client',
          label: 'Клиент',
        },
        {
          key: 'product',
          label: 'Клад',
        },
        {
          key: 'account',
          label: 'Аккаунт',
        },
        {
          key: 'source',
          label: 'Источник',
        },
        // {
        //   key: 'shift',
        //   label: 'Смена',
        // },
        {
          key: 'status',
          label: 'Статус изменен',
        },
        {
          key: 'actions',
          label: 'Просмотр',
        },

      ],
      filterField: [
        {
          name: "number",
          value: null,
          type: "number"
        },
        {
          name: "created_at",
          value: null,
          type: "date"
        },
        {
          name: "client_name",
          value: null,
          type: "text"
        },
        {
          name: "product_type",
          value: null,
          type: "select"
        },
        {
          name: "payment_method",
          value: null,
          type: "select"
        },
        {
          name: "bot_number",
          value: null,
          type: "select"
        },
        {
          name: "order_status",
          value: null,
          type: "select"
        }
      ],
      perPage: 3,
      currentPage: 1,
      totalRows: 54,
      item: null,
      client: null,
      statusesColors: {
        1: 'danger',
        2: 'warning',
        3: 'success',
        4: 'secondary',
        5: 'secondary',
        6: 'secondary',
        7: 'secondary',
        8: 'info',
        9: 'info',
        10: 'secondary',
        11: 'info',
      },
      showModal: false,
      sortProduct: {
        sortField: '',
        sortDirection: '',
      }
    };
  },
  methods: {
    resetDate() {
      const date = this.filterField.find(f => f.name === 'created_at')
      if (date) {
        date.value = null
      }
      this.onFilterEnter()
    },
    updateData() {
      this.currentPage = parseInt(this.filterParams.page) || 1;
      this.filterField = this.filterField.map((value) => {
        switch (value.name) {
          case "order_status":
            value.options = [];
            for (let item in this.orderStatus) {
              if (this.orderStatus.hasOwnProperty(item))
                value.options.push({text: this.orderStatus[item], value: item});
            }
            value.options.unshift({text: "Все", value: null});
            break;
          case "bot_number":
            value.options = this.select_bots;
            break;
          case "payment_method":
            value.options = this.paymentMethods;
            break;
          case "product_type":
            value.options = this.productTypes;
            break;
        }
        const filterValue = this.filterParams[value.name]
        if (filterValue) {
          value.value = filterValue
        }
        return value;
      });
    },
    sorting(header) {
      if (!header.sortBy) return;
      this.sortProduct.sortField = header.sortBy;
      this.sortProduct.sortDirection = header.sortDesc === true ? 'desc' : 'asc';
      this.$emit('sort', this.sortProduct);
    },
    onClickResetFiltersButton() {
      this.clearFilterFields()
      this.setDefaultSorting()
      this.onFilterEnter()
    },
    clearFilterFields() {
      this.filterField.forEach(f => f.value = null)
    },
    setDefaultSorting() {
      this.sortProduct.sortField = 'created_at';
      this.sortProduct.sortDirection = 'desc';
      this.$emit('sort', this.sortProduct);
    },
    paginate(pageNum) {
      this.currentPage = pageNum;
      this.$emit('paginate', pageNum);
    },
    onCopyText(e) {
      this.$bvToast.toast(
          'Текст скопирован',
          {
            title: 'Уведомление',
            variant: 'success',
            autoHideDelay: 3000,
          });
    },
    openGiveModal(item) {
      this.item = item;
      this.titleGiveModal = this.titleGiveModalInit + item.number;
      this.price = item.unpaid_amount.amount / 100;
      this.$bvModal.show('give');
    },
    handleOkGive(bvModalEvt) {
      bvModalEvt.preventDefault();
      this.handleSubmit();
    },
    async handleSubmit() {
      this.$emit('set-give-status-order', {price: this.price, number: this.item.number, status: 8});
      await this.$nextTick();
      this.$bvModal.hide('give');
    },
    openMessageModal(client) {
      this.client = client;
      this.titleMessageModal = this.titleMessageModalInit + (client.username || client.name);
      this.showModal = true;
      this.$nextTick(() => {
        this.$bvModal.show('message')
      })
    },
    handleMessage(bvMessageModalEvt) {
      bvMessageModalEvt.preventDefault();
      this.handleSendMessage();
    },
    async handleSendMessage(field) {
      this.$emit('send-message-to-client', field);
      this.showModal = false;
      await this.$nextTick();
      this.message = '';
      this.$bvModal.hide('message');
    },
    // async transfer(id) {
    //   const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите отдать заказ, но' +
    //       ' пометить его как "Переклад"?',
    //       {
    //         title: 'Пожалуйста подтвердите',
    //         size: 'sm',
    //         buttonSize: 'sm',
    //         okVariant: 'danger',
    //         okTitle: 'Подтвердить',
    //         cancelTitle: 'Отмена',
    //         footerClass: 'p-2',
    //         hideHeaderClose: false,
    //         centered: true
    //       });
    //   if (value)
    //     this.$emit('set-transfer-status-order', {number: id, status: 9});
    // },
    async cancel(id) {
      const value = await this.$bvModal.msgBoxConfirm(
          'Вы уверены, что хотите отменить заказ?',
          {
            title: 'Пожалуйста подтвердите',
            size: 'sm',
            buttonSize: 'sm',
            okVariant: 'danger',
            okTitle: 'Подтвердить',
            cancelTitle: 'Отмена',
            footerClass: 'p-2',
            hideHeaderClose: false,
            centered: true
          });
      if (value)
        this.$emit('set-cancel-status-operator-order', id);
    },
    async onFilterEnter() {
      this.currentPage = 1
      this.$emit('filters', this.filterField);
    },
    async restorCanceledOrder(number) {
      const value = await this.$bvModal.msgBoxConfirm(
          'Вы уверены, что хотите восстановить заказ?',
          {
            title: 'Пожалуйста подтвердите',
            size: 'sm',
            buttonSize: 'sm',
            okVariant: 'warning',
            okTitle: 'Восстановить',
            cancelTitle: 'Отмена',
            footerClass: 'p-2',
            hideHeaderClose: false,
            centered: true
          });
      if (value) {
        this.$emit('restor-canceled-order', number);
      }
    },
    async restorPaidOrder(number) {
      const value = await this.$bvModal.msgBoxConfirm(
          'Вы уверены, что хотите восстановить заказ?',
          {
            title: 'Пожалуйста подтвердите',
            size: 'sm',
            buttonSize: 'sm',
            okVariant: 'warning',
            okTitle: 'Восстановить',
            cancelTitle: 'Отмена',
            footerClass: 'p-2',
            hideHeaderClose: false,
            centered: true
          });
      if (value) {
        this.$emit('restor-paid-order', number);
      }
    },
    ...mapActions('bots', ['getSelect']),
    ...mapActions('paymentMethod', ['loadPaymentMethods']),
    ...mapActions('productTypes', ['getSelectProductType']),
  }
};
</script>

<style>
.id_column {
  width: 135px !important;
}
</style>