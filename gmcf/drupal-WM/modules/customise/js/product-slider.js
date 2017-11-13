jQuery(document).ready(function(){
	$('.loader_products').show();
	setTimeout(function() {
		$('.loader_products').hide();
		var maxSlides,
    	width = $(window).width();
		if ((width < 1400) && (width > 991)) {
			maxSlides = 2;
		} 
		else if(width < 991) {
			maxSlides = 1;
		}
		else {
			maxSlides = 3;
		}

		jQuery('.bxslider').show().bxSlider({
							minSlides : 1,
							maxSlides : maxSlides,
							slideWidth : 400,
							slideMargin : 10,
													
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
	},2000);
});