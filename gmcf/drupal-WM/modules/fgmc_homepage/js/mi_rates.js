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
   Drupal.behaviors.fgmc_homepage = {
    attach: function (context) {
        console.log(drupalSettings)
        var $context = $(context);
        $.ajax({url:'/fgmc_homepage_rates',method:'post',type:'JSON',success:function(data){
                if(data.status==1){
                    $('#mirates').html(data.template);
                } else {
                    $('#mirates').html('Oops! something went wrong. Please try after some time');
                }
            },error:function(){
                $('#mirates').html('Oops! something went wrong. Please try after some time');
            }
        });
        $.ajax({url:'/fgmc_homepage_terms_disclosure',data:{'tdid':drupalSettings.fgmc_homepage.nodeid[0]},method:'post',type:'JSON',success:function(data){
                if(data.status==1){
                    $('#termAndDisTitle').html(data.title);
                    var templatebody = data.body+'<script>'+data.node_js+'</script>';
                    $('#termAndDisBody').html(templatebody);
                } else {
                    $('#termAndDisBody').html('Oops! something went wrong. Please try after some time');
                }
            },error:function(){
                $('#termAndDisBody').html('Oops! something went wrong. Please try after some time');
            }
        });
        $.ajax({url:'/fgmc_homepage_rate_date_time',method:'post',type:'JSON',success:function(data){
                if(data.status==1){
                    $('#ratetime').html(data.Ratetime);
                    $('#ratedate').html(data.Ratedate);
                } else {
                    $('#ratedatetimeresponse').html('Oops! something went wrong. Please try after some time').show();
                }
            },error:function(){
                $('#ratedatetimeresponse').html('Oops! something went wrong. Please try after some time').show();
            }
        });
    },
   };
})(jQuery,Drupal,window.drupalSettings);
