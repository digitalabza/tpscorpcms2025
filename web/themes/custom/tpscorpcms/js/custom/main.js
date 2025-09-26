
(function ($, Drupal, once) {
  'use strict';


  // External links.
  $("a[href^='http']").each(function() {
    var re_matches = /https?:\/\/([^\/]*)/.exec($(this).attr('href'));
    // Check link against the current domain.
    if(re_matches && re_matches[1] && re_matches[1] != location.hostname && re_matches[1] != 'www.'+location.hostname && 'www.'+re_matches[1] != location.hostname) {
      $(this).attr('target', '_blank');
    }
  });

  // Mobile Toggle.
  // Toggle active classes for a piece of content.
  $('.js-mobile-toggle').click(function (e) {
    var $this = $(this);
    var $toggleContent = $this.data('toggle-content');

    $this.toggleClass('is-active');
    $('.' + $toggleContent).toggleClass('is-active');

    e.preventDefault();
  });

  // Mobile Overlay.
  $('.mobile-overlay').click(function(e) {
    $(this).fadeOut('fast');
    $('.mobile-search-form input.form-search').blur();
  });
  $('.mobile-overlay__content').click(function(e) {
    e.stopPropagation();
  });

  // Mobile Search.
  $('.mobile-control-nav .menu__item--search a').click(function(e) {
    $('.mobile-search-overlay').fadeIn('fast');
    $('.mobile-search-form .form-item-site-keyword input').focus();
    e.preventDefault();
  });
  $('.mobile-search-form__submit').click(function(e) {
    $('.mobile-search-form form').submit();
    e.preventDefault();
  });

  // Mobile Navigation.
  $('.mobile-control-nav .menu__item--menu a').click(function(e) {
    $('.mobile-nav-overlay').fadeIn('fast');
    e.preventDefault();
  });
  $('.mobile-overlay__close').click(function(e) {
    $('.mobile-overlay').fadeOut('fast');
    $('.mobile-search-form .form-item-site-keyword input').blur();
    e.preventDefault();
  });

  // Mobile Navigation - Clone expandable parent into sub-menu.
  $('.mobile-nav nav > ul > li.menu__item--expanded > a').each(function() {
    var $this = $(this);
    var $thisClone = $(this).clone();
    // Change the link text so there's not duplicate links beside each other.
    $thisClone.html(Drupal.t('Overview'));

    $thisClone.wrap('<li class="menu__item menu__item--parent-overview"></li>').parent().prependTo($this.next('ul'));
  });

  // Mobile Navigation - Click on parents to expand.
  $('.mobile-nav nav > ul > li.menu__item--expanded > a').click(function(e) {
    var $this = $(this);
    var $nextMenu = $this.next('ul');

    // Toggle slide animation for sub-menus.
    $nextMenu.slideToggle('fast');
    $('.mobile-nav nav > ul > li.menu__item--expanded > a').next('ul').not($nextMenu).slideUp('fast');

    e.preventDefault();
  });

  // Site search submit trigger.
  if ($('.form-search-submit-trigger').length) {
    $('.form-search-submit-trigger').click(function(e) {
      // Submit the parent form.
      $(this).parents('form').submit();
      e.preventDefault();
    });
  }

  // Form submit trigger.
  if ($('.form-submit--trigger').length) {
    $('.form-submit--trigger').click(function(e) {
      // Submit the parent form.
      $(this).parents('form').submit();
      e.preventDefault();
    });
  }

  // Magnific Popup Gallery.
  if ($('.magnific-popup-gallery').length) {
    $('.magnific-popup-gallery').magnificPopup({
      type: 'image',
      gallery: {
        enabled: true
      }
    });
  }

  // Tabs.
  if ($('.nav-tabs').length) {
    $('.nav-tabs').each(function () {
      $(this).tabCollapse();
    });
  }

  // Site Header Visibility Behavior
  Drupal.behaviors.siteHeaderVisibility = {
    attach: function (context, settings) {
      // Don't run site header behavior on account manager profile pages
      if ($('.account-manager-profile-page').length > 0) return;

      var $siteHeader = $(once('header-visibility', '.site-header', context));
      var $body = $('body');
      if ($siteHeader.length === 0) return;

      var isScrolling = false;

      // Show header and add body padding
      function showHeader() {
        $siteHeader.addClass('site-header--visible').removeClass('site-header--hidden');
        $body.addClass('header-visible');
      }

      // Hide header and remove body padding
      function hideHeader() {
        $siteHeader.addClass('site-header--hidden').removeClass('site-header--visible');
        $body.removeClass('header-visible');
      }

      // Initialize header state based on current scroll position
      function initializeHeaderState() {
        var scrollTop = $(window).scrollTop();
        if (scrollTop === 0) {
          // At the top - header should be hidden
          hideHeader();
        } else {
          // Not at top - header should be visible
          showHeader();
        }
      }

      // Throttled scroll handler for header visibility
      function handleHeaderVisibility() {
        var scrollTop = $(window).scrollTop();

        if (scrollTop === 0) {
          // Exactly at the top - hide header
          hideHeader();
        } else {
          // Any scroll down - show header and keep it visible
          showHeader();
        }

        isScrolling = false;
      }

      // Initialize header state on page load
      initializeHeaderState();

      // Passive scroll listener for header visibility
      $(window).on('scroll.headerVisibility', function() {
        if (!isScrolling) {
          requestAnimationFrame(handleHeaderVisibility);
          isScrolling = true;
        }
      });

      // Handle page resize and orientation changes
      $(window).on('resize.headerVisibility orientationchange.headerVisibility', function() {
        setTimeout(initializeHeaderState, 100);
      });
    }
  };

  // Scroll To Top Button Behavior
  Drupal.behaviors.scrollToTop = {
    attach: function (context, settings) {
      var $scrollToTopButton = $(once('scroll-to-top', '.b-page-scroll-to-top', context));
      if ($scrollToTopButton.length === 0) return;

      var isScrolling = false;

      // Throttled scroll handler for scroll-to-top button
      function handleScrollToTop() {
        var scrollTop = $(window).scrollTop();

        if (scrollTop > 300) {
          $scrollToTopButton.addClass('is-active');
        } else {
          $scrollToTopButton.removeClass('is-active');
        }

        isScrolling = false;
      }

      // Passive scroll listener for scroll-to-top button
      $(window).on('scroll.scrollToTop', function() {
        if (!isScrolling) {
          requestAnimationFrame(handleScrollToTop);
          isScrolling = true;
        }
      });

      // Click handler for scroll-to-top button
      $scrollToTopButton.click(function (e) {
        $('html, body').animate({ scrollTop: 0 }, 360);
        e.preventDefault();
      });
    }
  };

})(jQuery, Drupal, once);
