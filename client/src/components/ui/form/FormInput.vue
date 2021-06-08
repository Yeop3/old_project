<template>
    <b-form-group
            :id="name"
            :label="label"
            :class="formGroupClasses"
    >
        <b-form-input
                :type="type"
                :max="max"
                :min="min"
                :step="step"
                :name="name"
                :value="value"
                @input="$emit('input', $event)"
                @blur="$emit('blur', $event)"
                :placeholder="placeholder"
                :state="errors[name] === undefined ? null : false"
                :aria-describedby="name + '-feedback'"
                :class="classes"
                :readonly="readonly"
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
            name: {
                type: String,
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
                type: [String, Number],
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
            max: {
                type: Number,
            },
            min: {
                type: Number,
            },
            step: {
                type: Number,
                default: 1,
            },
            description: {
                type: String,
                default: ''
            },
            readonly: {
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
