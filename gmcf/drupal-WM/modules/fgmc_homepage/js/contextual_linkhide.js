/**
 * @file
 * Preview behaviors.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Disables all non-relevant links in node previews.
   *
   * Destroys links (except local fragment identifiers such as href="#frag") in
   * node previews to prevent users from leaving the page.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches confirmation prompt for clicking links in node preview mode.
   * @prop {Drupal~behaviorDetach} detach
   *   Detaches confirmation prompt for clicking links in node preview mode.
   */
   Drupal.behaviors.fgmc_homepage = {
    attach: function (context) {
        var $context = $(context);
         var contextual = Drupal.contextual;
         var linkwrapper = $context.find('[data-contextual-id]');
         $(linkwrapper).hide();
    },
   };
    
})(jQuery,Drupal);