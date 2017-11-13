jQuery(document).ready(function(){
    jQuery("#node-basic-page-edit-form input").click(function(){
        jQuery('#node-basic-page-edit-form').removeAttr('data-drupal-form-submit-last');
        if(jQuery(this).attr("id") != 'edit-preview'){
            
            jQuery('#node-basic-page-edit-form').removeAttr('target');
        }
    });
    jQuery("#node-home-page-blocks-edit-form input").click(function(){
        jQuery('#node-home-page-blocks-edit-form').removeAttr('data-drupal-form-submit-last');
        if(jQuery(this).attr("id") != 'edit-preview'){
            
            jQuery('#node-home-page-blocks-edit-form').removeAttr('target');
        }
    });
    jQuery("#node-banner-edit-form input").click(function(){
        jQuery('#node-banner-edit-form').removeAttr('data-drupal-form-submit-last');
        if(jQuery(this).attr("id") != 'edit-preview'){
            
            jQuery('#node-banner-edit-form').removeAttr('target');
        }
    });
    var path = window.location.href;
    path = path.split('/');
    if((jQuery.inArray( "states", path ) >= 0) && (jQuery.inArray( "overview", path ) >= 0)){
        jQuery('.action-links a').text('Add state');
        var link = jQuery('.action-links a').attr('href');
        jQuery('.action-links a').attr('href', link + '?destination=/admin/structure/taxonomy/manage/states/overview');
    }
    if((jQuery.inArray( "states", path ) >= 0) && (jQuery.inArray( "add", path ) >= 0)){
        jQuery('#block-adminimal-theme-page-title h1').text('Add state');
        var link = jQuery('.action-links a').attr('href');
        jQuery('.action-links a').attr('href', link + '?destination=/admin/structure/taxonomy/manage/states/overview');
    }
    if(jQuery("#node-preview-form-select").length) {
        jQuery("#node-preview-form-select").remove();
    }
});


