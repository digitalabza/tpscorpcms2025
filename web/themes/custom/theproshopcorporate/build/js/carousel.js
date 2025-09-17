/******/ (function() { // webpackBootstrap
/*!****************************!*\
  !*** ./src/js/carousel.js ***!
  \****************************/
/**
 * Carousel Component
 */
(function ($, Drupal) {
  Drupal.behaviors.componentCarousel = {
    attach: function attach(context, settings) {
      // Carousel
      if ($('.component-carousel__slider').length) {
        $('.component-carousel__slider', context).once('carouselComponent').each(function () {
          $(this).slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: true,
            arrows: true,
            dots: true,
            focusOnSelect: true,
            infinite: true,
            adaptiveHeight: true
          });
        });
      }
    }
  };
})(jQuery, Drupal);
/******/ })()
;
//# sourceMappingURL=carousel.js.map