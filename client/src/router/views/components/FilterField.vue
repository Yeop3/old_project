<template>
    <div>
        <template v-if="field.filter.type === 'select'">
            <b-select
                :options="field.filter.options"
                @change="onInput"
                v-model="field.filter.value"
                size="sm"
            >
            </b-select>
        </template>
        <template v-else-if="field.filter.type === 'date'">
            <b-input-group size="sm">
                <b-form-input
                    @change="onInput"
                    type="date"
                    v-model="field.filter.value"
                />
                <b-button-close
                    v-b-tooltip.hover.right
                    title="Сбросить дату"
                    class="ml-1"
                    @click="onResetFilter"
                />
            </b-input-group>
            <!-- <b-form-datepicker
                style="min-width: 140px"
                :date-format-options="{
                    year: 'numeric',
                    month: 'numeric',
                    day: 'numeric',
                }"
                placeholder="Дата"
                @input="onInput"
                locale="ru"
                size="sm"
                label-no-date-selected="Дата не выбрана"
                label-help=""
                reset-button
                label-reset-button="Сбросить"
                v-model="field.filter.value"
            /> -->
        </template>
        <template v-else-if="field.filter.type === 'skip'"> </template>
        <template v-else-if="field.filter.type === 'reset-button'">
            <b-button size="sm" @click="onClickResetButton">Сбросить</b-button>
        </template>
        <template v-else>
            <b-form-input
                :name="field.filter.name"
                :type="field.filter.type"
                v-model="field.filter.value"
                @keyup.enter="onInput"
                size="sm"
            ></b-form-input>
        </template>
    </div>
</template>

<script>
export default {
    props: {
        field: Object,
    },
    methods: {
        onClickResetButton() {
            this.$emit('on-click-reset-button')
        },
        onInput() {
            this.$emit('on-input')
        },
        onResetFilter() {
            this.field.filter.value = null
            this.onInput()
            this.$emit('on-reset-filter', this.field)
        },
    },
}
</script>

<style>
</style>