<script>
import {authComputed} from '@state/helpers'
import {BIcon, BIconCreditCard, BIconArrowLeft} from 'bootstrap-vue'
import MetisMenu from 'metismenujs/dist/metismenujs'

export default {
  components: {BIcon, BIconCreditCard, BIconArrowLeft},
  computed: {
    ...authComputed,
  },
  mounted() {
    // eslint-disable-next-line no-unused-vars
    if (!this.currentUser) {
      return;
    }

    var menuRef = new MetisMenu('#side-menu')

    this.menus.find(el => el.name ==='Финансы').display = this.currentUser.role !== 3;
  },
  methods: {
    isActive(path, index) {
      const clearPath = path.split('?')[0].replace(/\/$/, "");
      if (this.$route.fullPath.includes(clearPath)) {
        this.activeItemIndex = index
        return true
      }
      return false
    },
  },
  data() {
    return {
      activeItemIndex: null,
      menus: [
        {
          name: 'Заказы',
          // path: '/orders?order=all',
          path: '/orders',
          faIcon: 'shopping-cart',
          display: true
        },
        {
          name: 'Клиенты',
          path: '/clients',
          faIcon: 'users',
          display: true
        },
        {
          name: 'Товары',
          path: '/product-types',
          faIcon: 'list',
          display: true
        },
        {
          name: 'Клады',
          path: '/products',
          faIcon: 'gift',
          display: true
        },
        {
        	name: 'Ответственные',
          path: '/stokers',
          faIcon: 'tasks',
          display: true,
        },
        {
          name: 'Боты',
          path: '/bots',
          faIcon: 'comment',
          display: true
        },
        {
          name: 'Логика ботов',
          path: '/bot-logics',
          faIcon: 'bolt',
          display: true
        },
        {
          name: 'Локации',
          path: '/locations',
          faIcon: 'map-marked-alt',
          display: true
        },
        {
          name: 'Скидки',
          path: '/discounts',
          faIcon: 'percent',
          display: true
        },
        {
          name: 'Смены',
          path: '/shifts',
          faIcon: 'clock',
          display: true
        },
        {
          name: 'Финансы',
          faIcon: 'dollar-sign',
          children: [
            {
              name: 'Ручные Qiwi-Оплаты',
              faIcon: 'dollar-sign',
              path: '/payments/qiwi_manual/',
            },
            {
              name: 'Кошельки для оплаты',
              faIcon: 'wallet',
              path: '/wallets/qiwi_manual',
            },
            {
              name: 'Крипто-кошельки',
              faIcon: 'wallet',
              path: '/crypto-wallet/',
            },
            {
              name: 'Крипто-транзакции',
              faIcon: 'dollar-sign',
              path: '/crypto-transaction/',
            },
            {
              name: 'Кuna-аккаунты',
              faIcon: 'user-circle',
              path: '/kuna-accounts/',
            },
            {
              name: 'Кuna-коды',
              faIcon: 'qrcode',
              path: '/kuna-codes/',
            },
            {
              name: 'GlobalMoney-кошельки',
              faIcon: 'wallet',
              path: '/global-money/wallet/',
            },
            {
              name: 'GlobalMoney-транзакции',
              faIcon: 'dollar-sign',
              path: '/global-money/transaction/',
            },
            {
              name: 'Easypay-кошельки',
              faIcon: 'wallet',
              path: '/easy-pay/wallet/',
            },
            {
              name: 'Easypay-транзакции',
              faIcon: 'dollar-sign',
              path: '/easy-pay/transaction/',
            },
          ],
          display: true
        },
        {
          name: 'Курьеры',
          path: '/drivers',
          faIcon: 'user',
          display: true
        },
        {
          name: 'Прокси',
          path: '/proxies',
          faIcon: 'server',
          display: true
        },
        {
          name: 'Статистика',
          path: '/statistic',
          faIcon: 'chart-bar',
          display: true
        },
        {
          name: 'Рассылки',
          path: '/dispatchers',
          faIcon: 'paper-plane',
          display: true
        },
        {
          name: 'Excel отчеты',
          faIcon: 'star',
          children: [
            {
              name: 'За текущую смену',
              faIcon: 'download',
              path: '/download-excel-shift/current/',
            },
            {
              name: 'За предыдущую смену',
              faIcon: 'download',
              path: '/download-excel-shift/prev/',
            },
          ],
          display: true
        },
        {
          name: 'Настройки',
          faIcon: 'cogs',
          children: [
            {
              name: 'Резервирование',
              faIcon: 'cog',
              path: '/settings/reservation/',
            },
            {
              name: 'Крипто-настройки',
              faIcon: 'cog',
              path: '/settings/crypto-settings/',
            },
            {
              name: 'Способы оплаты',
              faIcon: 'cog',
              path: '/settings/payment-methods/',
            },
          ],
          display: true
        },
      ],
    }
  },
}
</script>

<template>
  <!--- Sidemenu -->
  <div id="sidebar-menu">
    <ul v-if="currentUser" id="side-menu" class="metismenu">
      <li class="menu-title">Навигация</li>

      <li v-for="(menu, i) in menus" :key="menu.name" v-if="menu.display">
        <template v-if="menu.children && menu.children.length">
          <a
              href="javascript: void(0);"
              :class="{'active': i === activeItemIndex}"
          >
            <i :class="'fa fa-' + menu.faIcon"/>
            <span>{{ menu.name }}</span>
          </a>
          <ul
              class="nav-second-level"
              :class="{'in': i === activeItemIndex}"
              aria-expanded="false"
              v-if="menu.children"
          >
            <li v-for="child in menu.children" :key="child.name">
              <router-link
                  tag="a"
                  :to="child.path"
                  :class="{'active font-weight-bold': isActive(child.path, i)}"
              >
                <i :class="'fa fa-' + child.faIcon"/>
                {{ child.name }}
              </router-link>
            </li>
          </ul>
        </template>
        <a v-else>
          <i :class="'fa fa-' + menu.faIcon"/>
          <span>
                        <router-link
                            tag="a"
                            :to="menu.path"
                            :class="{'active font-weight-bold': isActive(menu.path, i)}"
                        >{{ menu.name }}</router-link>
                    </span>
        </a>
      </li>
    </ul>
    <div class="clearfix"></div>
  </div>
  <!-- End Sidebar -->
</template>

<style lang="scss">
@import '~metismenujs/scss/metismenujs';
</style>
