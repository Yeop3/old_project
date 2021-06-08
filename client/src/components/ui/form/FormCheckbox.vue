<template>
    <b-form-group
            :id="name"
            :class="formGroupClasses"
    >
        <b-form-checkbox
                id="checkbox-1"
                :name="name"
                :checked="value"
                :value="checkedValue"
                :unchecked-value="uncheckedValue"
                :state="errors[name] === undefined ? null : false"
                :class="classes"
                @input="$emit('input', $event)"
        >
            <slot/>
        </b-form-checkbox>

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
            // checked: {
            //     type: [Boolean, String, Number]
            // },
            uncheckedValue: {
                type: [Boolean, String, Number]
            },
            label: {
                type: String,
            },
            value: {
                type: [String, Number, Boolean],
            },
            checkedValue: {
                type: [String, Number, Boolean],
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
            description: {
                type: String,
                default: ''
            },
        },
        data() {
            return {};
        },
        created() {
            // console.log(this.value);
            // console.log(this.uncheckedValue);
        }
    };
</script>
