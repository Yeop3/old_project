<template>
  <b-card
      bg-variant="light"
      text-variant="black"
      border-variant="primary"
  >
    <b-card-text>
      <router-link class="" tag="a"
                   :to="`/locations/edit/${node.number}`">
        <b-icon-map/>
        {{ node.name }} ->
      </router-link>
      <div class="text-muted">
          <span v-if="node.is_branch && !isChild">
              ~ Филиал
          </span>
        <span v-else-if="!node.is_branch && !isChild">
           ~ Входящая локация
        </span>
        <span v-else-if="isChild && node.children.length">
           ~ Промежуточная локация
        </span>
        <span v-else-if="isChild && !node.children.length">
           ~ Район (конечная локация)
        </span>
      </div>
      <div class="text-muted">
        <b-link
            v-for="driver in node.drivers"
            :key="driver.number"
            :to="`/drivers/edit/${driver.number}`"
        >
          <b-badge variant="info" class="mr-1">
            {{driver.name}}
          </b-badge>
        </b-link>
      </div>

    </b-card-text>
    <b-card-text
        class="small text-muted"
        v-if="!node.children.length"
    >
      {{ node.products.filter(value => value.status === 1).length }} товаров продается
    </b-card-text>
    <b-card-text
        class="small text-muted"
    >
      {{ node.commission_value }}
      {{commission_types.find((value) => node.commission_type=== value.value).text }} комиссия
    </b-card-text>
    <b-card-body deck v-if="node.children && node.children.length">

      <NodeTree
          v-for="child in node.children"
          :node="child"
          :is-child="true"
          :commission_types="commission_types"
      />
    </b-card-body>
  </b-card>
</template>

<script>
import {BIcon, BIconMap} from 'bootstrap-vue';

export default {
  name: "NodeTree",
  props: {
    node: {
      type: Object
    },
    commission_types: Array,
    isChild: {
      type: Boolean,
      default: false
    }
  },
  components: {
    BIcon,
    BIconMap,
  },
  data() {
    return {};
  },
  mounted() {
  },
  methods: {
    deleteLocation() {
      this.$emit('delete-locations', id);
    },
  },
};
</script>

<style scoped>

</style>