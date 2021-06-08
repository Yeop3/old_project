<template>
        <div>
            <div class="mt-3 mb-2 adaptive-filter">
                <b-form-radio-group
                        buttons
                        button-variant="outline-primary"
                        :options="filterField.find((value) => value.name === 'order_status').options"
                        v-model="filterField.find((value) => value.name === 'order_status').value"
                        @input="filter"
                        size="xs"
                >
                </b-form-radio-group>
            </div>
        </div>
    </template>

    <script>
        export default {
            name: "order-filters",
            props: {
                orderCounterFilter: {
                    type: Object,
                },
                orderStatus: {
                    type: Object
                },
                // orderSellers: {
                //     type: Array
                // },
                orderCounterFilterStatus: {
                    type: Object
                },
                filterFields: {
                    type: Array,
                    default: () => ([])
                }
            },
            created() {
                console.log(this.orderCounterFilterStatus);

                this.filterField = [...this.filterFields];
                this.filterField.find((value) => value.name === 'order_status').options = [];
                this.filterField.find((value) => value.name === 'order_status').options.push({
                    text: 'Все',
                    value: null
                });
                // console.log(this.orderStatus);
                for (let item in this.orderStatus) {
                    if (this.orderStatus.hasOwnProperty(item)) {
                        this.filterField.find((value) => value.name === 'order_status').options.push({
                            text: this.orderStatus[item] + ` (${this.orderCounterFilterStatus.order_status[item] || 0})`,
                            value: item
                        });
                    }
                }
            },
            data() {
                return {
                    filterField: [],
                };
            },
            methods: {
                filter() {
                    this.$emit('filters', this.filterField);
                }
            }
        };
</script>

<style scoped>
    @media (max-width: 1140px) {
        .adaptive-filter > div {
            display: flex;
            flex-flow: row wrap;
        }
    }
</style>