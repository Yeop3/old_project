<template>
  <b-form-group
      :id="id"
      :label="label"
      :class="formGroupClasses"
  >
    <Multiselect
      :name="name"
      :class="{...classes, 'is-invalid': !!errors[name]}"
      v-model="selected"
      :options="options"
      :close-on-select="closeOnSelect"
      :clear-on-select="clearOnSelect"
      :label="optionLabel"
      :placeholder="placeholderText"
      :trackBy="trackBy"
      searchable
      selectLabel="Выбрать"
      selectedLabel="Выбрано"
      deselectLabel="Удалить"
      @select="onSelect"
      @remove="onRemove"
      :disabled="disabled"
      :multiple="multiple"
    >
      <template v-if="valueIsArray" slot="selection" slot-scope="{ values, search, isOpen }">
          <span
              class="multiselect__single" v-if="values.some(v => !v.value) && !isOpen">
              Выбраны все
          </span>
          <span
              class="multiselect__single" v-else-if="values.length && !isOpen">
              Выбрано: {{ values.length }}
          </span>
      </template>
      <template v-if="valueIsArray" #noResult>
          Ничего не найдено
      </template>
    </Multiselect>
    <b-form-invalid-feedback :id="name + '-feedback'" :state="!!errors[name]">
      <span :key="i" v-for="(error, i) in errors[name]">
        {{ error }}
      </span>
    </b-form-invalid-feedback>

    <b-form-text>{{ description }}</b-form-text>

    <b-form-text>
      <slot name="description"/>
    </b-form-text>
  </b-form-group>
</template>

<script>
import Multiselect from "vue-multiselect";

export default {
  props: {
    description: {
      type: String
    },
    name: {
      type: String,
    },
    id: {
      type: String,
      default: ''
    },
    label: {
      type: String,
    },
    optionLabel: {
      type: String,
      default: 'text'
    },
    placeholder: {
      type: String,
    },
    value: {
      type: [String, Number, Boolean, Object, Array],
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
    classes: {
      type: [Array, Object, String],
      default: () => [],
    },
    formGroupClasses: {
      type: [Array, Object, String],
      default: () => [],
    },
    options: {
      type: Array,
      default: () => [],
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    multiple: {
      type: Boolean,
      default: false,
    },
    closeOnSelect: {
      type: Boolean,
      default: true,
    },
    clearOnSelect: {
      type: Boolean,
      default: true,
    },
    size: {
      type: String,
    },
    trackBy: {
      type: String,
      default: 'value'
    },
  },
  components: {Multiselect},
  data() {
    return {
      selected: this.value
    };
  },
  watch: {
    value: {
      handler() {
        this.selected = this.value
      },
      deep: true
    }
  },
  computed: {
    placeholderText() {
      if (this.placeholder) {
        return this.placeholder
      }
      return this.selected[this.trackBy] || (Array.isArray(this.selected) && this.selected.length) ? '' : 'Не выбрано'
    },
    valueIsArray() {
      return Array.isArray(this.selected)
    }
  },
  methods: {
    async onSelect(value) {
        this.$emit('select', value);
        await this.$nextTick()
        if (!this.valueIsArray) {
          this.onInput()
          return
        }
        if (this.isOptionAll(value[this.trackBy])) {
            this.selected = this.options
            this.onInput()
            return
        }
        this.unsetOptionAll()
        this.onInput()
    },
    async onRemove(value) {
        this.$emit('remove', value);
        await this.$nextTick()
        if (!this.valueIsArray) {
          this.onInput()
          return
        }
        if (this.isOptionAll(value[this.trackBy])) {
            this.selected = []
            this.onInput()
            return
        }
        this.unsetOptionAll()
        this.onInput()
    },
    isOptionAll(value) {
        return value === null
    },
    unsetOptionAll() {
        const index = this.selected.findIndex(b => !b[this.trackBy])
        if (index !== -1) {
            this.selected.splice(index, 1)
        }
    },
    onInput(){
        this.$emit('input', this.selected);
    }
  }
};
</script>
<style lang="scss">
.invalid-feedback {
  font-weight: 700;
}
</style>