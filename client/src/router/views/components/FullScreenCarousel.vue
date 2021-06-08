<template>
  <div>
  <b-modal class="full-modal" id="full-screen-modal" hide-footer hide-header dialog-class="full-modal-dialog" content-class="full-modal-content" body-class="full-modal-body">

    <b-button-close @click="$bvModal.hide('full-screen-modal')" class="full-modal-close-button"></b-button-close>
    <b-spinner style="width: 3rem; height: 3rem;" class="full-model-loading"></b-spinner>
       <b-carousel
          ref="carousel"
          @click.native="clickOnCarousel"
          class="d-flex align-items-center justify-content-center vh-100"
          :interval="5000"
          no-animation
          :controls="photos.length > 1"
          indicators
          @sliding-start="onSlideStart"
          @sliding-end="onSlideEnd"
    >


      <b-carousel-slide v-for="photo in photos" :key="photo.number">
        <template v-slot:img>
          <b-img-lazy
            style="max-height:100vh"
            class="d-block mx-auto img-fluid"
            :src="photo.url"
          />
        </template>
      </b-carousel-slide>


    </b-carousel>
  </b-modal>
  </div>
</template>

<script>
import { BCarousel, BCarouselSlide, BIconX } from 'bootstrap-vue'
export default {
  props: {
    photos: Array,
  },
  components: {
    BCarousel,
    BCarouselSlide,
    BIconX,
  },
  data() {
    return {
      sliding: null
    }
  },
  methods: {
    onSlideStart(slide) {
      this.sliding = true
    },
    onSlideEnd(slide) {
      this.sliding = false
    },
    clickOnCarousel() {
      if (!this.sliding) {
        this.$bvModal.hide('full-screen-modal')
      }
    }
  }
}
</script>

<style>
    .full-model-loading {
        position: absolute;
        top: 50%;
        left: 50%;
    }
    .full-modal {
        position: relative;
    }
    .full-modal-close-button {
        color: #fff;
        position: absolute;
        top: 25px;
        right: 25px;
        z-index: 100;
        padding: 10px 15px;
        font-size: 2rem;
    }
    .full-modal-close-button:hover {
        color: #fff;
    }
    .full-modal-dialog {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    max-width: none;
    }

    .full-modal-content {
    height: 100%;
    border-radius: 0;
    border: none;
    background-color:rgba(0, 0, 0, 0.8);
    }

    .full-modal-body {
    padding: 0;

    }
</style>