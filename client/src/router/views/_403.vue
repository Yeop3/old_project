<script>
import Layout from '@layouts/auth'

export default {
  page: {
    title: '403',
    meta: [{name: 'description', content: '403'}],
  },
  components: {Layout},
  props: {
    resource: {
      type: String,
      default: '',
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

            <div class="text-center">
              <h3 class="mt-4 text-uppercase font-weight-bold">У вас нет доступа</h3>
              <router-link tag="a" to="/" class="btn btn-primary mt-3"><i class="mdi mdi-reply mr-1"></i> Вернуться на
                главную
              </router-link>
              <router-link tag="a" to="/logout" class="btn btn-primary mt-3 ml-2"><i class="fas fa-sign-out-alt"></i> Выйти
              </router-link>
            </div>

          </div> <!-- end card-body -->
        </div>
        <!-- end card -->

      </div> <!-- end col -->
    </div>
  </Layout>
</template>

<style lang="scss" module>
.title {
  text-align: center;
}
</style>
