<script>
import Layout from '@layouts/auth'
import { authMethods } from '@state/helpers'
import appConfig from '@src/app.config'
export default {
  page: {
    title: 'Log in',
    meta: [{ name: 'description', content: `Log in to ${appConfig.title}` }],
  },
  components: { Layout },
  data() {
    return {
      email: '',
      password: '',
      authError: null,
      tryingToLogIn: false,
      isAuthError: false,
    }
  },
  computed: {
    placeholders() {
      return process.env.NODE_ENV === 'production'
        ? {}
        : {
            email: 'Use "admin" to log in with the mock API',
            password: 'Use "password" to log in with the mock API',
          }
    },
  },
  methods: {
    ...authMethods,
    // Try to log the user in with the username
    // and password they provided.
    tryToLogIn() {
      this.tryingToLogIn = true
      // Reset the authError if it existed.
      this.authError = null
      return this.logIn({
        email: this.email,
        password: this.password,
      })
        .then((token) => {
          this.tryingToLogIn = false
          this.isAuthError = false

          if (this.$route.query.redirectFrom === '/logout') {
            this.$route.query.redirectFrom  = '/';
          }

          // Redirect to the originally requested page, or to the home page
          this.$router.push(this.$route.query.redirectFrom || { name: 'home' })
        })
        .catch((error) => {
          this.tryingToLogIn = false
          this.authError = error.response? error.response.data.message: ''
          this.isAuthError = true
        })
    },
  },
}
</script>

<template>
  <Layout>
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card">
          <div class="card-body p-4">
            <div class="text-center w-75 m-auto">
              <a href="/">
                <span
                  ><img src="@assets/images/logo-dark.png" alt="" height="22"
                /></span>
              </a>
              <p class="text-muted mb-4 mt-3"
                >Enter your email address and password to access admin panel.</p
              >
            </div>

            <b-alert v-model="isAuthError" variant="danger" dismissible class="text-center">
              {{authError}}
            </b-alert>

            <b-form @submit.prevent="tryToLogIn">
              <b-form-group
                id="input-group-1"
                label="Email"
                label-for="input-1"
              >
                <b-form-input
                  id="input-1"
                  v-model="email"
                  type="text"
                  required
                  placeholder="Enter email"
                ></b-form-input>
              </b-form-group>

              <b-form-group
                id="input-group-2"
                label="Password"
                label-for="input-2"
              >
                <b-form-input
                  id="input-2"
                  v-model="password"
                  type="password"
                  required
                  placeholder="Enter password"
                ></b-form-input>
              </b-form-group>

              <b-form-group id="button-group" class="mt-4">
                <b-button type="submit" variant="primary" class="btn-block"
                  >Log in</b-button
                >
              </b-form-group>
            </b-form>
          </div>
          <!-- end card-body -->
        </div>
        <!-- end card -->

        <div class="row mt-3">
          <div class="col-12 text-center">
            <p>
              <router-link tag="a" to="/forget-password" class="ml-1">Forgot your password?</router-link>
              </p
            >
            <p class="text-muted"
              >Don't have an account?
              <router-link tag="a" to="/register" class="text-primary font-weight-medium ml-1">Sign Up</router-link>
              </p
            >
          </div>
          <!-- end col -->
        </div>
        <!-- end row -->
      </div>
      <!-- end col -->
    </div>
    <!-- end row -->
  </Layout>
</template>

<style lang="scss" module></style>
