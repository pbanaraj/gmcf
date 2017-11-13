(function ($) {   
   $(document).ready(function() {
	   $.ajax({
			method: "POST",
			dataType: 'json',
			url: "ajax/whyus_testimonials/left",
		})
		.done(function( res ) {					
			if(res){	
				$.each( res, function( key, value ) {
				  $(value.selector).html(value.data);
				});
				 $('#testimonial1').bxSlider({
					mode: 'horizontal',
					auto: true,
					autoControls: false,
					pause: 2000,
					controls: false,
					speed:1000
				});
				$('#testimonial2').bxSlider({
					mode: 'fade',
					auto: true,
					autoControls: false,
					pause: 6000,
					controls: false,
					speed:2000
				});
				$('#testimonial3').bxSlider({
					mode: 'fade',
					auto: true,
					autoControls: false,
					pause: 6000,
					controls: false,
					speed:2000
				});
				$('#testimonial4').bxSlider({
					mode: 'vertical',
					auto: true,
					autoControls: false,
					pause: 3000,
					controls: false,
					speed:1000
				});
				$('.bxslider').bxSlider({
				minSlides: 1,
				maxSlides: 2,
				slideWidth: 360,
				slideMargin: 10
				});
			}else{
				alert(res);
			}						
		});
	  
	  /*setTimeout(function() {
	   $('#testimonial1').bxSlider({
					mode: 'horizontal',
					auto: true,
					autoControls: false,
					pause: 2000,
					controls: false,
					speed:1000
				});
				$('#testimonial2').bxSlider({
					mode: 'fade',
					auto: true,
					autoControls: false,
					pause: 6000,
					controls: false,
					speed:2000
				});
				$('#testimonial3').bxSlider({
					mode: 'fade',
					auto: true,
					autoControls: false,
					pause: 6000,
					controls: false,
					speed:2000
				});
				$('#testimonial4').bxSlider({
					mode: 'vertical',
					auto: true,
					autoControls: false,
					pause: 3000,
					controls: false,
					speed:1000
				});
				$('.bxslider').bxSlider({
				minSlides: 1,
				maxSlides: 2,
				slideWidth: 360,
				slideMargin: 10
				});
		},1000);*/

   });
})(jQuery);
