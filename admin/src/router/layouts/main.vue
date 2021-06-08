<script>
import NavBar from '@components/nav-bar'
import SideBar from '@components/side-bar'
import RightBar from '@components/right-bar'

export default {
  components: { NavBar, SideBar, RightBar },
  data() {
    return {
      isMenuCondensed: false,
      user: this.$store? this.$store.state.auth.currentUser: {} || {},
      seller: {},
    }
  },
  methods: {
    toggleMenu() {
      this.isMenuCondensed = !this.isMenuCondensed
      document.body.classList.toggle('sidebar-enable')
      document.body.classList.toggle('enlarged')
    },
    toggleRightSidebar() {
      document.body.classList.toggle('right-bar-enabled')
    },
  },
  mounted() {
    console.log(this.user);
  }
}
</script>

<template>
  <div id="wrapper">
    <NavBar :user="user" :seller="seller"/>
    <SideBar :is-condensed="isMenuCondensed" />
    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

    <div class="content-page">
      <div class="content">
        <!-- Start Content-->
        <div class="container-fluid"> <slot /> </div
      ></div>
    </div>
    <RightBar />
  </div>
</template>
