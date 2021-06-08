<script>
import {authComputed, operatorsComputed, shiftsComputed, orderComputed, orderMethods} from '@state/helpers';
import VuePerfectScrollbar from 'vue-perfect-scrollbar';

export default {
  components: {VuePerfectScrollbar},
  props: {
    user: {
      type: Object,
      required: false,
      default: () => ({})
    },
  },
  data() {
    return {
      operators_select: [],
      shift: {},
      orderCalc: {},
    };
  },
  computed: {
    ...authComputed,
    ...orderComputed,
  },
  async mounted() {
    if (this.currentUser){
      // this.operators_select = await this.$store.dispatch('operator/getSelect');
      this.shift = await this.$store.dispatch('shifts/getCurrent');
      this.orderCalc = await this.$store.dispatch('order/getCalc');
    }
    this.getCrypto();
  },
  methods: {
  	...orderMethods,
    toggleMenu() {
      this.$parent.toggleMenu();
    },
    toggleRightSidebar() {
      this.$parent.toggleRightSidebar();
    },
    async changeShift(operator) {
      try {
        const value = await this.$bvModal.msgBoxConfirm(`Вы уверены, что хотите начать новую смену с оператором "${operator.name}"?`, {
          title: 'Пожалуйста подтвердите',
          size: 'sm',
          buttonSize: 'sm',
          okVariant: 'danger',
          okTitle: 'Да',
          cancelTitle: 'Нет',
          footerClass: 'p-2',
          hideHeaderClose: false,
          centered: true
        });
        if (value) {
          try {
            await this.$store.dispatch('shifts/start_new', operator.number);
            this.shift = await this.$store.dispatch('shifts/getCurrent');

          } catch (e) {

            this.$bvToast.toast(e.response.data.message, {
              title: 'Errors',
              variant: 'danger',
              autoHideDelay: 5000,
            });
          }
        }
      } catch (e) {
      }

    }
  },
};
</script>

<template>
  <!-- Topbar Start -->
  <div v-if="currentUser" class="navbar-custom adaptive-top-navbar">

    <!-- LOGO -->
    <div class="logo-box">
      <a href="/" class="logo text-center">
        <span class="logo-lg">
          <img src="@assets/images/logo-light.png" alt="" height="20"/>
        </span>
        <span class="logo-sm">
          <!-- <span class="logo-sm-text-dark">X</span> -->
          <img src="@assets/images/logo-sm.png" alt="" height="24"/>
        </span>
      </a>
    </div>

    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
      <li>
        <button
            class="button-menu-mobile waves-effect waves-light"
            @click="toggleMenu"
        >
          <i class="fe-menu"></i>
        </button>
      </li>
    </ul>



    <ul class="list-unstyled topnav-menu mb-0 adaptive-topnav-menu-right">

      <b-nav-item-dropdown class="d-lg-block adaptive-crypto-info" right v-if="crypto && crypto['BTC_UAH']">
        <template slot="button-content" class="crypto-title">
          <!--          Текущая смена-->
          <i class="crypto-icon remixicon-bit-coin-line"></i>
          Курс криптовалюты
        </template>

        <b-dropdown-item>
          <b-table-simple class="crypto-table" small stacked>
            <b-thead>
              <b-tr>
                <b-th>
                  1 BTC
                </b-th>
                <b-th>
                  1 BCH
                </b-th>
                <b-th>
                  1 LTC
                </b-th>
                <b-th>
                  1 ETH
                </b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <b-tr>
                <b-td stacked-heading="1 BTC">{{ crypto['BTC_UAH'].sell_price }} грн</b-td>
                <b-td stacked-heading="1 BCH">{{ crypto['BCH_UAH'].sell_price }} грн</b-td>
                <b-td stacked-heading="1 LTC">{{ crypto['LTC_UAH'].sell_price }} грн</b-td>
                <b-td stacked-heading="1 ETH">{{ crypto['ETH_UAH'].sell_price }} грн</b-td>
              </b-tr>
            </b-tbody>
          </b-table-simple>
        </b-dropdown-item>

      </b-nav-item-dropdown>

      <b-nav-item-dropdown class="d-lg-block adaptive-shift-info" right v-if="shift">
        <template slot="button-content">
<!--          Текущая смена-->
          Показатели
          <i class="mdi mdi-chevron-down"></i>
        </template>

<!--        <b-dropdown-item>-->
<!--          Смена:-->
<!--          <span class="text-shortage" v-if="shift.operator">{{ shift.operator.name }}</span>-->
<!--          <span class="text-shortage" v-else>Удален</span>-->
<!--          {{ moment(shift.started_at).locale('ru').startOf('hour').fromNow() }}-->
<!--        </b-dropdown-item>-->

        <b-dropdown-item>
          <span class="text-incoming">Приход: {{ orderCalc.coming }} ₴</span>
        </b-dropdown-item>

        <b-dropdown-item>
          <span class="text mr-2">Расход: 0,00 ₴</span>
        </b-dropdown-item>

        <b-dropdown-item>
          <span class="text-shortage">Недостача: {{ orderCalc.consumption }} ₴</span>
        </b-dropdown-item>
      </b-nav-item-dropdown>

      <b-nav-item class="shift-info" href="#" disabled v-if="shift">
<!--        Смена:-->
<!--        <span class="text-shortage" v-if="shift.operator">{{ shift.operator.name }}</span>-->
<!--        <span class="text-shortage" v-else>Удален</span>-->
<!--        {{ moment(shift.started_at).locale('ru').startOf('hour').fromNow() }}-->
        <span class="text-incoming mr-2 ml-2">Приход: {{ orderCalc.coming }} ₴</span>
        <span class="text mr-2">Расход: 0,00 ₴</span>
        <span class="text-shortage">Недостача: {{ orderCalc.consumption }} ₴</span>
      </b-nav-item>

      <!--            <b-nav-item-dropdown class="d-lg-block" right>-->
      <!--                <template slot="button-content">-->
      <!--                    Настройки-->
      <!--                    <i class="mdi mdi-chevron-down"></i>-->
      <!--                </template>-->

      <!--                <b-dropdown-item to="/settings/reservation">-->
      <!--                    <i class="mdi mdi-settings mr-1"></i>-->
      <!--                    <span>-->
      <!--                        Настройки резервирования-->
      <!--                    </span>-->
      <!--                </b-dropdown-item>-->

      <!--                <b-dropdown-item to="/settings/crypto-settings">-->
      <!--                    <i class="mdi mdi-settings mr-1"></i>-->
      <!--                    <span>-->
      <!--                        Крипто-Настройки-->
      <!--                    </span>-->
      <!--                </b-dropdown-item>-->
      <!--            </b-nav-item-dropdown>-->

      <!--                <b-dropdown-item to="/settings/common">-->
      <!--                    <i class="mdi mdi-settings mr-1"></i>-->
      <!--                    <span>-->
      <!--                        Основные настройки-->
      <!--                    </span>-->
      <!--                </b-dropdown-item>-->


<!--      <b-nav-item-dropdown class="d-lg-block" right v-if="currentUser.role !== 3">-->
<!--        <template slot="button-content">-->
<!--          Новая смена-->
<!--          <i class="mdi mdi-chevron-down"></i>-->
<!--        </template>-->
<!--        <b-dropdown-item disabled href="#">-->
<!--          <span>Выберите оператора</span>-->
<!--        </b-dropdown-item>-->
<!--        <b-dropdown-item @click="changeShift(operator)" v-for="operator in operators_select"-->
<!--                         v-bind:key="operator.number">-->
<!--          <i class="fe-user mr-1"></i>-->
<!--          <span>{{ operator.name }}</span>-->
<!--        </b-dropdown-item>-->
<!--      </b-nav-item-dropdown>-->

      <b-nav-item-dropdown right class="profile-dropdown">
        <template slot="button-content">
          <div class="nav-user mr-0 waves-effect waves-light">
            <span class="pro-user-name ml-1">
              {{ currentUser ? currentUser.name : '' }}
<!--              ({{ currentUser.role === 2 ? 'Админ' : 'Оператор' }})-->
              <i class="mdi mdi-chevron-down"></i>
            </span>
          </div>
        </template>

        <b-dropdown-item v-if="currentUser.role !== 3" to="/operators">
          <i class="remixicon-account-circle-line"></i>
          <span>Операторы</span>
        </b-dropdown-item>

        <b-dropdown-item href="/logout">
          <i class="remixicon-logout-box-line"></i>
          <span>Выйти</span>
        </b-dropdown-item>
      </b-nav-item-dropdown>
    </ul>
  </div>
  <!-- end Topbar -->
</template>

<style lang="scss">
.text-incoming {
  color: #4aff4d;
}

.text-shortage {
  color: #ffdd36;
}

.notification-items {
  height: 220px;
}

.ps > .ps__scrollbar-y-rail {
  width: 8px !important;
  background-color: transparent !important;
}

.ps > .ps__scrollbar-y-rail > .ps__scrollbar-y,
.ps.ps--in-scrolling.ps--y > .ps__scrollbar-y-rail > .ps__scrollbar-y,
.ps > .ps__scrollbar-y-rail:active > .ps__scrollbar-y,
.ps > .ps__scrollbar-y-rail:hover > .ps__scrollbar-y {
  width: 6px !important;
}

.adaptive-top-navbar {
  display: flex;
  flex-direction: row;
  padding: 0;
}

.adaptive-top-navbar .topnav-menu-left {
  flex-grow: 1;
}

.adaptive-shift-info {
  display: none !important;
}

@media (max-width: 1210px) {

  .adaptive-shift-info {
    display: block !important;
  }

  .shift-info {
    display: none;
  }

}

@media (max-width: 622px) {

  .adaptive-top-navbar .adaptive-topnav-menu-right > li {
    height: 35px;
  }

  .adaptive-top-navbar .adaptive-topnav-menu-right > li > a {
    line-height: 35px !important;
  }

  .adaptive-top-navbar .adaptive-topnav-menu-right > li:last-child {
    width: 130px;
  }

  .adaptive-top-navbar .adaptive-topnav-menu-right > li:last-child > a {
    padding: 0;
  }

  .adaptive-top-navbar .adaptive-topnav-menu-right > li:last-child > a > div > span > i {
    margin-right: 2px;
  }

  .adaptive-topnav-menu-right {
    display: flex;
    flex-flow: row wrap;
    justify-content: flex-end;
  }

}
.crypto-table {
  width: 220px !important;
}

.adaptive-crypto-info > .nav-link {
  cursor: pointer;
  display: flex !important;
  flex-direction: row;
  align-items: center;
  justify-content: center;
}


.crypto-text {
  font-size: 14px !important;
}
</style>
