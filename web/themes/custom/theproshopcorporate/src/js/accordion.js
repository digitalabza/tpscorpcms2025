/**
 * Accordion Component
 */
(function ($, Drupal) {

  Drupal.behaviors.componentAccordion = {
    attach: function (context, settings) {

      $('.component-accordion', context).once('accordionComponent').each(function () {
        var $itemTitle = $(this).find('.component-accordion__item__title');

        // Accordion item click
        $itemTitle.click(function (e) {
          var $item = $(this).parent();
          var $itemContent = $item.find('.component-accordion__item__content');

          // Set item active and toggle content
          $item.toggleClass('active');
          $itemContent.toggle('fast');

          // Unset other active items and hide their content
          $('.component-accordion__item').not($item).removeClass('active');
          $('.component-accordion__item__content').not($itemContent).hide('fast');

          e.preventDefault();
        });
      });

    }
  };

})(jQuery, Drupal);