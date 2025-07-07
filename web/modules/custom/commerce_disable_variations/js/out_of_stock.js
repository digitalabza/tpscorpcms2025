(function (Drupal, once) {
  "use strict";

  Drupal.behaviors.commerceDisableVariations = {
    attach(context, settings) {
      const cfg = settings.commerceDisableVariations;
      if (!cfg || !cfg.termAvailability) {
        return;
      }

      // ⚠️ Use the correct machine name "attribute_sizes"
      const attributeFields = ["attribute_sizes", "attribute_colour"];
      const selector = "form.commerce-order-item-add-to-cart-form";

      once("cdv-multi-attr", selector, context).forEach((formEl) => {
        const $form = jQuery(formEl);

        attributeFields.forEach((field) => {
          const availability = cfg.termAvailability[field] || {};

          // Rendered-entity radios
          $form.find(`input[type="radio"][name*="${field}"]`).each(function () {
            const $input = jQuery(this);
            const $wrapper = $input.closest(".form-item");
            const labelText = $wrapper
              .find(".attribute-value .field__item")
              .text()
              .trim();

            if (availability[labelText] === false) {
              $input.prop("disabled", true);
              $wrapper.addClass("out-of-stock no-hover");
              $wrapper.find(".attribute-value").css({
                "text-decoration": "line-through",
                color: "#aaa",
                opacity: "0.6",
              });
              $wrapper.on("click", (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();
              });
            }
          });

          // Title-widget dropdowns
          $form.find(`select[name*="${field}"] option`).each(function () {
            const $opt = jQuery(this);
            const text = $opt.text().trim();

            if (availability[text] === false) {
              $opt.prop("disabled", true).addClass("out-of-stock").css({
                color: "#999",
                "font-style": "italic",
              });
            }
          });
        });
      });
    },
  };
})(Drupal, once);
