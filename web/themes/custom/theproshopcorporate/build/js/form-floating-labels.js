/******/ (function() { // webpackBootstrap
/*!****************************************!*\
  !*** ./src/js/form-floating-labels.js ***!
  \****************************************/
/**
 * Form Floating Labels
 */
(function ($, Drupal) {
  Drupal.behaviors.formFloatingLabels = {
    attach: function attach(context, settings) {
      $('.form-item--floater', context).once('formFloatingLabels').each(function () {
        var $this = $(this);
        var $element = $this.find('.form-control');
        var $placeholder = $element.attr('placeholder');

        // Set the element active if it has a value
        if ($element.is('select') && $element.find('option:selected')) {
          $this.addClass('is-active');
        } else if ($element.val()) {
          $this.addClass('is-active');
        } else {
          // Check elements for browser autofill
          var checkAutofill = function checkAutofill() {
            try {
              autofilled = $element.is(":autofill");
            } catch (error) {
              try {
                autofilled = $element.is(":-webkit-autofill");
              } catch (error) {}
            }
            if (autofilled) {
              $this.addClass('is-active');
            }
          };
          var autofilled = false;
          checkAutofill();
          setTimeout(checkAutofill, 600);
        }

        // Remove placeholder initially
        $element.attr('placeholder', '');

        // Focus
        $element.on('focus', function () {
          $this.addClass('is-active');
          $element.attr('placeholder', $placeholder);
        });

        // Blur
        $element.on('blur', function () {
          if (!$element.is('select')) {
            if (!$element.val()) {
              $this.removeClass('is-active');
            }
            $element.attr('placeholder', '');
          }
        });
      });
    }
  };
})(jQuery, Drupal);
/******/ })()
;
//# sourceMappingURL=form-floating-labels.js.map