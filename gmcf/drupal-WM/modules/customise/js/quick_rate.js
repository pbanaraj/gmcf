jQuery(window).unload(function(){
 var url = window.location.pathname;
 var tplPage = url.includes("tp_pre_qual");
 var tplThankPage = url.includes("tp_pre_qual_thank");
 if(tplPage != true && tplThankPage != true) {
	localStorage.clear();
 }
});
function removeCurrencyFormat(newVal){
	if(newVal){
		newVal = newVal.replace(/,/g, '');
		return newVal;
	}else
		return parseInt(0);
	
		
}
function addCurrencyFormat(n){				
	return n.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");		
}
(function ($) {   
   'use strict';
   $(document).on('change','.sortRates',function(e){
	 var key_val = $(this).val();
     var rates = JSON.parse($("#rates_data").val());
	 switch(key_val) {
		case 'LMP':
					rates.sort(function(a, b) {
						return parseFloat(a.MonthlyPayment) - parseFloat(b.MonthlyPayment);
					});
					break;
		case 'LR':
					rates.sort(function(a, b) {
						return parseFloat(a.Rate) - parseFloat(b.Rate);
					});
					break;
		case 'LF':
					rates.sort(function(a, b) {
						return parseFloat(a.TotalFees) - parseFloat(b.TotalFees);
					});
					break;
	 }
	 $('[data-toggle="tooltip"]').tooltip();	
         $('#quick_rate_result').html("<div style='margin:0 auto; text-align:center; width:100%;'><img src='/themes/basis/images/bx_loader.gif' /></div>");
	 $.post("/quick_rate_sort",{  "rates": rates,'type_sort': key_val},function(data) {
			$('#quick_rate_result').html(data);
			jQuery('.qrq_accordion').each(function () {
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
			jQuery('[data-toggle="tooltip"]').tooltip();
		});
	 });
   });
   
	$(document).on('keyup','.currency',function(event){
		
		if(event.which != 37 && event.which != 39 && event.which != 0 && event.which != 9){
			var val = $(this).val();
			val = val.replace(/[^0-9]/g,'');			
			while (/(\d+)(\d{3})/.test(val.toString())){
				val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
			}
      
			$(this).val(val);			
		}			
	});
	

			 $.validator.addMethod('qrq_lessThan', function(value, element, param) {
				var i = parseInt(removeCurrencyFormat(value));
				var j = parseInt(removeCurrencyFormat($(param).val()));
				return i < j;
			}, "Less Than");
			$.validator.addMethod('qrq_greaterThan', function(value, element) {
				var i = parseInt(removeCurrencyFormat(value));
				return i > 0;
			}, "Greater Than");
			
			$.validator.addMethod('checkZipCode', function (value, element){
                  
			//  var ip = location.host;
			//  if(ip == "serenity.fgmcportals.com") {
			//	var zip_url = drupalSettings.basis.loanx.zip_url;
			 // } else {
				var zip_url = drupalSettings.basis.quick_rate.zip_url;
			  //}
              
              var return_val = false;
			  if(value.length == 5) {
				  $.ajax({
					  url: zip_url+value,
					  type: 'GET',
					  dataType:"json",                  
					  async: false,
					  success : function(response) {
                                                           $(".qrq_city").val(response.city);
                                                             $(".qrq_state").val(response.state);
                                                           
                                                           $('#tpl_zipcode_hidden').val(JSON.stringify(response));     
                                                           if(response.length > 0){
								  return_val = false;
							   }
							   else{
								   return_val = true;
							   }
							}
					  });
				}
                  return return_val;
            });
			$.validator.addMethod('checkZipCodeService', function (value, element){
                  
             // var ip = location.host;
			//  if(ip == "serenity.fgmcportals.com") {
			//	var zip_url = "http://r2d2.fgmcportals.com:5100/api/v1/state/";
			 // } else {
				var zip_url = drupalSettings.basis.quick_rate.zip_url;
			 // }
              var return_val = false;
			  if(value.length == 5) {
				  $.ajax({
					  url: zip_url+value+'/serviced',
					  type: 'GET',
					  dataType:"json",                  
					  async: false,
					  success : function(response) {
							   if(response){
								  return_val = true;
							   }
							   else{
								   return_val = false;
							   }
							}
					  });
				}
                  return return_val;
            });
			jQuery.validator.addMethod("phoneUS", function(phone_number, element) {
				phone_number = phone_number.replace(/\s+/g, "");
				return this.optional(element) || phone_number.length > 9 && 
				phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
			}, "Please enter a valid phone number.");
			
			$.validator.addMethod(
				'email',function(value, element){
							return this.optional(element) || /(^[-!#$%&'*+/=?^_`{}|~0-9A-Z]+(\.[-!#$%&'*+/=?^_`{}|~0-9A-Z]+)*|^"([\001-\010\013\014\016-\037!#-\[\]-\177]|\\[\001-\011\013\014\016-\177])*")@((?:[A-Z0-9](?:[A-Z0-9-]{0,61}[A-Z0-9])?\.)+(?:[A-Z]{2,6}\.?|[A-Z0-9-]{2,}\.?)$)|\[(25[0-5]|2[0-4]\d|[0-1]?\d?\d)(\.(25[0-5]|2[0-4]\d|[0-1]?\d?\d)){3}\]$/i.test(value);
				},
				'Please enter a valid email address.'
			);
			$.validator.addMethod("checkEmail", function(value, element) {

			   var return_val = true;			   
			   var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				if (!filter.test(value)) {
					return_val = false;
					return false;
				}
				return return_val;
			});
			var type_form = $("#type_form").val();
			if(type_form == 'home') var err_req = true; else err_req = false;
			
	            $('#quick_rate_form').validate({
	               errorElement: "div",
	               errorClass: "error",
				   onfocusout: function(e) {
						this.element(e);
					},
				   ignore: [],
	               rules: {	                  
	                   "qrq_purchase_price": {required : true,number:true,qrq_greaterThan:true},
	                   "qrq_down_payment": {required : true,number:true,qrq_greaterThan:true,
							qrq_lessThan: '#qrq_purchase_price'},
	                   "qrq_zipcode": {
	                       required: true,number:true,qrq_greaterThan:true,
						   checkZipCode:true,
						   checkZipCodeService:true,
	                       minlength:5						   
	                   },
					   "qrq_credit_score": {required: true},
					   "qrq_loan_product_hidden": {required: true},
					   "qrq_customer_firstname": {required: err_req},
                                            "qrq_customer_lastname": {required: err_req},
	                   "qrq_customer_phone": {required: err_req,phoneUS:true},
					   "qrq_customer_email": {required: err_req,email:true}
	
	               },
	               messages: {
					   "qrq_purchase_price": {required : "Please enter the purchase price of the property you would like to purchase.",qrq_greaterThan:"Amount should be greater than zero."},
	                   "qrq_down_payment": {required : "Please enter the down payment amount.",qrq_greaterThan:"Amount should be greater than zero.",
							qrq_lessThan: "The down payment amount cannot be equal to or more than the purchase price."},
	                   "qrq_zipcode": {
	                       required: "Please enter zip code",qrq_greaterThan:"Zipcode should be greater than zero.",
						   checkZipCode:"Please enter a valid zipcode.",
						   checkZipCodeService:"Sorry! We are not currently licensed in this state. Please check back with us. We hope to be there soon.",
	                       minimum:"Please enter a valid zip code."
	                   },
					   "qrq_credit_score": {required: "Please select your credit score range."},
					   "qrq_loan_product_hidden": {required: "please select a loan product."},
					   "qrq_customer_firstname": {required: "Please enter your first name."},
                                           "qrq_customer_lastname": {required: "Please enter your last name."},
	                   "qrq_customer_phone": {required: "Please enter your phone number."},
					   "qrq_customer_email": {required: "Please enter your email.",email:"Please enter a valid email address."} 
	               },
	               submitHandler: function(form) {  
                           if ($(form).valid()) 
                               form.submit(); 
                           $("#show_quote_submit").prop( "disabled", true );			
                           $("#show_quote_submit").text( "Processing...");	
                           return false; // prevent normal form posting
                    }
	           });
			   	 $('#quick_rate_ref_form').validate({
	               errorElement: "div",
	               errorClass: "error",
				   ignore: [],
				   onfocusout: function(e) {
						this.element(e);
					},
	               rules: {	                  
	                   "qrq_home_value": {required : true,number:true,qrq_greaterThan:true},
	                   "qrq_current_balance": {required : true,number:true,qrq_greaterThan:true,
							qrq_lessThan: '#qrq_home_value'},
	                   "qrq_zipcode": {
	                       required: true,number:true,qrq_greaterThan:true,
						   checkZipCode:true,
						   checkZipCodeService:true,
	                       minlength:5	                      
	                   },
					   "qrq_credit_score": {required: true},
					   "qrq_loan_product_hidden": {required: true},
					   "qrq_customer_firstname": {required: err_req},
                                            "qrq_customer_lastname": {required: err_req},
	                   "qrq_customer_phone": {required: err_req,phoneUS:true},
					   "qrq_customer_email": {required: err_req,email:true},
					   /*"qrq_cash_out" : {qrq_greaterThan:true}*/
	
	               },
	               messages: {
					   "qrq_home_value": {required : "Please enter the estimated property value.",qrq_greaterThan:"Amount should be greater than zero."},
	                   "qrq_current_balance": {required : "Please enter your current mortgage balance.",qrq_greaterThan:"Amount should be greater than zero.",
							qrq_lessThan: "Current balance must be less than Home Value."},
	                   "qrq_zipcode": {
	                       required: "Please enter a valid zip code.",qrq_greaterThan:"Zip code should be greater than zero.",
						   checkZipCode:"Please enter a valid zip code.",
						   checkZipCodeService:"Sorry! We are not currently licensed in this state. Please check back with us. We hope to be there soon.",
	                       minimum:"Please enter a valid zip code."	                      
	                   },
					   "qrq_credit_score": {required: "Please select your credit score range."},
					   "qrq_loan_product_hidden": {required: "please select a loan product."},
					   "qrq_customer_firstname": {required: "Please enter your first name."},
                                           "qrq_customer_lastname": {required: "Please enter your last name."},
	                   "qrq_customer_phone": {required: "Please enter your phone number."},
					   "qrq_customer_email": {required: "Please enter your email.",email:"Please enter a valid email address."},
					   /*"qrq_cash_out" : {qrq_greaterThan:"Amount should be greater than zero."}*/
	               },
	              submitHandler: function(form) {  
                           if ($(form).valid()) 
                           
                               form.submit(); 
                               
                           $("#show_quote_submit").prop( "disabled", true );			
                           $("#show_quote_submit").text( "Processing...");
                           return false; // prevent normal form posting
                    } 
	           });

			   $("#qrq_customer_phone").mask("(999) 99?9-9999",{placeholder:" "});
				/*
				$("#qrq_customer_phone").on("blur", function() {
					var last = $(this).val().substr( $(this).val().indexOf("-") + 1 );
					
					if( last.length == 3 ) {
						var move = $(this).val().substr( $(this).val().indexOf("-") - 1, 1 );
						var lastfour = move + last;
						
						var first = $(this).val().substr( 0, 9 );
						
						$(this).val( first + '-' + lastfour );
					}
				});*/
				$("#qrq_customer_phone_ref").mask("(999) 99?9-9999",{placeholder:" "});

				/*
				$("#qrq_customer_phone_ref").on("blur", function() {
					var last = $(this).val().substr( $(this).val().indexOf("-") + 1 );
					
					if( last.length == 3 ) {
						var move = $(this).val().substr( $(this).val().indexOf("-") - 1, 1 );
						var lastfour = move + last;
						
						var first = $(this).val().substr( 0, 9 );
						
						$(this).val( first + '-' + lastfour );
					}
				});	*/
				$('#quick_rate_form .select_loan_products').multiselect({
					nonSelectedText:"Loan Product",
					includeSelectAllOption: true,
					selectAllText: 'Select All',
					onChange: function(option, checked, select) {
						var data=[];
						var $el=$('#quick_rate_form .select_loan_products');
						$el.find('option:selected').each(function(){
							data.push($(this).val());
						});
						
						if(data.length > 0) {
							$('#quick_rate_form #qrq_loan_product_hidden-error').hide();
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').val('1');	
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').valid();
							$('#quick_rate_form .multiSelect .btn-group .btn-default').removeClass('error');
						} else {
							$('#quick_rate_form #qrq_loan_product_hidden-error').html("Please select your loan product.").show();
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').val('');
							$('#quick_rate_form .multiSelect .btn-group .btn-default').addClass('error');
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').valid();
						}
					},
					onSelectAll: function() {
						var data=[];
						var $el=$('#quick_rate_form .select_loan_products');
						$el.find('option:selected').each(function(){
							data.push($(this).val());
						});
						if(data.length > 0) {
							$('#quick_rate_form #qrq_loan_product_hidden-error').hide();
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').val('1');
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').valid();
							$('#quick_rate_form .multiSelect .btn-group .btn-default').removeClass('error');
						} else {
							$('#quick_rate_form #qrq_loan_product_hidden-error').html("Please select your loan product.").show();
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').val('');
							$('#quick_rate_form .multiSelect .btn-group .btn-default').addClass('error');
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').valid();							
						}
					}
				});
				$('#quick_rate_ref_form .select_loan_products').multiselect({
					nonSelectedText:"Loan Product",
					includeSelectAllOption: true,
					selectAllText: 'Select All',
					onChange: function(option, checked, select) {
						var data=[];
						var $el=$('#quick_rate_ref_form .select_loan_products');
						$el.find('option:selected').each(function(){
							data.push($(this).val());
						});
						if(data.length > 0) {
							$('#quick_rate_ref_form #qrq_loan_product_hidden-error').hide();
							$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').val('1');	
							$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').valid();
							$('#quick_rate_ref_form .multiSelect .btn-group .btn-default').removeClass('error');
						} else {
							$('#quick_rate_ref_form #qrq_loan_product_hidden-error').html("Please select your loan product.").show();
							$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').val('');
							$('#quick_rate_ref_form .multiSelect .btn-group .btn-default').addClass('error');
							$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').valid();
						}
					},
					onSelectAll: function() {
						var data=[];
						var $el=$('#quick_rate_ref_form .select_loan_products');
						$el.find('option:selected').each(function(){
							data.push($(this).val());
						});
						if(data.length > 0) {
							$('#quick_rate_ref_form #qrq_loan_product_hidden-error').hide();
							$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').val('1');
							$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').valid();
							$('#quick_rate_ref_form .multiSelect .btn-group .btn-default').removeClass('error');
						} else {
							$('#quick_rate_ref_form #qrq_loan_product_hidden-error').html("Please select your loan product.").show();
							$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').val('');
							$('#quick_rate_ref_form .multiSelect .btn-group .btn-default').addClass('error');
							$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').valid();
						}
					}
				});
				$(document).on("click","#show_quote_submit",function() {
						var data=[];
						var $el=$('#quick_rate_form .select_loan_products');
						$el.find('option:selected').each(function(){
							data.push($(this).val());
						});
						if(data.length > 0) {
							$('#quick_rate_form .multiSelect .btn-group .btn-default').removeClass('error');
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').val('1');
						} else {
							$('#quick_rate_form .multiSelect .btn-group .btn-default').addClass('error');
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').val('');
						}
				});
				$(document).on("click","#show_quote_ref_submit",function() {
						var data=[];
						var $el=$('#quick_rate_ref_form .select_loan_products');
						$el.find('option:selected').each(function(){
							data.push($(this).val());
						});
						if(data.length > 0) {
							$('#quick_rate_ref_form .multiSelect .btn-group .btn-default').removeClass('error');
							$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').val('1');
						} else {
							$('#quick_rate_ref_form .multiSelect .btn-group .btn-default').addClass('error');
							$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').val('');
						}
				});
				if($("#loan_products_arr").val() && $("#loan_products_arr").val() != "null" ) {
					var loan_product_json = JSON.parse($("#loan_products_arr").val()); 
					$("#qrq_submit .select_loan_products").multiselect('select',loan_product_json);
				}
				$('#quick_rate_form .multiselect').on('blur', function(){
						var data=[];
						var $el=$('#quick_rate_form .select_loan_products');
						$el.find('option:selected').each(function(){
							data.push($(this).val());
						});
						if(data.length > 0) {
							$('#quick_rate_form #qrq_loan_product_hidden-error').hide();
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').val('1');
							$('#quick_rate_form .multiSelect .btn-group .btn-default').removeClass('error');
						} else {						
							$('#quick_rate_form #qrq_loan_product_hidden-error').html("Please select your loan product.").show();
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').val('');
							$('#quick_rate_form .multiSelect .btn-group .btn-default').addClass('error');
							$('#quick_rate_form input[name="qrq_loan_product_hidden"]').valid();
						}
				});
				$('#quick_rate_ref_form .multiselect').on('blur', function(){
					var data=[];
						var $el=$('#quick_rate_ref_form .select_loan_products');
						$el.find('option:selected').each(function(){
							data.push($(this).val());
					});
					if(data.length > 0) {
						$('#quick_rate_ref_form #qrq_loan_product_hidden-error').hide();
						$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').val('1');
						$('#quick_rate_ref_form .multiSelect .btn-group .btn-default').removeClass('error');
					} else {
						$('#quick_rate_ref_form #qrq_loan_product_hidden-error').html("Please select your loan product.").show();
						$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').val('');
						$('#quick_rate_ref_form .multiSelect .btn-group .btn-default').addClass('error');
						$('#quick_rate_ref_form input[name="qrq_loan_product_hidden"]').valid();	
					}
				});
                                $('#landing_pre_qual_form').validate({
                                        errorElement: "div",
                                        errorClass: "error",
                                                    onfocusout: function(e) {
                                                                 this.element(e);
                                                         },
                                                    ignore: [],
                                        rules: {
                                            "tpl_how_help": {required: true},
                                             "tpl_zipcode": {
                                                required: true,
												number:true,
												qrq_greaterThan:true,
                                                checkZipCode:true,
                                                checkZipCodeService:true,
                                                minlength:5						   
                                            },
                                        },
                                        messages: {
                                            "tpl_how_help": {required: "Please select your home goal."},
                                            "tpl_zipcode": {
                                                required: "Please enter zip code",
												qrq_greaterThan:"Please enter the zip code greater than zero.",
                                                checkZipCode:"Please enter a valid zipcode.",
                                                checkZipCodeService:"Sorry, the location entered by you is currently not serviced by us. We will be there soon.",
                                                minimum:"Please enter a valid zip code."
                                            },
                                        },
                                        submitHandler: function(form) {
                                            $('.nav-tabs > .active').next('li').find('a').trigger('click');
                                            return false;
                                        }
                                });
                                $('#landing_pre_qual_2_form').validate({
                                        errorElement: "div",
                                        errorClass: "error",
                                                    onfocusout: function(e) {
                                                                 this.element(e);
                                                         },
                                        errorPlacement: function(error, element) {
                                            if (element.attr("name") == "tpl_purchase_price" ) {
                                                
                                              error.insertAfter($("#tpl_purchase_price").closest(".input-group"));
                                              
                                            } else if (element.attr("name") == "tpl_down_pay") {
                                                
                                              error.insertAfter($("#tpl_down_pay").closest(".input-group"));
                                            
                                            } else if (element.attr("name") == "tpl_down_pay_int") {
                                                
                                              error.insertAfter($("#tpl_down_pay_int").closest(".input-group"));
                                            
                                            } else if (element.attr("name") == "tpl_current_mortgage") {
                                                
                                              error.insertAfter($("#tpl_current_mortgage").closest(".input-group"));
                                              
                                            } else if (element.attr("name") == "tpl_cashout") {
                                                
                                              error.insertAfter($("#tpl_cashout").closest(".input-group"));
                                              
                                            } else if (element.attr("name") == "tpl_est_property") {
                                                
                                              error.insertAfter($("#tpl_est_property").closest(".input-group"));
                                              
                                            }
                                            else {
                                                error.insertAfter(element);
                                            }
                                          },
                                        rules: {
                                            "tpl_refinance_purpose": {required: true},
                                            "tpl_purchase_price": {required : true,qrq_greaterThan:true},
                                            "tpl_down_pay": {required : true,qrq_greaterThan:true,
							qrq_lessThan: '#tpl_purchase_price'},
                                             "tpl_down_pay_int": {number:true},
                                             "tpl_cashout": {required : true,qrq_greaterThan:true},
                                             "tpl_est_property": {required : true,qrq_greaterThan:true},
                                             "tpl_current_mortgage": {required : true},
                                             "tpl_desire_pgm": {required : true},
                                             "tpl_credit_score": {required : true},
                                        },
                                        messages: {
                                            "tpl_refinance_purpose": {required: "Please select the purpose of refinance."},
                                            "tpl_purchase_price": {required : "Please enter the purchase price of the property you would like to purchase.",qrq_greaterThan:"Please enter the purchase price greater than zero."},
                                            "tpl_down_pay": {required : "Please enter the down payment amount.",qrq_greaterThan:"Please enter the down payment greater than zero.",
							qrq_lessThan: "The down payment amount cannot be equal to or more than the purchase price."},
                                            "tpl_down_pay_int": {required:"Please enter the valid number"},
                                            "tpl_cashout": {required : "Please enter the cash out amount",qrq_greaterThan:"Amount should be greater than zero"},
                                            "tpl_est_property": {required : "Please enter the estimated property value",qrq_greaterThan:"Amount should be greater than zero"},
                                            "tpl_current_mortgage": {required : "Please enter your current mortgage balance"},
                                            "tpl_desire_pgm": {required : "please select a loan product"},
                                            "tpl_credit_score": {required : "Please select your credit score range"},
                                        },
                                        submitHandler: function(form) { 
                                            $('.nav-tabs > .active').next('li').find('a').trigger('click');
                                            return false;
                                        }
                                });
                                $("#tpl_customer_mobile").mask("(999) 999-9999",{placeholder:" "});
                                jQuery.validator.addMethod("alphabetsOnly", function(value, element) {
                                    return this.optional(element) || /^[a-z ]+$/i.test(value);
                                }, "Alphabets only"); 
                                $('#landing_pre_qual_3_form').validate({
                                        errorElement: "div",
                                        errorClass: "error",
                                        onfocusout: function(e) {
														this.element(e);														
													},
                                                    ignore: [],
                                        rules: {
                                            "tpl_customer_fname": {required : true,alphabetsOnly: true},
                                            "tpl_customer_lname": {required : true,alphabetsOnly: true},
                                             "tpl_customer_mobile": {required : true,phoneUS:true},
                                             "tpl_customer_email": {required : true,email:true},
                                        },
                                        messages: {
                                            "tpl_customer_fname": {required : "Please enter your first name",alphabetsOnly:"Please enter only alphabet letters"},
                                            "tpl_customer_lname": {required : "Please enter your last name",alphabetsOnly:"Please enter only alphabet letters"},
                                            "tpl_customer_mobile": {required : "please enter a valid mobile number",phoneUS:"Please enter a valid mobile number"},
                                            "tpl_customer_email": {required : "Please enter a email address",email:"Please enter a valid email address"},
                                        },
                                        submitHandler: function(form) {
                                            
                                            $('div#tpl_customer_email-error').remove();
                                            var $inputs = $('#landing_pre_qual_form :input');
                                            var values_form_1 = {};
                                            $inputs.each(function() {
                                                values_form_1[this.name] = $(this).val();
                                            });
                                            
                                            var $inputs2 = $('#landing_pre_qual_2_form :input');
                                            var values_form_2 = {};
                                            $inputs2.each(function() {
                                                values_form_2[this.name] = $(this).val();
                                            });
                                            
                                            var $inputs3 = $('#landing_pre_qual_3_form :input');
                                            var values_form_3 = {};
                                            $inputs3.each(function() {
                                                values_form_3[this.name] = $(this).val();
                                            });
                                            var zip_info = JSON.parse(values_form_1['tpl_zipcode_hidden']);
                                            
                                            var source_uri = $(location).attr('search');
                                            var mobile_number = values_form_3['tpl_customer_mobile'];
                                            mobile_number = mobile_number.replace(/[^0-9\.]/g, '');
                                            var refinPurp;
                                            if(values_form_2['tpl_refinance_purpose'] != null){
                                               refinPurp = values_form_2['tpl_refinance_purpose'];
                                            }else{
                                                refinPurp = null;
                                            }
											var cd_api_url = drupalSettings.path.lnx_api_url_preql_leadinfo;
											var purchaseAmount = (values_form_2['tpl_est_property']) ? removeCurrencyFormat(values_form_2['tpl_est_property']) : removeCurrencyFormat(values_form_2['tpl_purchase_price']);
											var cashOut = (values_form_2['tpl_cashout']) ? removeCurrencyFormat(values_form_2['tpl_cashout']) : '';
											var currentMortgage = (values_form_2['tpl_current_mortgage']) ? removeCurrencyFormat(values_form_2['tpl_current_mortgage']) : '';
											var downPay = (values_form_2['tpl_down_pay']) ? removeCurrencyFormat(values_form_2['tpl_down_pay']) : '';
											var source = values_form_3['tpl_source_hidden'];
											var informationData = { 'FirstName': values_form_3['tpl_customer_fname'],'LastName': values_form_3['tpl_customer_lname'],'LoanPurpose':values_form_1['tpl_how_help'],'RefinancePurpose': refinPurp,'CashOutAmount': cashOut,'CurrentMortgageBalance': currentMortgage,'HouseTypeId': values_form_1['tpl_property_type'],'OccupancyTypeId': "PrimaryResidence",'EstimatedPurchaseAmount': purchaseAmount,'DownPayment': downPay,'City': zip_info['city'],'StateCode': zip_info['state'],'ZipCode': zip_info['zip'],'CreditScoreRange': values_form_2['tpl_credit_score'],'IsVaLoanType': false,'LeadMobileNumber': mobile_number,'LeadEmail': values_form_3['tpl_customer_email'],'CouponType': "1",'CouponCode': "12345",'OtherMarketingInfo': "",'Source':source,'pushToLoanX':false,'requestedProducts':values_form_2['tpl_desire_pgm'],'status':"Assigned"};
                                            var loanx_data = { "informationDataType": "leadInfo",
											                   "id": "00000000-0000-0000-0000-000000000000",
															   "informationData": JSON.stringify(informationData)};
                                            $.ajax({
                                                    url: cd_api_url,
                                                    dataType : "json",
                                                    contentType: "application/json; charset=utf-8",
                                                    method:'POST',
                                                    data: JSON.stringify(loanx_data),
													success: function(data) {
											                        $('.tab-inner-content').html('<img id="tpl-loader-img" alt="Processing..." src="/themes/basis/images/bx_loader.gif" align="center" />');
														localStorage.setItem("leadid_tpl_prequal",data);
														sessionStorage.setItem("leadid_tpl_prequal",data);
														$(location).attr('href', drupalSettings.path.baseUrl+'tp_pre_qual_thank'+source_uri+'&cname='+values_form_3['tpl_customer_fname']);    
													},
                                                                                                        error: function(data) {  
                                                                                                            if(data.responseText == 'Invalid domain' || data.responseText == 'Invalid account' || data.responseText == 'Invalid pattern') {
																												$('#landing_pre_qual_3_form #tpl_customer_email').addClass('error');
																												$('#tpl_customer_email').after('<div id="tpl_customer_email-server-error" class="error">Please enter a valid email address</div>');
																												return true;
																											}
                                                                                                        }
                                                                                                        
                                            });
											return false;
                                            
                                            
                                        }
                                });
                                $('.btnPrevious').click(function (event) {
                                    $('.nav-tabs > .active').prev('li').find('a').trigger('click');
                                    event.preventDefault();
                                });
                                $('#tpl_customer_email').on("keyup",function () {                                    
                                    $('div#tpl_customer_email-server-error').remove();                                    
                                });
                                var hidden_inputs_refin = 0;
                                var hidden_inputs_purch = 0;
                                $('a[href="#tab1"]').on('click', function(event) {
                                    $('.nav-tabs li:nth-child(1)').removeClass( "valid" );
                                    $('.nav-tabs li:nth-child(2)').removeClass( "valid" );
                                });
                                $('.tpl_tab_2').on('click', function(event) {
                                    $('.nav-tabs li:nth-child(2)').removeClass( "valid" );
                                    if($('#landing_pre_qual_form').valid()){
                                       $('.welcome-placehlder h3').empty(); 
                                       $('.nav-tabs li:nth-child(1)').addClass( "valid" );
                                       var $inputs = $('#landing_pre_qual_form :input');
                                       var how_help;
                                       $inputs.each(function() {
                                           if(this.name === 'tpl_how_help') {
                                               how_help = $(this).val();
                                           }
                                       });
                                       if(how_help == "purchase") {
                                           if(hidden_inputs_purch != 1) {
                                                var purch =   $('#landing_pre_qual_2_form').find('.refin-input').length;
                                                if(purch) {
                                                    $('.cd-inputs .refin-input').remove();
                                                }
                                                var purappendhtml = ''; 
                                                purappendhtml+= '<div class="purch-input"><div class="form-group"><label>Purchase Price</label><div class="input-group"><span class="input-group-addon">$</span> <input class="form-control currency" id="tpl_purchase_price" name="tpl_purchase_price" placeholder="Purchase Price" required="" type="text" maxlength="11" autocomplete="off"/></div></div><div class="form-group"><label>Down Payment</label><div class="row"><div class="col-lg-7"><div class="input-group"><span class="input-group-addon">$</span><input class="form-control currency" id="tpl_down_pay" name="tpl_down_pay" placeholder="Down Payment" required="" type="text" maxlength="11" autocomplete="off"/></div></div><div class="col-lg-5"><div class="input-group"><input class="form-control globalPercentCut" id="tpl_down_pay_int" name="tpl_down_pay_int" type="text" maxlength="6"/><span class="input-group-addon">%</span></div></div></div></div></div>';
                                                $('#landing_pre_qual_2_form .cd-inputs').prepend(purappendhtml);
                                                hidden_inputs_purch = 1;
                                                hidden_inputs_refin = 0;
                                            } 
                                        }else if(how_help == "refinance") {
                                            if(hidden_inputs_refin != 1) {
                                                var refin =   $('#landing_pre_qual_2_form').find('.purch-input').length;
                                                if(refin) {
                                                    $('.cd-inputs .purch-input').remove();
                                                }
                                                var appendhtml = '';
                                                appendhtml+= '<div class="refin-input"><div class="form-group"><label>Purpose of refinance</label> <div class="row"><div class="col-xs-10"><select class="form-control" name="tpl_refinance_purpose" id="tpl_refinance_purpose"><option value>Select</option><option value="2">Cashout</option><option value="4">Payoff debts</option><option value="8">Change rate</option></select></div><div class="col-xs-2 refinance-helpicon"><a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="By using payoff debts, you can pay off your high interest debts using this option" data-original-title="By using payoff debts, you can pay off your high interest debts using this option"><img src="'+drupalSettings.path.baseUrl+'themes/basis/images/info.png" alt=""></a></div></div></div>\n\
                                                <div class="form-group refin-cash" style="display:none"><label>Cashout Amount</label><div class="input-group"><span class="input-group-addon">$</span> <input class="form-control currency" id="tpl_cashout" name="tpl_cashout" placeholder="Cash out amount" required="" type="text" maxlength="11" autocomplete="off"/></div></div><div class="form-group"><label>Estimated value of property</label> <div class="input-group"><span class="input-group-addon">$</span><input class="form-control currency" id="tpl_est_property" name="tpl_est_property" placeholder="Estimated property value" required="" type="text" maxlength="11" autocomplete="off"/></div></div><div class="form-group"><label>Current Mortgage Balance</label><div class="input-group"><span class="input-group-addon">$</span> <input class="form-control currency" id="tpl_current_mortgage" name="tpl_current_mortgage" placeholder="Current mortgage balance" required="" type="text" maxlength="11" autocomplete="off"/><div></div></div>';
                                                $('#landing_pre_qual_2_form .cd-inputs').prepend(appendhtml);
                                                hidden_inputs_refin = 1;
                                                hidden_inputs_purch = 0;
                                            } 
                                       }
                                       $('#tpl_refinance_purpose').on('change',function () {
                                            if($(this).val() == 2 ) {
                                                $('.cd-inputs .refin-cash').show();
                                            }else {
                                                $('.cd-inputs .refin-cash').hide();
                                            }    
                                        });
                                        $("#tpl_down_pay").change(function(){
                                            var purchase = removeCurrencyFormat($('#tpl_purchase_price').val());
                                            var downpay = removeCurrencyFormat($('#tpl_down_pay').val());
											
                                            var round_val = (parseInt(downpay) / parseInt(purchase)) * 100;
											
                                            if(!$('#tpl_purchase_price').val()) {
                                                $("#tpl_down_pay_int").val();
                                            }else {
                                                if(round_val > 99.999){
                                                    $("#tpl_down_pay_int").val(99.999);
                                                }else{
                                                    var digitLength = round_val.toString().length;
                                                    if(digitLength > 6) {
                                                      var digitFirst = round_val.toFixed(3);
                                                      $("#tpl_down_pay_int").val(digitFirst);
                                                    }else {
                                                      $("#tpl_down_pay_int").val(round_val);
                                                    }
                                                }
                                            }
                                            
                                        });
                                        $("#tpl_down_pay_int").change(function(){
                                            var purchase = parseInt(removeCurrencyFormat($('#tpl_purchase_price').val()));
                                            var dp_int;
                                            dp_int = $('#tpl_down_pay_int').val();
                                            if($('#tpl_down_pay_int').val() > 99.999){ 
                                                dp_int = $('#tpl_down_pay_int').val(99.999);
                                            }else {
                                                dp_int = $('#tpl_down_pay_int').val();
                                            }
                                            var round_val = dp_int * purchase /100;
                                            if(!$('#tpl_purchase_price').val()) {
                                                $("#tpl_down_pay").val();
                                            }else if($('#tpl_purchase_price').val().length != 0) {
                                                if($.isNumeric(round_val)) {
                                                   var noDecimal = round_val.toFixed(0);
                                                   $("#tpl_down_pay").val(addCurrencyFormat(noDecimal) );
                                                }else {
                                                   $("#tpl_down_pay").val();
                                                }
                                            }
                                           
                                             $("#tpl_down_pay").trigger('blur');
                                        });                                   
                                        $(function($) {
                                            // Only allow values up to 99.999
                                            $('.globalPercentCut').autoNumeric('init', {  vMax: '99.999' });    
                                        });
										
										$( "#tpl_purchase_price" ).change(function() {
									  	  if($("#tpl_down_pay_int").val().length != 0 && $("#tpl_down_pay").val().length != 0) {
											$("#tpl_down_pay_int").val('');
											$("#tpl_down_pay").val('');
										  }
									    });

                                       event.preventDefault();
                                    }else{
                                        return false;
                                    }
                                });
                                $('.tpl_tab_3').on('click', function(event) {
                                    if(($('#landing_pre_qual_2_form').valid()) && ($('#landing_pre_qual_form').valid()) && ($('.nav-tabs li:nth-child(1)').hasClass('valid'))){
                                           $('.nav-tabs li:nth-child(2)').addClass( "valid" );
                                        var source_uri = $(location).attr('search');
                                        
                                        if (source_uri.indexOf("mail-offer") >= 0) {
                                            $('.mail-offer').show();
                                        }else {
                                            $('.mail-offer').hide();
                                        }
                                         
                                       event.preventDefault();
                                    }else{
                                       return false;
                                    }
                                });

									

                                $('#tpl_purchase_price').on('paste', function(e) {
                                    e.preventDefault();
                                });
								
								// setting values based on calculator pre filled values
								if(window.localStorage.getItem("HM_default_value"))
										var default_HM = window.localStorage.getItem("HM_default_value");
									else if(window.sessionStorage.getItem("HM_default_value"))
										var default_HM = window.sessionStorage.getItem("HM_default_value");									
										
								if(window.localStorage.getItem("DP_default_value"))
										var default_DP = window.localStorage.getItem("DP_default_value");
									else if(window.sessionStorage.getItem("DP_default_value"))
										var default_DP = window.sessionStorage.getItem("DP_default_value");									
										
								if(window.localStorage.getItem("LT_default_value"))
										var default_LT = window.localStorage.getItem("LT_default_value");
									else if(window.sessionStorage.getItem("LT_default_value"))
										var default_LT = window.sessionStorage.getItem("LT_default_value");
									
								if(window.localStorage.getItem("CMB_default_value"))
										var default_CMB = window.localStorage.getItem("CMB_default_value");
									else if(window.sessionStorage.getItem("CMB_default_value"))
										var default_CMB = window.sessionStorage.getItem("CMB_default_value");
									
								if(window.localStorage.getItem("LTIM_default_value"))
										var default_LT = Math.round(window.localStorage.getItem("LTIM_default_value")/12);
									else if(window.sessionStorage.getItem("LTIM_default_value"))
										var default_LT = Math.round(window.sessionStorage.getItem("LTIM_default_value")/12);
									
								if(window.localStorage.getItem("CNM_default_value"))
										var default_CNM = window.localStorage.getItem("CNM_default_value");
									else if(window.sessionStorage.getItem("CNM_default_value"))
										var default_CNM = window.sessionStorage.getItem("CNM_default_value");
								
								if($("#loanTermsConfig").val()) {
									var loan_terms_json = JSON.parse($("#loanTermsConfig").val()); 
									if (default_LT in loan_terms_json) default_LT = String(default_LT)+" year fixed"; else default_LT = "";
								} else {									
									default_LT = String(default_LT)+" year fixed";
								}
								
								$("#quick_rate_landing_default #qrq_purchase_price").val(default_HM);
								$("#quick_rate_landing_default #qrq_down_payment").val(default_DP);
								$("#quick_rate_landing_default #qrq_current_balance").val(default_CMB);
								$("#quick_rate_landing_default #quick_rate_form #qrq_customer_name,#quick_rate_landing_default #quick_rate_ref_form #qrq_customer_name").val(default_CNM);
								//if(default_LT !="") $("#quick_rate_landing_default #quick_rate_form .select_loan_products").multiselect('select',[default_LT]);
								//if(default_LT !="") $("#quick_rate_landing_default #quick_rate_ref_form .select_loan_products").multiselect('select',[default_LT]);								
								$("#quick_rate_landing_default #qrq_home_value").val(default_HM);								
								if(window.location.hash.substr(1) == "refinance") {
									$('#quick-rates ul.nav-tabs li').removeClass("active");
									$('#quick-rates div.tab-content div').removeClass("active");
									$('#quick-rates div.tab-content div').removeClass("in");
									$('a[href^="#refinance"]').parent().addClass('active');
									$('#quick-rates #refinance').addClass("active");
									$('#quick-rates #refinance').addClass("in");
								}


 //$(this).closest('tr').find('td:eq(1)').text();
	//apply start
       $('.select-rate').click(function() {
            var credit=$(this).parent('td').attr('credit');
            var monthlypayment=$(this).parent('td').attr('monthlypayment');
            var apr=$(this).parent('td').attr('apr');
            var points=$(this).parent('td').attr('points');
            var rate=$(this).parent('td').attr('rate');
            var totalfees=$(this).parent('td').attr('totalfees');
            var products={"30 Year Fixed":1,"20 Year Fixed":2,"25 Year Fixed":5,"15 Year Fixed":3,"10 Year Fixed":4, "10/1 LIBOR ARM":11,"7/1 LIBOR ARM":12,"5/1 LIBOR ARM":13,"3/1 LIBOR ARM":14};
            //if unsure products["unsure"]=7;
            var ProductName=7;
            var proddesc=$('div.open h4').text();
            var length = products.length;
            $.each( products, function( key, value ) {
            if(key==proddesc){
                ProductName=value;
                }
            }); 
			// get property & occupency types from form
			/*var active_tab = $("#quick-rates ul.nav-tabs li.active a").attr("href");
			var occup_id = active_tab+" #qrq_occupency_type";
			var prop_id = active_tab+" #qrq_property_type";
			var qrq_property_type="SingleFamily";var qrq_occupency_type = "PrimaryResidence";
			if($(occup_id).val()) var qrq_property_type = $(occup_id).val(); 
			if($(prop_id).val()) var qrq_occupency_type = $(prop_id).val();*/

                if(window.localStorage.getItem("leadId"))
					var LeadId = window.localStorage.getItem("leadId");
                else if(window.sessionStorage.getItem("leadId"))
                    var LeadId = window.sessionStorage.getItem("leadId");
                else
                    var LeadId = '';
				
				
				
           if(LeadId !="f1dc76dc-92ae-4b36-8682-891570de1c0e" && LeadId){			   
               var pagetype='register';
			   LeadId = LeadId;
			   
					/*
                     var data = {
                                  "id": LeadId,
                                  "informationDataType": "LeadInterestRate",
                                  "informationData" : JSON.stringify(LeadInfo)
                     }*/

                     
           } else {
               var pagetype='prod';
			   LeadId = "00000000-0000-0000-0000-000000000000";
                     // var data = {
                          // id: "00000000-0000-0000-0000-000000000000",
                          // informationDataType: "LeadInterestRate",
                          // informationData: "{'Rate': {'Apr':"+apr+",'InterestRate':"+rate+",'MonthlyPayment':"+monthlypayment+",'Fees':"+totalfees+",'ProductName':'"+ProductName+"','Points':"+points+",'Credit':"+credit+"},'ProductCategory' :''}"

                     // }                  
           }
		   
		   var LeadInfo = {
				   "SessionId": LeadId,
				   "Rate": { 
							"Apr": apr,
							"InterestRate": rate,
							"MonthlyPayment": monthlypayment,
							"Fees": totalfees,
							"ProductName": ProductName,
							"Points": points,
							"Credit": credit
						}				
			   }
			   var data = {
                                  "id": LeadId,
                                  "informationDataType": "LeadInterestRate",
                                  "informationData" : JSON.stringify(LeadInfo)
                     }

           
                   redirect_To_CD_App(data,pagetype);
	 });      
         //apply end  

	// arm discliamer pop up js
	$('.arm-disclaimer').click(function() {
		var arm_rateperc = $(this).data('rate').trim();
		var arm_aprperc = $(this).data('apr');
		var arm_points = $(this).data('points');
		var term_name = $(this).data('product').trim();
		var I = (arm_rateperc * 0.01) / 12;
		var I_apr = (arm_aprperc * 0.01) / 12;
		var arm_term_arr = term_name.split("/");
		var arm_term = arm_term_arr[0];
		var arm_rem_term = parseInt(30-arm_term);
		var T = parseInt(arm_term*12);
		var P = 200000;
		var H = 250000;
		var pmt_val = format_money(round_decimals(PMT(I,30*12,P),2));
		var balance = getBalance(P,arm_rateperc,30,arm_term*12);
		var rem_pmt_val = format_money(round_decimals(PMT(I_apr,arm_rem_term*12,balance),2));
		var ltv_perc = Math.round((P/H)*100);
		var update_html = 'The initial payment on a 30-year $'+H+' '+arm_term+'-year Adjustable-Rate Loan at '+arm_rateperc+'% and '+ltv_perc+'% loan-to-value (LTV) is $'+pmt_val+' with '+arm_points+' points due at closing. The Annual Percentage Rate (APR) is '+arm_aprperc+'%. After the initial '+arm_term+' years, the principal and interest payment is $'+rem_pmt_val+'. The fully indexed rate of '+arm_aprperc+'% is in effect for the remaining '+arm_rem_term+' years and can change once every year for the remaining life of the loan. Payment does not include taxes and insurance premiums. The actual payment amount will be greater. Rate is variable and subject to change after '+arm_term+' years.';
		jQuery("#myModal_discliamers .modal-body").html(update_html);
	});

})(jQuery);

function isNumber(evt) {
	evt = (evt) ? evt : window.event;
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}
	return true;
}
function onlyAlphabets(e) {
                if (window.event) {
                    var charCode = window.event.keyCode;
                }
                else if (e) {
                    var charCode = e.which;
                }
                else { return true; }
                if ((charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123) || charCode == 39 || charCode == 45 || charCode == 8 || charCode == 127 || charCode == 32 || charCode == 9 || charCode == 37 || charCode == 39 )
                    return true;
                else
                    return false;
}
// arm discliamer pop up js
function PMT(ir,np,pv){
    var q;
    q=Math.pow (1+ir,np);
    return((ir*(0+q*pv))/(-1+q));
}
function format_money(n) {
	return n.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
}
function round_decimals(original_number, decimals) {
	var result1 = original_number * Math.pow(10, decimals)
	var result2 = Math.round(result1)
	var result3 = result2 / Math.pow(10, decimals)
	return (result3)
}
function getBalance(P,I,L,num_of_pay_made){

			var P = eval(Math.floor(P))/1.00;  // P principle I interest rate L length of years
			var I = eval(I);
			var L = eval(L); // L number of years
			var t = num_of_pay_made; // t Number of payments made 

			var J = I/(12 * 100); 
			var N = (L * 12);

			   var i = I/(12 * 100);
			   var pow = 1;
				for (var j = 0; j < N; j++){
					pow = pow * (1 + i);
				}
				 var monthly_payment = (P * pow * i) / (pow - 1);

			var interest_rate = (I / 100.0);
			var term_remaining = N - t;
			var loan_balance = calculate_balance(monthly_payment, interest_rate / 12, term_remaining);

			return loan_balance;

}

function calculate_balance(PMT, IR, NP) {
		  var PV = PMT * (1 - Math.pow(1 + IR, -NP)) / IR
		  return round_decimals(PV, 2)
}
// end arm discliamer pop up js