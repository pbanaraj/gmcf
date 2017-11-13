(function ($, Drupal,drupalSettings) {   
   'use strict';
   $(document).on('click','.removeContribute',function(e){
     e.preventDefault();
     var redirectlink = $(this).attr('href');
	 if(confirm(" Are you sure you want to delete contribute?")){
		window.location.href=redirectlink;
	 } else {
		return false;
	 }
   });
   
   
   $(document).on('change','#LT_default_value',function(e){
       var defr  =  JSON.parse(defa_rates);
       if(defr[$(this).val()]){
           jQuery("#IR_default_value").val(defr[$(this).val()]);
       }
       else {
           
           jQuery("#IR_default_value").val("");
           
       }
     
   });
   
   $(document).on('click','a.remove_section',function(e){
     e.preventDefault();
     var redirectlink = $(this).attr('href');
	 var sectionkey = $(this).data('key');
	 if(confirm(" Are you sure you want to delete section?")){
		window.location.href=redirectlink+"?key="+sectionkey;
	 } else {
		return false;
	 }
   });
   $(document).on('click','.validate_rates',function(e){
	 $('input[name="' + this.name + '"]').not(this).prop('checked', false);
	 if($('.validate_rates:checked').length > 3) {
		alert("Maximum you can select 3 rates..");return false;
	 }	 
	 var resName = $(this).data('key')+"[key_row]";	 
     $('input[name="' + resName + '"]').val($(this).data('key1'));	 
   });
   $(document).bind('cbox_closed', function () {
	$('#cboxOverlay').remove();
	$('#colorbox').remove();
	});
	$(document).on("keypress",".ins_rate_number",function(event) {
				  if ((event.which != 46 || $(this).val().indexOf('.') != -1) &&
					((event.which < 48 || event.which > 57) &&
					  (event.which != 0 && event.which != 8))) {
					event.preventDefault();
				  }

				  var text = $(this).val();

				  if ((text.indexOf('.') != -1) &&
					(text.substring(text.indexOf('.')).length > 3) &&
					(event.which != 0 && event.which != 8) &&
					($(this)[0].selectionStart >= text.length - 3)) {
					event.preventDefault();
				  }
	});
	Number.prototype.between  = function (a, b, inclusive) {
			var min = Math.min.apply(Math, [a,b]),
				max = Math.max.apply(Math, [a,b]);
			return inclusive ? this >= min && this <= max : this > min && this < max;
	};
	$(document).on("keypress",".per_field",function(e) {
		    isNumberIns(e);
            var v= $(this).val();
			var position = document.getElementById(this.id).selectionStart;
            var b = String.fromCharCode(e.which);
            v = [v.slice(0, position), b, v.slice(position)].join('');
            v = parseFloat(v );
			var textValue = parseFloat(v).toString();
			var newVal = parseInt( textValue.substr(0, textValue.indexOf(".")) );					
			if(!parseFloat(v).between(0,99.999,true)) {
				v = this.value.substring(0, 2);
				e.preventDefault();								
				return false;
			} 
			return true;
		});
		$(document).on("change","#LT_default_value",function(e) {
			var desired_mortgage_html = "";
			var term = $(this).val();
			for(var i=1; i <= term; i++){
				if($("#DLM_default_value1").val() > term) {
					if (term == i) var selected_dlt = "selected";	else var selected_dlt = "";
				} else {
					if ($("#DLM_default_value1").val() == i) var selected_dlt = "selected";	else var selected_dlt = "";
				}
				desired_mortgage_html = desired_mortgage_html+"<option value='"+i+"' "+selected_dlt+">"+i+"</option>";
			}
			$("#DLM_default_value").html(desired_mortgage_html);
		});
			 $.validator.addMethod('max_lessThan', function(value, element, param) {
				var i = parseInt(value);
				var j = parseInt($(param).val()*0.97);
				return i <= j;
			}, "Less Than");
			$.validator.addMethod('max_lessThan1', function(value, element, param) {
				var i = parseInt(value);
				var j = parseInt($(param).val()*0.10);
				return i <= j;
			}, "Less Than");
			$.validator.addMethod('min_greaterThan', function(value, element, param) {
				var i = parseInt(value);
				var j = parseInt($(param).val()*0.03);
				return i >= j;
			}, "Less Than");
			$.validator.addMethod('greaterThan', function(value, element, param) {
				var i = parseInt(value);
				var j = parseInt($(param).val());
				return i >= j;
			}, "Greater Than");
			$.validator.addMethod('lessThan', function(value, element, param) {
				var i = parseInt(value);
				var j = parseInt($(param).val());
				return i <= j;
			}, "Less Than");
			$.validator.addMethod('greaterThan1', function(value, element, param) {
				var i = parseInt(value);
				var j = parseInt($(param).val());
				return i > j;
			}, "Greater Than");
			$.validator.addMethod('lessThan1', function(value, element, param) {
				var i = parseInt(value);
				var j = parseInt($(param).val());
				return i < j;
			}, "Greater Than");
			$.validator.addMethod('maxV', function(value, element, param) {
				var i = parseInt(value);
				var j = parseInt($(param).val()*12);
				return i <= j;
			}, "Greater Than");
			$.validator.addMethod('maxVY', function(value, element, param) {
				var i = parseInt(value);
				var j = parseInt($(param).val());
				return i <= j;
			}, "Greater Than");
			
			$.validator.addMethod('greaterThan_IR', function(value, element, param) {
				var i = parseFloat(value);
				var j = parseFloat($(param).val());				
				return i >= j;
			}, "Greater Than1");
			$.validator.addMethod('lessThan_IR', function(value, element, param) {
				var i = parseFloat(value);
				var j = parseFloat($(param).val());	
				return i <= j;
			}, "Less Than1");
			$.validator.addMethod('max_lessThan2', function(value, element, param) {
				var i = parseInt(value);
				var j = parseInt($(param).val()*0.99999);
				return i < j;
			}, "Less Than");
			
			$('#calc_config_form').validate({
	               errorElement: "div",
	               errorClass: "error",
				   ignore: [],
				   onfocusout: function(e) {
						this.element(e);
					},
	               rules: {
					   "HM_min_value": {required : true,max: 999999999},
					   "HM_max_value": {required : true,max: 999999999,greaterThan1: '#HM_min_value'},
					   "HM_default_value": {required : true,greaterThan: '#HM_min_value',lessThan: '#HM_max_value'},
					   "LA_min_value": {required : true,max_lessThan: '#HM_max_value'},
					   "LA_max_value": {required : true,max_lessThan: '#HM_max_value',greaterThan1: '#LA_min_value'},					   
					   "LA_default_value": {required : true,greaterThan: '#LA_min_value',lessThan: '#LA_max_value'},
					   "DP_min_value": {required : true,min_greaterThan: '#HM_default_value'},
					   "DP_max_value": {required : true,max_lessThan2: '#HM_max_value',greaterThan1: '#DP_min_value'},
					   "DP_default_value" : {required : true,greaterThan: '#DP_min_value',lessThan: '#DP_max_value'},
					   "DP_per_min_value": {required : true,min:3,max:99.999},
					   "DP_per_max_value": {required : true,min:3,max:99.999,greaterThan1: '#DP_per_min_value'},
					   "DP_per_default_value" : {required : true,greaterThan: '#DP_per_min_value',lessThan: '#DP_per_max_value'},
					   "API_min_value" : {required : true,max:99.999},
					   "API_max_value" : {required : true,greaterThan1: '#API_min_value',max: 99.999},
					   "API_default_value" : {required : true,greaterThan_IR: '#API_min_value',lessThan_IR: '#API_max_value'},
					   "APT_min_value" : {required : true,max:99.999},
					   "APT_max_value" : {required : true,greaterThan1: '#APT_min_value',max: 99.999},
					   "APT_default_value" : {required : true,greaterThan_IR: '#APT_min_value',lessThan_IR: '#APT_max_value'},
					   "PR_min_value": {required : true,max: 999999999},
					   "PR_max_value": {required : true,max: 999999999,greaterThan1: '#PR_min_value'},
					   "PR_default_value": {required : true,greaterThan: '#PR_min_value',lessThan: '#PR_max_value'},
					   "IR_min_value" : {required : true,max:99.999},
					   "IR_max_value" : {required : true,greaterThan1: '#IR_min_value',max: 99.999},
					   "IR_default_value" : {required : true,greaterThan_IR: '#IR_min_value',lessThan_IR: '#IR_max_value'},
					   "NPM_min_value" : {required : true,maxV:'#LT_default_value'},
					   "NPM_max_value" : {required : true,greaterThan1: '#NPM_min_value',maxV: '#LT_default_value'},
					   "NPM_default_value" : {required : true,greaterThan: '#NPM_min_value',lessThan: '#NPM_max_value'},
					   "RT_default_value" : {required : true,maxVY: '#LT_default_value'},
					   "EXP_default_value" : {required : true,lessThan1: '#LA_max_value'},
					   "GI_min_value": {required : true,max: 999999999},
					   "GI_max_value": {required : true,max: 999999999,greaterThan1: '#GI_min_value'},
					   "GI_default_value": {required : true,greaterThan: '#GI_min_value',lessThan: '#GI_max_value'},
					   "CMB_min_value": {required : true,max: 999999999},
					   "CMB_max_value": {required : true,max: 999999999,greaterThan1: '#CMB_min_value'},
					   "CMB_default_value": {required : true,greaterThan: '#CMB_min_value',lessThan: '#CMB_max_value'},
					   "CMB2_min_value": {required : true,max: 999999999},
					   "CMB2_max_value": {required : true,max: 999999999,greaterThan1: '#CMB2_min_value'},
					   "HOA_min_value" : {required : true,lessThan1: '#LA_max_value'},
					   "HOA_max_value" : {required : true,lessThan1: '#LA_max_value',greaterThan1: '#HOA_min_value'},
					   "HOA_default_value" : {required : true,greaterThan: '#HOA_min_value',lessThan: '#HOA_max_value'},
					   "HA_API_min_value" : {required : true,max:10},
					   "HA_API_max_value" : {required : true,greaterThan1: '#HA_API_min_value',max: 10},
					   "HA_API_default_value" : {required : true,greaterThan: '#HA_API_min_value',lessThan: '#HA_API_max_value'},
					   "HA_APT_min_value" : {required : true,max:10},
					   "HA_APT_max_value" : {required : true,greaterThan1: '#HA_APT_min_value',max: 10},
					   "HA_APT_default_value" : {required : true,greaterThan: '#HA_APT_min_value',lessThan: '#HA_APT_max_value'},
					   "RTM_default_value" : {required : true,maxV: '#LT_default_value'},
					   "MR_min_value" : {required : true,min:1,max: 999999999},
					   "MR_max_value" : {required : true,min:1,max: 999999999,greaterThan1: '#MR_min_value'},
					   "MR_default_value" : {required : true,greaterThan: '#MR_min_value',lessThan: '#MR_max_value'},
					   "AHA_min_value" : {required : true,min:0,max: 99},
					   "AHA_max_value" : {required : true,greaterThan1: '#AHA_min_value',min:0,max: 99},
					   "AHA_default_value" : {required : true,greaterThan: '#AHA_min_value',lessThan: '#AHA_max_value'},
					   "ME_min_value": {required : true,lessThan1: '#HM_max_value'},
					   "ME_max_value": {required : true,lessThan1: '#HM_max_value',greaterThan1: '#ME_min_value'},
					   "ME_default_value": {required : true,greaterThan: '#ME_min_value',lessThan: '#ME_max_value'},
					   "OLB_min_value": {required : true,max: 999999999},
					   "OLB_max_value": {required : true,max: 999999999,greaterThan1: '#OLB_min_value'},
					   "OLB_default_value": {required : true,greaterThan: '#OLB_min_value',lessThan: '#OLB_max_value'},
					   "LTIM_min_value" : {required : true,maxV:'#LT_default_value'},
					   "LTIM_max_value" : {required : true,greaterThan1: '#LTIM_min_value',maxV: '#LT_default_value'},
					   "LTIM_default_value" : {required : true,greaterThan: '#LTIM_min_value',lessThan: '#LTIM_max_value'},
					   "DLM_default_value" : {required : true,min:1,maxVY: '#LT_default_value'},
					   "YPTS_min_value" : {required : true,maxV:'#LT_default_value'},
					   "YPTS_max_value" : {required : true,greaterThan1: '#YPTS_min_value',maxV: '#LT_default_value'},
					   "YPTS_default_value" : {required : true,greaterThan: '#YPTS_min_value',lessThan: '#YPTS_max_value'},	
					   "CFIT_min_value" : {required : true,min:0,max: 99},
					   "CFIT_max_value" : {required : true,greaterThan1: '#CFIT_min_value',min:0,max: 99},
					   "CFIT_default_value" : {required : true,greaterThan: '#CFIT_min_value',lessThan: '#CFIT_max_value'},
					   "CSIT_min_value" : {required : true,min:0,max: 99},
					   "CSIT_max_value" : {required : true,greaterThan1: '#CSIT_min_value',min:0,max: 99},
					   "CSIT_default_value" : {required : true,greaterThan: '#CSIT_min_value',lessThan: '#CSIT_max_value'},
					   "RRDP_min_value" : {required : true,min:0,max: 99},
					   "RRDP_max_value" : {required : true,greaterThan1: '#RRDP_min_value',min:0,max: 99},
					   "RRDP_default_value" : {required : true,greaterThan: '#RRDP_min_value',lessThan: '#RRDP_max_value'},
					   "CCA_min_value" : {required : true,min:0,max: 99},
					   "CCA_max_value" : {required : true,greaterThan1: '#CCA_min_value',min:0,max: 99},
					   "CCA_default_value" : {required : true,greaterThan: '#CCA_min_value',lessThan: '#CCA_max_value'},
					   "PMI_min_value" : {required : true,min:0,max: 1},
					   "PMI_max_value" : {required : true,greaterThan1: '#PMI_min_value',min:0,max: 1},
					   "PMI_default_value" : {required : true,greaterThan: '#PMI_min_value',lessThan: '#PMI_max_value'},
					   "YBSP_min_value" : {required : true,maxVY:'#LT_default_value'},
					   "YBSP_max_value" : {required : true,greaterThan1: '#YBSP_min_value',maxVY: '#LT_default_value'},
					   "YBSP_default_value" : {required : true,greaterThan: '#YBSP_min_value',lessThan: '#YBSP_max_value'},
					   "DISPSL_min_value" : {required : true,min:0,max: 99},
					   "DISPSL_max_value" : {required : true,greaterThan1: '#DISPSL_min_value',min:0,max: 99},
					   "DISPSL_default_value" : {required : true,greaterThan: '#DISPSL_min_value',lessThan: '#DISPSL_max_value'},
					   "DISPSM_min_value" : {required : true,min:0,max: 99},
					   "DISPSM_max_value" : {required : true,greaterThan1: '#DISPSM_min_value',min:0,max: 99},
					   "DISPSM_default_value" : {required : true,greaterThan: '#DISPSM_min_value',lessThan: '#DISPSM_max_value'},
					   "LNCS_min_value" : {required : true,lessThan1: '#LA_max_value'},
					   "LNCS_max_value" : {required : true,lessThan1: '#LA_max_value',greaterThan1: '#LNCS_min_value'},
					   "LNCS_default_value" : {required : true,greaterThan: '#LNCS_min_value',lessThan: '#LNCS_max_value'},
	               },
	               messages: {					   
					   "HM_min_value": {required : "Please provide a valid value.",max:"The home value should not be more than 999999999."},
					   "HM_max_value": {required : "Please provide a valid value.",max:"The home value should not be more than 999999999.",greaterThan1: "The maximum value should be more than min value"},
					   "HM_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "LA_min_value": {required : "Please provide a valid value.",max_lessThan: "Max value cannot be more than 97% of home value"},
					   "LA_max_value": {required : "Please provide a valid value.",max_lessThan: "Max value cannot be more than 97% of home value",greaterThan1: "The maximum value should be more than min value"},
					   "LA_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "DP_min_value": {required : "Please provide a valid value.",min_greaterThan: "The minimum value cannot be less than 3% of home value"},
					   "DP_max_value": {required : "Please provide a valid value.",max_lessThan2: "The maximum value cannot be more than 99.999% of home value",greaterThan1: "The maximum value should be more than min value"},
					   "DP_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "DP_per_min_value": {required : "Please provide a valid value.",min:"The minimum value cannot be less then 3%",max: "The maximum value cannot be more then 99.999%"},
					   "DP_per_max_value": {required : "Please provide a valid value.",min:"The minimum value cannot be less then 3%", max: "The maximum value cannot be more then 99.999%",greaterThan1: "The maximum value should be more than min value"},
					   "DP_per_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "API_min_value" : {required : "Please provide a valid value.",max:"Max value should not be more than 99.999%"},
					   "API_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",max: "Max value should not be more than 99.999%"},
					   "API_default_value" : {required : "Please provide a valid value.",greaterThan_IR: "Default value cannot be less than min value",lessThan_IR: "Default value cannot be more than max value"},
					   "APT_min_value" : {required : "Please provide a valid value.",max:"Max value should not be more than 99.999%"},
					   "APT_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",max: "Max value should not be more than 99.999%"},
					   "APT_default_value" : {required : "Please provide a valid value.",greaterThan_IR: "Default value cannot be less than min value",lessThan_IR: "Default value cannot be more than max value"},
					   "PR_min_value": {required : "Please provide a valid value.",max:"The Principal should not be more than 999999999."},
					   "PR_max_value": {required : "Please provide a valid value.",max:"The Principal should not be more than 999999999.",greaterThan1: "The maximum value should be more than min value"},
					   "PR_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "IR_min_value" : {required : "Please provide a valid value.",max:"Max value should not be more than 99.999%"},
					   "IR_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",max: "Max value should not be more than 99.999%"},
					   "IR_default_value" : {required : "Please provide a valid value.",greaterThan_IR: "Default value cannot be less than min value",lessThan_IR: "Default value cannot be more than max value"},
					   "NPM_min_value" : {required : "Please provide a valid value.",maxV:"Max value should not be more than loan term"},
					   "NPM_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",maxV: "Max value should not be more than loan term"},
					   "NPM_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "RT_default_value" : {required : "Please provide a valid value.",maxVY:"Max value should not be more than loan term"},
					   "EXP_default_value" : {required : "Please provide a valid value.",lessThan1: "Extra Principal cannot be more than loan amount"},
					   "GI_min_value": {required : "Please provide a valid value.",max:"The gross annual income should not be more than 999999999."},
					   "GI_max_value": {required : "Please provide a valid value.",max:"The gross annual income should not be more than 999999999.",greaterThan1: "The maximum value should be more than min value"},
					   "GI_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "CMB_min_value": {required : "Please provide a valid value.",max:"The first mortgage current balance should not be more than 999999999."},
					   "CMB_max_value": {required : "Please provide a valid value.",max:"The first mortgage current balance should not be more than 999999999.",greaterThan1: "The maximum value should be more than min value"},
					   "CMB_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "CMB2_min_value": {required : "Please provide a valid value.",max:"The second mortgage current balance should not be more than 999999999."},
					   "CMB2_max_value": {required : "Please provide a valid value.",max:"The second mortgage current balance should not be more than 999999999.",greaterThan1: "The maximum value should be more than min value"},
					   "HOA_min_value" : {required : "Please provide a valid value.",lessThan1: "HOA cannot be more than loan amount"},
					   "HOA_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",lessThan1: "HOA cannot be more than loan amount"},
					   "HOA_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "HA_API_min_value" : {required : "Please provide a valid value.",max:"Max value should not be more than 2%"},
					   "HA_API_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",max: "Max value should not be more than 2%"},
					   "HA_API_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "HA_APT_min_value" : {required : "Please provide a valid value.",max:"Max value should not be more than 2%"},
					   "HA_APT_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",max: "Max value should not be more than 2%"},
					   "HA_APT_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "RTM_default_value" : {required : "Please provide a valid value.",maxV:"Max value should not be more than loan term"},					   
					   "MR_min_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 999999999."},
					   "MR_max_value": {required : "Please provide a valid value.",max:"The monthly rental should not be more than 999999999.",greaterThan1: "The maximum value should be more than min value"},
					   "MR_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "AHA_min_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99."},
					   "AHA_max_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99.",greaterThan1: "The maximum value should be more than min value"},
					   "AHA_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "ME_min_value": {required : "Please provide a valid value.",lessThan1: "Extra Principal cannot be more than home value"},
					   "ME_max_value": {required : "Please provide a valid value.",lessThan1: "Extra Principal cannot be more than loan amount",greaterThan1: "The maximum value should be more than min value"},
					   "ME_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},					   
					   "OLB_min_value": {required : "Please provide a valid value.",max:"The original loan balance should not be more than 999999999."},
					   "OLB_max_value": {required : "Please provide a valid value.",max:"The original loan balance should not be more than 999999999.",greaterThan1: "The maximum value should be more than min value"},
					   "OLB_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "LTIM_min_value" : {required : "Please provide a valid value.",maxV:"Max value should not be more than loan term"},
					   "LTIM_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",maxV: "Max value should not be more than loan term"},
					   "LTIM_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "DLM_default_value" : {required : "Please provide a valid value.",maxVY:"Max value should not be more than loan term"},	
					   "YPTS_min_value" : {required : "Please provide a valid value.",maxV:"Max value should not be more than loan term"},
					   "YPTS_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",maxV: "Max value should not be more than loan term"},
					   "YPTS_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
	                   "CFIT_min_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99."},
					   "CFIT_max_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99.",greaterThan1: "The maximum value should be more than min value"},
					   "CFIT_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "CSIT_min_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99."},
					   "CSIT_max_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99.",greaterThan1: "The maximum value should be more than min value"},
					   "CSIT_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "RRDP_min_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99."},
					   "RRDP_max_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99.",greaterThan1: "The maximum value should be more than min value"},
					   "RRDP_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "CCA_min_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99."},
					   "CCA_max_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99.",greaterThan1: "The maximum value should be more than min value"},
					   "CCA_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "PMI_min_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 1."},
					   "PMI_max_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 1.",greaterThan1: "The maximum value should be more than min value"},
					   "PMI_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "YBSP_min_value" : {required : "Please provide a valid value.",maxVY:"Max value should not be more than loan term"},
					   "YBSP_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",maxVY: "Max value should not be more than loan term"},
					   "YBSP_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "DISPSL_min_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99."},
					   "DISPSL_max_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99.",greaterThan1: "The maximum value should be more than min value"},
					   "DISPSL_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "DISPSM_min_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99."},
					   "DISPSM_max_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99.",greaterThan1: "The maximum value should be more than min value"},
					   "DISPSM_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "LNCS_min_value" : {required : "Please provide a valid value.",lessThan1: "Loan cost cannot be more than loan amount"},
					   "LNCS_max_value" : {required : "Please provide a valid value.",greaterThan1: "The maximum value should be more than min value",lessThan1: "Loan cost cannot be more than loan amount"},
					   "LNCS_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
				   },
	              submitHandler: function(form) {  
                           if ($(form).valid()) 
                               form.submit(); 
                           return false; // prevent normal form posting
                    } 
	    });


})(jQuery, Drupal,drupalSettings);

function isNumber(evt) {
	evt = (evt) ? evt : window.event;
	var charCode = (evt.which) ? evt.which : evt.keyCode;
			
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}
	return true;
}
function isNumberIns(evt) {
	evt = (evt) ? evt : window.event;
	var charCode = (evt.which) ? evt.which : evt.keyCode;
			
	if (charCode > 31 && charCode!=46 && (charCode < 48 || charCode > 57)) {
		return false;
	}
	return true;
}
