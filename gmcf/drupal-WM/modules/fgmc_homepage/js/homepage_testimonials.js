/**
 * @file
 * Preview behaviors.
 */

(function ($, Drupal,drupalSettings) {

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
   Drupal.behaviors.fgmc_homepage_testimonials = {
    attach: function (context) {
        var $context = $(context);
        $('.loader_testimonial').show();
        $.ajax({url:'/fgmc_homepage_testimonial_content',method:'post',data:{'bnid':drupalSettings.fgmc_homepage.bannerNodeid[0]},type:'JSON',success:function(data){
            	setTimeout(function() {
				$('.loader_testimonial').hide();
                if(data.status==1){
                    $('.home-banner > .banner-caption > .tm-slider').html(data.template);
                    $('.loader_testimonial').hide();
                    $('.loader_products').hide();
                    jQuery('.bxslider-social-testimonial').show().bxSlider({
                                minSlides : 1,
                                maxSlides : 1,
                                slideWidth : 600,
                                slideMargin : 10,
                                auto: true,
                    });
                    //Accordian
                    jQuery('.accordion').each(function () {
                                    var accordian = jQuery(this);
                                    accordian.find('.accordion-head').on('click', function () {
                                        jQuery(this).parent().find(".accordion-head").removeClass('open close1');
                                        jQuery(this).removeClass('open').addClass('close1');
                                        accordian.find('.accordion-body').slideUp();
                                        if (!jQuery(this).next().is(':visible')) {
                                            jQuery(this).removeClass('close1').addClass('open');
                                            jQuery(this).next().slideDown();
                                        }
                                    });
                    });
                } else {
                     $('.loader_testimonial').html('Oops! something went wrong. Please reload the page after some time.');
                }
				},1000);
            },error:function(){
                $('.loader_testimonial').html('Oops! something went wrong. Please reload the page after some time.');
            }
        });
    },
   };
})(jQuery,Drupal,window.drupalSettings);
