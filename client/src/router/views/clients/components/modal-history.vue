<template>
  <b-modal
      :id="id"
      :title="client ? `@${client.username || client.name}(${client.telegram_id}) история изменений` : ''"
      lazy
      hide-footer
  >
    <b-table
        v-if="client"
        :items="clientHistory"
        :fields="fields"
        class="text-center"
        small
        no-local-sorting
        :emptyText="'История пуста'"
        show-empty
    >
      <template v-slot:cell(name)="data">
        <template v-if="data.item.username">
          @{{ data.item.username }}
        </template>
        <b-badge variant="warning" v-else>
          Ник не указан
        </b-badge>
      </template>
      <template v-slot:cell(username)="data">
        <template v-if="data.item.name">
          {{ data.item.name }}
        </template>
        <b-badge variant="warning" v-else>
          Имя не указано
        </b-badge>
      </template>
      <template v-slot:cell(created_at)="data">
        {{
          new Date(data.item.created_at).toLocaleString("ru-RU", {
            year: 'numeric', month:
                'numeric',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
          })
        }}
      </template>
    </b-table>
  </b-modal>
</template>

<script>

export default {
  name: "modal-history",
  components: {},
  data() {
    return {
      fields: [
        {
          key: 'name',
          label: 'Имя',
        },
        {
          key: 'username',
          label: 'Ник',
        },
        {
          key: 'created_at',
          label: 'Дата',
        },
      ]
    }
  },
  async mounted() {
  },
  props: {
    titleModal: {
      type: String
    },
    id: {
      type: String,
      required: true,
    },
    client: {
      type: Object,
    },
    clientHistory: {
    	type: Array,
    }
  },
  methods: {
  }
}
</script>