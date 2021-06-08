<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card-box row justify-content-center">
                <b-form @submit.prevent="sumbitForm" class="col-lg-6 col-md-7 col-sm-12">

                    <form-select
                            name="driver_id"
                            label="Курьер"
                            v-model="field.driver_id"
                            :options="driversSelect"
                            :errors="errors"
                            :size="'sm'"
                    />

                    <b-form-group
                            label="Товар"
                            class="product-type"
                    >
                        <Multiselect
                                v-model="product_type"
                                :options="productTypesSelect"
                                track-by="value"
                                label="text"
                                :state="errors['product_type_id'] === undefined ? null : false"
                                placeholder="Выберите тип товара..."
                                searchable
                                selectLabel="Нажмите Enter, чтобы выбрать"
                                selectedLabel="Выбрано"
                                deselectLabel="Нажмите Enter, чтобы удалить"
                        >
                        </Multiselect>

                        <div class="invalid-feedback" :class="{'d-block': errors['product_type_id'] || false}">
                            <span :key="i" v-for="(error, i) in errors['product_type_id']">
                                {{error}}
                            </span>
                        </div>
                    </b-form-group>

                    <b-form-group
                            label="Локация"
                    >
                        <Multiselect
                                v-model="location"
                                :options="locationsSelect"
                                track-by="value"
                                label="text"
                                :state="errors['location_id'] === undefined ? null : false"
                                placeholder="Выберите локацию..."
                                searchable
                                selectLabel="Нажмите Enter, чтобы выбрать"
                                selectedLabel="Выбрано"
                                deselectLabel="Нажмите Enter, чтобы удалить"
                        >
                        </Multiselect>

                        <div class="invalid-feedback" :class="{'d-block': errors['location_id'] || false}">
                            <span :key="i" v-for="(error, i) in errors['location_id']">
                                {{error}}
                            </span>
                        </div>
                    </b-form-group>

                    <form-select
                            v-if="[1, 2, '1', '2'].includes(field.status)"
                            name="status"
                            label="Статус"
                            v-model="field.status"
                            :options="statusListEditable"
                            :errors="errors"
                            :size="'sm'"
                    >
                    </form-select>

                    <b-row>
                        <b-col lg="8" md="7">

                            <form-input
                                    name="commission_value"
                                    label="Комиссия"
                                    v-model="field.commission_value"
                                    :errors="errors"
                                    type="number"
                                    :min="0"
                                    :size="'sm'"
                                    description="Дополнительная наценка на товары данного типа"

                            >
                            </form-input>
                        </b-col>
                        <b-col lg="4" md="5">

                            <form-select
                                    name="commission_type"
                                    label="Тип"
                                    v-model="field.commission_type"
                                    :options="commission_types"
                                    :errors="errors"
                                    :size="'sm'"
                            >
                            </form-select>
                        </b-col>
                    </b-row>

                    <template  v-if="!isCreateMany">
                        <form-input
                            name="coordinates"
                            label="Координаты"
                            v-model="field.coordinates"
                            :errors="errors"
                            :size="'sm'"
                            description="Например: 49.515574, 33.213522"
                        >
                        </form-input>

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

                        <form-input-file
                                name="video"
                                label="Видео"
                                v-model="field.video"
                                :errors="errors"
                                :size="'sm'"
                                :placeholder="field.video_url ? 'Есть загруженное видео' : ''"
                        >
                        </form-input-file>

                        <b-embed
                                v-if="field.video_url"
                                aspect="16by9"
                                controls
                                :src="field.video_url"
                        />

                        <form-input
                                v-if="!isCreateMany"
                                name="address"
                                label="Описание"
                                v-model="field.address"
                                :errors="errors"
                                :size="'sm'"
                        >
                        </form-input>
                    </template>

                    <template  v-else>
                        <form-input
                                name="count"
                                label="Количетво кладов"
                                v-model="productsCount"
                                type="number"
                                :min="1"
                                :size="'sm'"

                        >
                        </form-input>

                        <div v-for="i in parseInt(productsCount)" :key="i">
                            <h3>
                                Клад №{{i}}
                            </h3>

                            <form-input
                                    :name="`coordinates[${i - 1}]`"
                                    :label="`Координаты клада №${i}`"
                                    v-model="coordinateses[i - 1]"
                                    :errors="errors"
                                    :size="'sm'"
                                    description="Например: 49.515574, 33.213522"
                            >
                            </form-input>

                            <div v-if="imageBlobs.length && imageBlobs[i - 1]">
                                <img
                                        :src="imageBlobs[i - 1] || null"
                                        alt=""
                                        width="240"
                                        height="320"
                                        class="mb-1 mr-1"
                                >
                            </div>
                            <div v-else>
                                <h5>Фото не выбрано</h5>
                            </div>

                            <b-form-group>
                                <input
                                        type="file"
                                        class="d-none"
                                        :ref="`files_${i - 1}`"
                                        @input="handleInputFile(i - 1, $event)"
                                />

                                <button
                                        class="btn btn-info"
                                        @click.prevent="handleFileButtonClick(i - 1)"
                                >
                                    Выбрать фото клада №{{i}}
                                </button>
                            </b-form-group>

                            <form-input
                                    :name="`addresses[${i - 1}]`"
                                    :label="`Описание клада №${i}`"
                                    v-model="addresses[i - 1]"
                                    :errors="errors"
                                    :size="'sm'"
                            >
                            </form-input>

                            <hr>
                        </div>
                    </template>

                    <b-form-group
                            v-if="type !== 'Обновить'"
                            label="Информировать клиентов о пополнении с помощью активных ботов"
                    >
                        <Multiselect
                                v-model="bots_number"
                                :options="select_bots"
                                :multiple="true"
                                :close-on-select="false"
                                :clear-on-select="false"
                                :preserve-search="true"
                                placeholder=""
                                label="text"
                                track-by="value"
                                :state="errors['location_numbers'] === undefined ? null : false"
                                searchable
                                selectLabel="Нажмите Enter, чтобы выбрать"
                                selectGroupLabel="Нажмите Enter, чтобы выбрать группу"
                                selectedLabel="Выбрано"
                                deselectLabel="Нажмите Enter, чтобы удалить"
                                deselectGroupLabel="Нажмите Enter, чтобы отменить выбор группы"
                        >
                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span
                                    class="multiselect__single" v-if="values.length && !isOpen">{{ values.length }} выбрано</span>
                            </template>
                        </Multiselect>
                        <b-form-invalid-feedback :id="'location_numbers-feedback'">
                              <span :key="i" v-for="(error, i) in errors['location_numbers']">
                                {{error}}
                              </span>
                        </b-form-invalid-feedback>
                        <b-form-text>* После сохранения будет автоматически сгенерирован текст рассылки по наличию для
                            каждого выбранного бота (по шаблону описанному в логике бота) и поставлен в очередь на
                            рассылку. Данная функция работает только для товаров со статусом «Продается».
                        </b-form-text>
                    </b-form-group>

                    <b-form-group id="button-group" class="mt-4 column">
                        <div class="row justify-content-center">
                            <b-button type="submit"  variant="primary" class="btn-block col-4" :disabled="submitting">
                                {{type}}
                            </b-button>
                        </div>
                    </b-form-group>
                </b-form>
            </div>
        </div>
    </div>
</template>

<script>
    import FormInput from "@components/ui/form/FormInput";
    import FormInputFile from "@components/ui/form/FormInputFile";
    import FormSelect from "@components/ui/form/FormSelect";
    import Multiselect from "vue-multiselect";
    import {botsComputed, commissionComputed} from "@state/helpers";
    import FormTextarea from "@components/ui/form/FormTextarea";
    import VueUploadComponent from 'vue-upload-component';
    import {BIcon, BIconTrash} from 'bootstrap-vue';
    import axios from "axios";

    export default {
        name: "form-product",
        components: {
            FormTextarea,
            FormInput,
            FormSelect,
            Multiselect,
            FileUpload: VueUploadComponent,
            FormInputFile,
            BIcon,
            BIconTrash
        },
        computed: {
            ...commissionComputed,
            ...botsComputed,
            addressCount: {
                get: function () {
                    return [...new Set(this.field.address.split('\n').filter(Boolean))].length;
                },
                set() {

                }
            },
            statusListEditable() {
                return this.statusList.filter(s => parseInt(s.value) === 1 || parseInt(s.value) === 2);
            },
        },
        props: {
            product: {
                type: Object,
                default: () => {
                }
            },
            locationsSelect: {
                type: Array,
                default: () => []
            },
            type: {
                type: String,
            },
            errors: {
                type: Object
            },
            statusList: {
                type: Array,
                default: () => []
            },
            driversSelect: {
                type: Array,
                default: () => [],
            },
            productTypesSelect: {
                type: Array,
                default: () => [],
            },
            isCreateMany: {
                type: Boolean,
                default: false
            },
            submitting: {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                field: {},
                files: [],
                bots_number: [],
                product_type: null,
                location: null,
                productsCount: 1,
                addresses: [],
                coordinateses: [],
                images: [],
                imageBlobs: [],
                parents: [
                    {
                        value: null,
                        text: "Нет"
                    }
                ],
                accept: 'image/jpeg,image/JPEG,image/png',
                extensions: 'jpeg,JPEG,png',
                minSize: 1024,
                size: 1024 * 5,
            };
        },
        created() {
            this.field = {...this.product};

            if (this.product.product_type) {
                this.product_type = this.productTypesSelect.find(p => p.value === this.product.product_type.number);
            }

            if (this.product.location) {
                this.location = this.locationsSelect.find(p => p.value === this.product.location.number);
            }

        },
        methods: {
            deletePhoto(number) {
                axios
                    .delete('/api/products/photos/' + number)
                    .then(() => {
                        this.field.photos = this.field.photos.filter(p => parseInt(p.number) !== parseInt(number));
                    });
            },
            handleInputFile(i, event) {
                const file = event.target.files[0]

                this.images[i] = file;

                let URL = window.URL || window.webkitURL;
                if (URL && URL.createObjectURL) {
                    this.$set(this.imageBlobs, i, URL.createObjectURL(file))
                    // this.imageBlobs[i] = URL.createObjectURL(file);
                }
            },
            handleFileButtonClick(i) {
                this.$refs[`files_${i}`][0].click();
            },
            sumbitForm() {
                if (this.location) {
                    this.field.location_id = this.location.value;
                }

                if (this.product_type) {
                    this.field.product_type_id = this.product_type.value;
                }

                this.field.bots_numbers = this.bots_number.map(value => (value.value));

                this.field.images = this.files.map(f => f.file);

                if (this.addresses.length) {
                    this.field.addresses = this.addresses;
                }

                if (this.images.length) {
                    this.field.images = this.images;
                }

                if (this.coordinateses.length) {
                    this.field.coordinates = this.coordinateses;
                }

                this.$emit('sumbit-form', this.field);
            },

            inputFile: function (newFile, oldFile) {
                if (newFile && oldFile && !newFile.active && oldFile.active) {
                    // Get response data
                   // console.log('response', newFile.response)
                    if (newFile.xhr) {
                        //  Get the response status code
                       // console.log('status', newFile.xhr.status)
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
        },
    };
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style lang="scss">
    .multiselect{
        min-height: 31px !important;
    }

    .multiselect__select{
        height: 31px !important;
    }

    .multiselect__tags{
        min-height: 31px !important;
        padding-top: 0;
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