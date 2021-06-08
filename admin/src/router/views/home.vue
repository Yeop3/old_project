<script>
import appConfig from '@src/app.config'
import Layout from '@layouts/main'
import PageHeader from '@components/page-header'

export default {
  page: {
    title: 'Dashboard',
    meta: [{ name: 'description', content: appConfig.description }],
  },
  components: { Layout, PageHeader },
  data() {
    return {
      title: 'Dashboard',
      items: [
        {
          text: 'Minton',
          href: '/',
        },
        {
          text: 'Dashboard',
          active: true,
        },
      ],
    }
  },
}
</script>

<template>
  <Layout>
    <PageHeader :title="title" :items="items" />

    <div class="row">
      <div class="col-md-12">
        <div class="card-box">
          This is dashboard page
        </div>
      </div>
    </div>
  </Layout>
</template>
