<script>
import appConfig from '@src/app.config'

export default {
  page: {
    // All subcomponent titles will be injected into this template.
    titleTemplate(title) {
      title = typeof title === 'function' ? title(this.$store) : title
      return title ? `${title} | ${appConfig.title}` : appConfig.title
    },
  },
}
</script>

<template>
  <div id="app">
    <!--
    Even when routes use the same component, treat them
    as distinct and create the component again.
    -->
    <RouterView :key="$route.fullPath" />
  </div>
</template>

<!-- This should generally be the only global CSS in the app. -->
<style lang="scss">
// Allow element/type selectors, because this is global CSS.
// stylelint-disable selector-max-type, selector-class-pattern

// Design variables and utilities from src/design.
@import '~@design';
</style>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style lang="scss">
.multiselect{
  min-height: 31px !important;
  &.is-invalid .multiselect__tags{
    border-color: #f1556c;
  }
}

.multiselect__select{
  height: 31px !important;
}

.multiselect__tags{
  //padding: 10px 15px;
  min-height: 31px !important;
  padding-top: 0;
}

.multiselect__tag:first-child {
  margin-top: 10px;
}

.multiselect__input{
  margin-top: 4px;
  margin-bottom: 4px;
}

.multiselect__single{
  margin-bottom: 0;
  margin-top: 4px;
  font-weight: 400;
  font-size: 0.875rem;
}

.multiselect__placeholder{
  margin-bottom: 0;
  margin-top: 3px;
}
</style>
