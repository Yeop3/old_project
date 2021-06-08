<template>
    <b-form-group
            :id="id"
            :label="label"
            :class="formGroupClasses"
    >
        <b-form-select
                :type="type"
                :name="name"
                :value="value"
                @input="$emit('input', $event)"
                @change="$emit('change', $event)"
                :placeholder="placeholder"
                :state="errors[name] === undefined ? null : false"
                :aria-describedby="name + '-feedback'"
                :class="classes"
                :options="options"
                :disabled="disabled"
                :size="size"
        />
        <b-form-invalid-feedback :id="name + '-feedback'">
          <span :key="i" v-for="(error, i) in errors[name]">
            {{error}}
          </span>
        </b-form-invalid-feedback>

        <b-form-text>{{description}}</b-form-text>

        <b-form-text>
            <slot name="description"/>
        </b-form-text>
    </b-form-group>
</template>

<script>
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
            type: {
                type: String,
                default: 'text',
            },
            placeholder: {
                type: String,
            },
            value: {
                type: [String, Number, Boolean, Object],
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
            size: {
                type: String,
            }
        },
        data() {
            return {};
        },

    };
</script>
<style lang="scss">
    .invalid-feedback{
        font-weight: 700;
    }
</style>