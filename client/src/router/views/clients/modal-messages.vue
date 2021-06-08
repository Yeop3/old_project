<template>
  <b-modal
      :id="id"
      :ref="id"
      :title="titleModal"
      @ok="handleOkGive"
      lazy
  >
    <form ref="form" @submit.stop.prevent="handleSubmit">
      <form-select
          v-model="field.bot_number"
          :options="select_bots_for_client"
          description="* Выберите бота."
          label="Бот"
          :size="'sm'"
      />

      <form-textarea
          name="price"
          description="* Введите сообщение."
          v-model="field.message"
          label="Сообщение, которое хотите отправить."
          :size="'sm'"
      ></form-textarea>

      <div class="d-flex justify-content-between flex-wrap">
        <div class="position-relative"  v-for="photo in field.photos">
          <img
                  :key="photo.number"
                  :src="photo.url"
                  alt=""
                  width="120"
                  height="120"
                  class="mb-2 mr-2"
          >

          <b-button
                  class="position-absolute"
                  style="color: white;right: 12px;top:0"
                  size="xs"
                  variant="danger"
                  @click.prevent="deletePhoto(photo.number)"
          >
            <b-icon
                    icon="trash"
            />
          </b-button>
        </div>

        <div class="position-relative" v-for="(file, k) in files">
          <img
                  :src="file.blob"
                  alt=""
                  width="120"
                  height="120"
                  class="mb-2 mr-2"
          >

          <b-button
                  class="position-absolute"
                  style="color: white;right: 12px;top:0"
                  size="xs"
                  variant="danger"
                  @click.prevent="files = files.filter((file, fileIndex) => fileIndex !== k)"
          >
            <b-icon
                    icon="trash"
            />
          </b-button>
        </div>
      </div>

      <b-form-group label="Загрузите фотки (максимум 5 штук)" v-if="5 - field.photos.length > 0">
        <file-upload
                ref="upload"
                v-model="files"
                @input-file="inputFile"
                @input-filter="inputFilter"
                class="btn btn-info mb-2"
                :extensions="extensions"
                :accept="accept"
                :multiple="true"
                :maximum="5 - field.photos.length"
        >
          Выбрать фотки
        </file-upload>

        <div class="invalid-feedback" :class="{'d-block': errors['images'] || false}">
                                <span :key="i" v-for="(error, i) in errors['images']">
                                    {{error}}
                                </span>
        </div>
      </b-form-group>

    </form>
  </b-modal>
</template>

<script>
import FormTextarea from '@components/ui/form/FormTextarea'
import FormSelect from '@components/ui/form/FormSelect'
import {mapActions, mapState} from "vuex";
import VueUploadComponent from 'vue-upload-component';
import {BIcon, BIconTrash} from 'bootstrap-vue';

export default {
  name: "modal-messages",
  components: {FormTextarea, FileUpload: VueUploadComponent, BIcon, BIconTrash, FormSelect},
  computed: {
    ...mapState('bots', {
      select_bots: (state) => state.select_bots,
      select_bots_for_client: (state) => state.select_bots_for_client,
    }),
  },
  data() {
    return {
      field: {
        message: '',
        bot_number: null,
        photos: [],
        number: null
      },
      files: [],
      accept: 'image/jpeg,image/JPEG,image/png',
      extensions: 'jpeg,JPEG,png',
    }
  },
  async mounted() {
    await this.getSelectByClient(this.item.number);
    this.$bvModal.show('modal-message');
  },
  props: {
    titleModal: {
      type: String
    },
    item: {
      type: [Object],
      default: () => null
    },
    id: {
      type: String
    },
    errors : {
      type: Object
    }
  },
  methods: {
    ...mapActions('bots', ['getSelect', 'getSelectByClient']),
    handleSubmit() {
      this.field.photos = this.files.map(f => f.file);
      this.field.number = this.item.number;
      this.$emit('handle-submit', this.field);
    },
    handleOkGive(bvModalEvt) {
      bvModalEvt.preventDefault();
      this.handleSubmit();
    },
    inputFile: function (newFile, oldFile) {
      if (newFile && oldFile && !newFile.active && oldFile.active) {
        // Get response data
        console.log('response', newFile.response)
        if (newFile.xhr) {
          //  Get the response status code
          console.log('status', newFile.xhr.status)
        }
      }
    },
    inputFilter: function (newFile, oldFile, prevent) {
      if (newFile && !oldFile) {
        // Filter non-image file
        if (!/\.(jpeg|jpe|jpg|gif|png|webp)$/i.test(newFile.name)) {
          return prevent()
        }
      }

      // Create a blob field
      newFile.blob = ''
      let URL = window.URL || window.webkitURL
      if (URL && URL.createObjectURL) {
        newFile.blob = URL.createObjectURL(newFile.file)
      }
    },
  }
}
</script>

<style scoped>

</style>