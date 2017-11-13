jQuery(document).ready(function(){
	jQuery('body').show();
	// jQuery('ul.nav li.dropdown').hover(function() {
	//   jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
	// }, function() {
	//   jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
	// });
	// jQuery('li.dropdown-submenu').hover(function() {
	//   jQuery(this).find('.dropdown-menu1').stop(true, true).delay(200).fadeIn(500);
	// }, function() {
	//   jQuery(this).find('.dropdown-menu1').stop(true, true).delay(200).fadeOut(500);
	// });

	jQuery('[data-toggle="tooltip"]').tooltip();
	jQuery('#quick-rate').hide();

	$('#menu12 a.quick-rate-btn').click(function(){
		$('#quick-rate').toggle(1000);
		$('.quick-rate-btn span').toggleClass("glyphicon-remove");
		jQuery(".select_loan_products").multiselect('refresh');
		if($("#loan_prds").val()) {
			var loan_prds_json = JSON.parse($("#loan_prds").val()); 
			jQuery(".select_loan_products").multiselect('deselect',loan_prds_json);
		}
		$('.multiSelect .btn-group .btn-default').removeClass('error');
		$('#quick_rate_form')[0].reset();
		$('#quick_rate_ref_form')[0].reset();
		$("#quick_rate_form div.error").hide();
		$("#quick_rate_ref_form div.error").hide();
		$('form#quick_rate_form').find('input, select').each(function(){
			$(this).removeClass('error'); // This is the jquery object of the input, do what you will
		});
		$('form#quick_rate_ref_form').find('input, select').each(function(){
			$(this).removeClass('error'); // This is the jquery object of the input, do what you will
		});
		if($("#menu12 a.quick-rate-btn span").hasClass('glyphicon-remove')) {
			$('#menu12').animate({bottom: '2%'}, 'slow');
		}	else {
			$('#menu12').animate({bottom: '22%'}, 'slow');
		}
	});

	jQuery('.slide-menu-open').on('click', function () {
        jQuery('.side-menu-wrapper, .side-menu-overlay').addClass('open');
    });
    jQuery('.menu-close, .side-menu-overlay').on('click touchstart', function () {
        jQuery('.side-menu-wrapper, .side-menu-overlay').removeClass('open');
    });
	
	jQuery(document).on("click", ".collapseLink", function () {
   
	    var sign = jQuery(this).children();
	    if (sign.hasClass("glyphicon-plus")) {
	        sign.removeClass("glyphicon-plus").addClass("glyphicon-minus");
	    } else {
	        sign.removeClass("glyphicon-minus").addClass("glyphicon-plus");
	    }
	});
	
	
    setTimeout(function() {
        jQuery('.section-1').each(function(){  
            var highestBox = 0;

            jQuery(this).find('.lightPink').each(function(){
                if(jQuery(this).height() > highestBox){  
                    highestBox = jQuery(this).height();  
                }
            })

            jQuery(this).find('.lightPink').height(highestBox);
        });    
                jQuery('.section-2').each(function(){  
            var highestBox = 0;

            jQuery(this).find('.column').each(function(){
                if(jQuery(this).height() > highestBox){  
                    highestBox = jQuery(this).height();  
                }
            })

            jQuery(this).find('.column').height(highestBox);
        }); 


    },2000);
     
    var path = window.location.href; var refresh = path;
    path = path.split('/');
    if((jQuery.inArray( "preview", path ) >= 0) && (jQuery.inArray( "draft", path ) <= 0)){
        jQuery('.text-right .btn-success').removeAttr('onclick');
        jQuery('.edit_homesections').hide();
        window.onbeforeunload = function () {var c = 1;
            if(jQuery('#responsive-preview-toolbar-tab .responsive-preview-options').find('.active').length){
                c = 2;
            }
            if(jQuery('.responsive-preview-frame').length){
                c = 3;
            }
            if(c <= 2){
                return false;
            }
        };
        jQuery('.region-content').prepend('<p class="preview"> Preview Mode</p>');
    }
    
    //jQuery(".text-right .btn-success, a[class!='refresh-btn'], button").click(function(event){
    jQuery("a[class!='terms-btn']").click(function(event){
        var base_url = window.location.origin;
        if((jQuery.inArray( "preview", path ) >= 0) && (jQuery(this).attr('href') != '#')){
            
            var r = confirm("Are you sure you want to navigate out of preview mode?");
            if (r == false) {
                
                if(jQuery(this).hasClass('menu-close')){return;  }
                event.preventDefault();
            }else{
                if(jQuery(this).attr('href')){
                    window.open(base_url+"/"+jQuery(this).attr('href'), '', "height=970,width=920");
                    event.preventDefault();
                }
            }
        }
    });

    if(jQuery("#edit-backlink").length) {
        $(document).on("keydown", disableF5);
    }
	
	// hiding search results page title
	jQuery(".region-content").find('form#search-form').next('h2').hide();
    
    jQuery(document).on("click",".ft_bot",function(event){
        event.preventDefault();
        window.open(jQuery(this).attr('href'), '', "height=970,width=920");
    });

     jQuery(document).on('click','#search',function() {
         jQuery('.mob-search').slideToggle('fast');
     });
     
     


});
function disableF5(e) { 
    if (e.keyCode == 116 ){ 
        e.preventDefault();
        alert('You should not refresh the preview page');
    } 
};


    function redirect_To_CD_App(data,pageType){
                //pageType value will be calc or login or register
                var apiurl=drupalSettings.path.wm_cd_integration_api;
                var RedirectionUrl=drupalSettings.path.wm_cd_redirection;
                var pageNames= ["prequal", "register", "login"];
				if(pageType=='Apply'){//Api call not required since it is not saving anything
					var locationhref= RedirectionUrl+'external?targetRoute='+pageNames[0];
					//Redirect  
					window.open(locationhref);
					//jQuery(location).attr('href',locationhref);
				}else{
					var accesType = 'POST';
					var routingUrl = drupalSettings.path.wm_cd_integration_api;
					if(data.id != '00000000-0000-0000-0000-000000000000') {
						accesType = 'PUT';
						routingUrl = routingUrl+"/"+data.id;
					}
					jQuery.ajax({
                        type: accesType,
                        data: JSON.stringify(data),
                        dataType: 'json',
                        url: routingUrl,
                        contentType: "application/json; charset=utf-8",
					})
					.done(function( res ) {					
							if(res){
								var responseId;
								if(data.id == '00000000-0000-0000-0000-000000000000') {
									responseId = res;
								}else {
									responseId = data.id;
								}									
								if(pageType=='register'){
									var locationhref= RedirectionUrl+'external?targetRoute='+pageNames[1]+"&id="+ responseId;
								} else if(pageType=='login'){
									var locationhref= RedirectionUrl;
								} else {
									var locationhref= RedirectionUrl+'external?targetRoute='+pageNames[0]+"&id="+ responseId;
								}
								//Redirect  
								window.open(locationhref);
								//jQuery(location).attr('href',locationhref);
							} 					
					});
				}
                
    }
jQuery(document).ready(function(){   
    jQuery('.buyahome, .refinance, .applybutton, .signin, .wmwhyusdetail').click(function(e) {
        e.preventDefault();
        
        var pageType='';
        var LoanPurpose='Purchase';
        if (jQuery(this).hasClass("buyahome")){
            LoanPurpose= 'Purchase'; 
        } else if(jQuery(this).hasClass("refinance")){
            LoanPurpose= 'Refinance'; 
        } else if(jQuery(this).hasClass("signin")){
              pageType='login';
        }else{
            LoanPurpose ='';
            pageType='Apply';
        }
        if(LoanPurpose){
                        var data = { 
        "id": "00000000-0000-0000-0000-000000000000",
        "informationDataType": "leadInfo",
        "informationData": "{'LoanPurpose' : '"+LoanPurpose+"'}"

                        };   
        }else{
                        var data = { 
        "id": "00000000-0000-0000-0000-000000000000",
        "informationDataType": "leadInfo",
                                "informationData":""

                        };				   
        }               
        redirect_To_CD_App(data,pageType);
    });
});
    
    jQuery(document).ajaxComplete(function () {
        jQuery('.wmreadytostart').click(function() {
               var pageType='';
               var LoanPurpose='Purchase';

                   var data = { 
                        "id": "00000000-0000-0000-0000-000000000000",
                        "informationDataType": 'leadInfo',
                        "informationData": "{'LoanPurpose' : '"+LoanPurpose+"'}"

                  };
                       redirect_To_CD_App(data,pageType);            
       });
    });    
	
(function ( $ ) {
 
    $.fn.bootcomplete = function(options) {
        
        var defaults = {
            url : "/search.php",
            method : 'get',
            wrapperClass : "bc-wrapper",
            menuClass : "bc-menu",
            idField : true,
            idFieldName : $(this).attr('name')+"_id",
            minLength : 3,
            dataParams : {},
            formParams : {}
        }
        
        var settings = $.extend( {}, defaults, options );
        
        $(this).attr('autocomplete','off')
        $(this).wrap('<div class="'+settings.wrapperClass+'"></div>')
        if (settings.idField) {
            if ($(this).parent().parent().find('input[name="' + settings.idFieldName + '"]').length !== 0) {
                //use existing id field
            } else {
                //there is no existing id field so create one
                $('<input type="hidden" name="' + settings.idFieldName + '" value="">').insertBefore($(this))
            }
        }
        $('<div class="'+settings.menuClass+' list-group"></div>').insertAfter($(this))
        
        $(this).on("keyup", searchQuery);
        $(this).on("focusout", hideThat)

        var xhr;
        var that = $(this)

        function hideThat() {
            if ($('.list-group-item' + ':hover').length) {
                return;
            }
            $(that).next('.' + settings.menuClass).hide();
        }
        
        function searchQuery(){
            
            var arr = [];
            $.each(settings.formParams,function(k,v){
                arr[k]=$(v).val()
            })
            var dyFormParams = $.extend({}, arr );
            var Data = $.extend({query: $(this).val()}, settings.dataParams, dyFormParams);
            
            if(!Data.query){
                $(this).next('.'+settings.menuClass).html('')    
                $(this).next('.'+settings.menuClass).hide()    
            }
            
            if(Data.query.length >= settings.minLength){
                
                if(xhr && xhr.readyState != 4){
                    xhr.abort();
                }
                
                xhr = $.ajax({
                    type: settings.method,
                    url: settings.url,
                    data: Data,
                    dataType: "json",
                    success: function( json ) {
                        var results = '';
						var start = 1;
						if(json) {
							$.each( json, function(i, j) {
								if(start==1) $("#first_section").val(j.id);
								start++;
								results += '<a href="'+j.in_link+'" class="list-group-item" data-id="'+j.id+'" data-label="'+j.label+'">'+j.label+'</a>'
							});
							$(that).next('.'+settings.menuClass).html(results);	
						} else $(that).next('.'+settings.menuClass).html("<span class='list-group-item'>No results found.</span>");                        
                        //$(that).next('.'+settings.menuClass).children().on("click", selectResult)
                        $(that).next('.'+settings.menuClass).show()
                   
                    }
                })
            }
        }
        
        function selectResult(){
            $(that).val($(this).data('label'))
            if (settings.idField) {
                if ($(that).parent().parent().find('input[name="' + settings.idFieldName + '"]').length !== 0) {
                    //use existed id field
                    $(that).parent().parent().find('input[name="' + settings.idFieldName + '"]').val($(this).data('id'));
					//ensure we trigger the onchange so we can do stuff
					$(that).parent().parent().find('input[name="' + settings.idFieldName + '"]').trigger('change');
                }
                else {
                    //use created id field
                    $(that).prev('input[name="' + settings.idFieldName + '"]').val($(this).data('id'));
					//ensure we trigger the onchange so we can do stuff
					$(that).prev('input[name="' + settings.idFieldName + '"]').trigger('change');
                }
            }
            $(that).next('.' + settings.menuClass).hide();
            return false;
        }

        return this;
    };
 
}( jQuery ));
jQuery(document).ready(function() {
	jQuery('#header_search_term').bootcomplete({
			url:'/global_search',
			minLength : 2,
	});
        
// navigate to respective 
/*     
var hash = location.hash.substr(1);
var arr = window.location.pathname.split('/');
var newArray = arr.filter(function(v){return v!==''});
if(hash != "" && newArray.length == 0 && isNaN(hash)) {                                                    
                                jQuery('window').animate({
                                                scrollTop: jQuery('#'+hash).offset().top - 100
                                }, 'slow');
}*/
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
	jQuery("body").css("cursor","pointer");
} else {
	jQuery("body").css("cursor","");
}
});
$( window ).load(function(e) {  
	e.preventDefault();
	var hash = location.hash.substr(1);
	var arr = window.location.pathname.split('/');
	var newArray = arr.filter(function(v){return v!==''});
	if(hash != "" && newArray.length == 0 && isNaN(hash)) {
		var displayBlock = '#' + hash; 
		setTimeout(function() {
			$('html, body').animate({
				'scrollTop' : $(displayBlock).position().top-71
			});
		},2100);
	}
});



