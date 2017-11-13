(function ($) {   
   $(document).ready(function() {


				$('.cascade-slider_nav li:gt(4)').hide();

				$('.prev').click(function() {
					var first = $('.cascade-slider_nav').children('li:visible:first');
					first.prevAll(':lt(5)').css("display", "inline-block");
					first.prev().nextAll().hide()
				});

				$('.next').click(function() {
					var last = $('.cascade-slider_nav').children('li:visible:last');
					last.nextAll(':lt(5)').css("display", "inline-block");
					last.next().prevAll().hide();
				});
				$('#cascade-slider').cascadeSlider({
			
				});
				//apply start
				$('.wmproduct').click(function() {
						var prodcatTitle = $('.now .slide .header h4').text();
						var prodtype = $('.holder_bu_center .slide .header span.prodtype').text();
								  var data = { 
										"id": "00000000-0000-0000-0000-000000000000",
									"informationDataType": "InterestRate",
									"informationData": "{'MortgageType' : '"+prodtype+"'}"
									
						  };
						redirect_To_CD_App(data,'prod');
				});      
			 //apply end        
			});
})(jQuery);
