(function ($) {   
   'use strict';
   $(document).ready(function() {                        
	//apply start
       $('.wmproduct').click(function() {
            var prodcatTitle = $('.holder_bu_center .slide .header h4').text();
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
