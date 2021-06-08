<script>
import { authComputed } from '@state/helpers'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'

export default {
  components: { VuePerfectScrollbar },
  props: {
    user: {
      type: Object,
      required: false,
      default: () => ({})
    },
  },
  data() {
    return {}
  },
  computed: {
    ...authComputed,
  },
  methods: {
    toggleMenu() {
      this.$parent.toggleMenu()
    },
    toggleRightSidebar() {
      this.$parent.toggleRightSidebar()
    },
  },
}
</script>

<template>
  <!-- Topbar Start -->
  <div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">
        <b-nav-item-dropdown class="profile-dropdown">
          <template slot="button-content">
            <div class="nav-user mr-0 waves-effect waves-light">
              <img
                src="@assets/images/users/avatar-1.jpg"
                alt="user-image"
                class="rounded-circle"
              />
              <span class="pro-user-name ml-1">
                {{user ? user.name : ''}} <i class="mdi mdi-chevron-down"></i>
              </span>
            </div>
          </template>


          <b-dropdown-item href="/logout">
            <i class="remixicon-logout-box-line"></i>
            <span>Logout</span>
          </b-dropdown-item>
        </b-nav-item-dropdown>
    </ul>

    <!-- LOGO -->
    <div class="logo-box">
      <a href="index.html" class="logo text-center">
        <span class="logo-lg">
          <img src="@assets/images/logo-light.png" alt="" height="20" />
          <!-- <span class="logo-lg-text-light">Xeria</span> -->
        </span>
        <span class="logo-sm">
          <!-- <span class="logo-sm-text-dark">X</span> -->
          <img src="@assets/images/logo-sm.png" alt="" height="24" />
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
  </div>
  <!-- end Topbar -->
</template>

<style lang="scss">
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
</style>
