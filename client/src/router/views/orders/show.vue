<template>
  <Layout>
    <PageHeader :title="title" :items="items"/>
    <div class="row">
      <div class="col-lg-12">
        <div class="card-box" v-if="!spiner_table">
          <div class="table-responsive">
            <b-table
                id="drivers-table"
                stacked=""
                :items="order"
                :fields="fields"
                small
            >
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
                <template v-if="data.item.client">
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
                </template>

                <b-badge
                    v-else
                    variant="warning"
                >
                  Удален
                </b-badge>

              </template>
              <template v-slot:cell(product)="data">

                <router-link
                    v-if="data.item.product"
                    tag="a"
                    :to="`/products/show/${data.item.product.number}`"
                >
                  {{ data.item.product.product_type.name }}
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
                <div class="small text-warning">
                  Скидка:
                  <router-link v-if="data.item.discount" :to="`discounts/show/${data.item.discount.number}`">
                    {{ data.item.discount.name }}
                  </router-link>
                  <b-badge
                          v-else
                          variant="warning"
                  >
                    Отсутствует
                  </b-badge>
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
                  <div class="small text-left">

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
                    <div class="text-left">
                      Оплаты
                    </div>
                    <div v-for="manualPayment in data.item.transactions" :key="manualPayment.number">
                      {{ `${manualPayment.amount.amount / 100} ${manualPayment.amount.currency}` }}
                    </div>

                  </div>

                </template>

                <template v-if="data.item.wallet_type === 'wallet_crypto'">
                  <div class="text-left">
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
                    <div class="text-left">
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
                  <div class="text-left">
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
                    <div class="text-left">
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
                  <div class="small text-left">
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
                    <div class="text-left">
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
                  <div class="small text-left">
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
                    <div class="text-left">
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
                     class="text-left text-success">
                  Отдан за {{
                    data.item.transactions.map(t => parseInt(t.amount.amount)).reduce((first, next) => first + next, 0) / 100
                  }} {{ data.item.transactions[0].amount.currency }}
                </div>

              </template>

              <template v-slot:cell(source)="data">
                <template v-if="data.item.source_type === 'bot'">
                  <div class="">Бот</div>
                  <div class=" small">
                    <router-link
                        v-if="data.item.source"
                        :to="`bots/card/${data.item.source.number}`">
                      {{ data.item.source.name }}
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
                <div class="text-left">
                  <b-badge :variant="statusesColors[data.item.status]">
                    {{ order_status[data.item.status] }}
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

                    <div class="mt-2 nowrap" v-if="!data.item.is_for_delivery">
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
                      v-if="!data.item.is_for_delivery && (data.item.status === 4 || data.item.status === 5 || data.item.status === 6 || data.item.status === 7)">

                    <div class="mt-1 nowrap">
                      <b-button
                          class="btn"
                          variant="warning"
                          size="xs"
                          @click="restorOrderCanceled(data.item.number)"
                      >
                        Восстановить
                      </b-button>
                    </div>

                  </template>

                  <template v-if="!data.item.is_for_delivery && data.item.status === 3">

                    <div class="mt-1 nowrap">
                      <b-button
                          class="btn"
                          variant="warning"
                          size="xs"
                          @click="restorOrderPaid(data.item.number)"
                      >
                        Восстановить
                      </b-button>
                    </div>

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
            </b-table>
          </div> <!-- end table-responsive-->
        </div> <!-- end card-box -->
        <div class="d-flex justify-content-center mb-3" v-else>
          <b-spinner></b-spinner>
        </div>
      </div> <!-- end col -->
      <b-modal
          id="modal-give"
          ref="modal-give"
          :title="titleModal"
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
      <ModalMessages
          :title-modal="titleMessageModal"
          @handle-submit="handleSendMessage"
          :item="client"
          v-if="showModal"
          id="message"
      />
    </div>
  </Layout>
</template>

<script>
import appConfig from '@src/app.config';
import Layout from '@layouts/main';
import PageHeader from '@components/page-header';
import {
  clientsMethods,
  commissionComputed,
  orderComputed,
  orderMethods,
  productComputed,
  productMethods
} from '@state/helpers';
import FormInput from "@components/ui/form/FormInput";
import ModalMessages from '@views/clients/modal-messages'
import {BIcon, BIconEye, BIconClipboard, BIconChat} from 'bootstrap-vue';


export default {
  name: "show-product",
  components: {PageHeader, Layout, FormInput, ModalMessages, BIcon, BIconEye, BIconClipboard, BIconChat},
  computed: {
    ...orderComputed,
  },
  page: {
    title: 'Просмотр',
    meta: [{name: 'description', content: appConfig.description}],
  },
  async created() {
    await this.getById(this.$route.params.id);
    console.log(this.order);
    await this.getStatus();
  },
  data() {
    return {
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
      fields: [
        {
          key: 'number',
          label: 'ID',
          sortable: true
        },
        {
          key: 'created_at',
          label: 'Создан',

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

      ],
      title: 'Просмотр ',
      items: [
        {
          text: 'Главная',
          to: '/',
        },
        {
          text: 'Все заказы',
          to: '/orders',
        },
        {
          text: 'Просмотр',
          active: true,
        },
      ],
      titleModal: "Отдать заказ #",
      price: 0,
      item: null,
      showModal: false,
      titleMessageModal: '',
      client: null,
      titleMessageModalInit: 'Отправить сообщение @',
    };
  },
  methods: {
    ...orderMethods,
    ...clientsMethods,
    openGiveModal(item) {
      this.item = item;
      this.titleModal += item.number;
      this.price = item.unpaid_amount.amount / 100;
      this.$bvModal.show('modal-give');
    },
    handleOkGive(bvModalEvt) {
      bvModalEvt.preventDefault();
      this.handleSubmit();
    },
    async handleSubmit() {
      try {
        await this.setStatusGive({price: this.price, number: this.item.number, status: 8});
        await this.getById(this.item.number);
      } catch (e) {
        this.$bvToast.toast(e.response.data.message, {
          title: 'Errors',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      }
      await this.$nextTick();
      this.$bvModal.hide('modal-give');
    },
    // async transfer(id) {
    //   const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите отдать заказ, но' +
    //           ' пометить его как "Переклад"?',
    //           {
    //             title: 'Пожалуйста подтвердите',
    //             size: 'sm',
    //             buttonSize: 'sm',
    //             okVariant: 'danger',
    //             okTitle: 'Подтвердить',
    //             cancelTitle: 'Отмена',
    //             footerClass: 'p-2',
    //             hideHeaderClose: false,
    //             centered: true
    //           });
    //   if (value) {
    //     try {
    //       await this.setTransferStatus({number: id, status: 9});
    //       await this.getById(id)
    //     } catch (e) {
    //       this.$bvToast.toast(e.response.data.message, {
    //         title: 'Errors',
    //         variant: 'danger',
    //         autoHideDelay: 5000,
    //       });
    //     }
    //   }
    // },
    async cancel(id) {
      const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите отменить заказ?',
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
      if (value) {
        try {
          await this.setCancelOperatorStatus(id);
          await this.getById(id)
        } catch (e) {
          this.$bvToast.toast(e.response.data.message, {
            title: 'Errors',
            variant: 'danger',
            autoHideDelay: 5000,
          });
        }
      }
    },
    async restorOrderCanceled(number) {
      const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите восстановить заказ?',
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
        await this.restorationCanceledOrder(number).then((res) => {
          this.getById(number);
          this.$bvToast.toast('Товар успешно восстановлен.', {
            title: 'Success',
            variant: 'success',
            autoHideDelay: 5000,
          });
        }).catch((err) => {
          this.$bvToast.toast(err.response.data.message, {
            title: 'Errors',
            variant: 'danger',
            autoHideDelay: 5000,
          });
        });
      }
    },
    async restorOrderPaid(number) {
      const value = await this.$bvModal.msgBoxConfirm('Вы уверены, что хотите восстановить заказ?',
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
        await this.restorationPaidOrder(number).then((res) => {
          this.getById(number);
          this.$bvToast.toast('Товар успешно восстановлен.', {
            title: 'Success',
            variant: 'success',
            autoHideDelay: 5000,
          });
        }).catch((err) => {
          this.$bvToast.toast(err.response.data.message, {
            title: 'Errors',
            variant: 'danger',
            autoHideDelay: 5000,
          });
        });
      }
    },
    async openMessageModal() {
      this.client = this.order[0].client;
      this.titleMessageModal = this.titleMessageModalInit + this.client.username || this.client.name;

      this.showModal = true;
      await this.$nextTick();
      this.$bvModal.show('message')
    },
    async handleSendMessage(field) {
      this.showModal = false;
      await this.sendMessageToClient({...field, number: this.client.number})
      await this.$nextTick();
      this.message = '';
      this.$bvModal.hide('message');
    }
  }
};
</script>

<style lang="scss" module></style>