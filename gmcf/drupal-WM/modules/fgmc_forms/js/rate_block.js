/*(function($) {
	Drupal.behaviors.rate_block = {
		attach: function (context, settings) {
			$('.show_edit_popup').live('hover', function(){
				var block_title   = $(this).find('.block-title').html();
				var block_slogan  = $(this).find('.block-slogan').html();
				var block_id      = $(this).attr("id");
				alert(block_title+"---"+block_slogan+"-----"+block_id);
			});
			
			
		}
		
	};
})(jQuery);*/
(function ($, Drupal,drupalSettings) {   
   'use strict';
   /*$(document).ready(function(){
	
	
	$( ".edit_form_class" ).submit(function( event ) {
		alert("hiiiiivvvvvvvvvvviiii");
		var desc = $( ".desc-custom-class" ).val();
		var block_id = $('.block-id-custom-class').val();
	  if ( desc ) {
		alert(desc);
	  }else{
		  alert('empty');
	  }
		var base_path = 'http://localhost/sarita/';
		$.ajax({
			method: "POST",
			dataType: "json",
			url: base_path+"rate_block/block_fields",
			data: { block_desc: desc,block_id:block_id}
		})
		.done(function( res ) {					
			if(res){	
				alert(res);
				//d.find('.block-title').html(res.title);
				//d.find('.block-slogan').html(res.body);
				
			}else{
				alert(res);
			}						
		});
	  event.preventDefault();
	});
	   
   });*/
   
   
})(jQuery, Drupal,drupalSettings);
