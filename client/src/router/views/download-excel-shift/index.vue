<template>

</template>

<script>
import {shiftsMethods} from "@state/helpers";

export default {
  name: "index",
  async created() {
    await this.exportExcel();
    this.$router.go(-1)
  },
  methods: {
    ...shiftsMethods,
    async exportExcel() {
      await this.exportExcelByID(this.$route.params.id);
    }
  }
}
</script>

<style scoped>

</style>