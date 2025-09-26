/**
 * @file
 * Profile Sticky Header functionality for Account Manager Profile pages.
 */

(function ($, Drupal) {
  'use strict';

  /**
   * Profile Sticky Header behavior.
   */
  Drupal.behaviors.profileStickyHeader = {
    attach: function (context, settings) {
      var $stickyHeader = $('#profileStickyHeader', context);

      // Only run on profile pages that have the sticky header element
      if ($stickyHeader.length === 0) {
        return;
      }

      var $window = $(window);
      var triggerPoint = 1; // Show sticky header after 1px scroll (reliably hides at top)
      var isVisible = false;

      /**
       * Handle scroll events to show/hide sticky header.
       */
      function handleScroll() {
        var scrollTop = $window.scrollTop();

        if (scrollTop > triggerPoint && !isVisible) {
          // Show sticky header
          $stickyHeader.addClass('visible');
          isVisible = true;
        } else if (scrollTop <= triggerPoint && isVisible) {
          // Hide sticky header
          $stickyHeader.removeClass('visible');
          isVisible = false;
        }
      }

      /**
       * Throttle function to limit scroll event frequency.
       */
      function throttle(func, limit) {
        var inThrottle;
        return function() {
          var args = arguments;
          var context = this;
          if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(function() {
              inThrottle = false;
            }, limit);
          }
        };
      }

      // Attach scroll event with throttling
      var throttledScrollHandler = throttle(handleScroll, 10);
      $window.on('scroll.profileStickyHeader', throttledScrollHandler);

      // Check initial scroll position
      handleScroll();

      /**
       * Handle WhatsApp button analytics (if needed)
       */
      $stickyHeader.find('.sticky-whatsapp-btn').on('click', function() {
        // Track WhatsApp clicks for analytics
        if (typeof gtag !== 'undefined') {
          gtag('event', 'click', {
            'event_category': 'Profile Contact',
            'event_label': 'Sticky Header WhatsApp',
            'transport_type': 'beacon'
          });
        }
      });

      // Cleanup function for when the behavior is detached
      $(context).on('remove', function() {
        $window.off('scroll.profileStickyHeader');
      });
    },

    /**
     * Detach behavior.
     */
    detach: function (context, settings, trigger) {
      if (trigger === 'unload') {
        $(window).off('scroll.profileStickyHeader');
      }
    }
  };

  /**
   * Additional profile page enhancements.
   */
  Drupal.behaviors.profilePageEnhancements = {
    attach: function (context, settings) {
      var $profilePage = $('.account-manager-profile-page', context);

      if ($profilePage.length === 0) {
        return;
      }

      // Add smooth scrolling for any anchor links within profile page
      $profilePage.find('a[href^="#"]').on('click', function(e) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
          e.preventDefault();
          $('html, body').animate({
            scrollTop: target.offset().top - 100 // Account for sticky header
          }, 600);
        }
      });

      // Add loading animation to contact buttons (optional)
      $profilePage.find('.contact-btn').on('click', function() {
        var $btn = $(this);
        $btn.addClass('loading');

        // Remove loading class after a short delay (for visual feedback)
        setTimeout(function() {
          $btn.removeClass('loading');
        }, 300);
      });

      // Intersection Observer for animations (if needed)
      if ('IntersectionObserver' in window) {
        var observerOptions = {
          threshold: 0.1,
          rootMargin: '0px 0px -50px 0px'
        };

        var observer = new IntersectionObserver(function(entries) {
          entries.forEach(function(entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('animate-in');
            }
          });
        }, observerOptions);

        // Observe bio section for animation
        var $bioSection = $profilePage.find('.profile-bio-section')[0];
        if ($bioSection) {
          observer.observe($bioSection);
        }
      }
    }
  };

})(jQuery, Drupal);