<template>
    <b-form-group
            :id="name"
            :label="label"
            :class="formGroupClasses"
    >
        <b-form-textarea
                :name="name"
                :value="value"
                @input="$emit('input', $event)"
                :placeholder="placeholder"
                :state="errors[name] === undefined ? null : false"
                :aria-describedby="name + '-feedback'"
                :class="classes"
                :rows="rows"
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
            placeholder: {
                type: String,
            },
            value: {
                type: String,
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
            rows: {
                type: Number,
                default: 4,
            },
            description: {
                type: String,
                default: ''
            },
        },
        data() {
            return {};
        },
    };
</script>