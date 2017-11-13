/* jQuery Validation Plugin - v1.13.0 - 7/1/2014
 * http://jqueryvalidation.org/
 * Copyright (c) 2014 J�rn Zaefferer; Licensed MIT */
!function(b){"function"==typeof define&&define.amd?define(["jquery"],b):b(jQuery)}(function(f){f.extend(f.fn,{validate:function(g){if(!this.length){return void (g&&g.debug&&window.console&&console.warn("Nothing selected, can't validate, returning nothing."))}var a=f.data(this[0],"validator");return a?a:(this.attr("novalidate","novalidate"),a=new f.validator(g,this[0]),f.data(this[0],"validator",a),a.settings.onsubmit&&(this.validateDelegate(":submit","click",function(c){a.settings.submitHandler&&(a.submitButton=c.target),f(c.target).hasClass("cancel")&&(a.cancelSubmit=!0),void 0!==f(c.target).attr("formnovalidate")&&(a.cancelSubmit=!0)}),this.submit(function(h){function c(){var b;return a.settings.submitHandler?(a.submitButton&&(b=f("<input type='hidden'/>").attr("name",a.submitButton.name).val(f(a.submitButton).val()).appendTo(a.currentForm)),a.settings.submitHandler.call(a,a.currentForm,h),a.submitButton&&b.remove(),!1):!0}return a.settings.debug&&h.preventDefault(),a.cancelSubmit?(a.cancelSubmit=!1,c()):a.form()?a.pendingRequest?(a.formSubmitted=!0,!1):c():(a.focusInvalid(),!1)})),a)},valid:function(){var g,a;return f(this[0]).is("form")?g=this.validate().form():(g=!0,a=f(this[0].form).validate(),this.each(function(){g=a.element(this)&&g})),g},removeAttrs:function(g){var h={},a=this;return f.each(g.split(/\s/),function(i,c){h[c]=a.attr(c),a.removeAttr(c)}),h},rules:function(a,k){var n,o,l,m,q,r,p=this[0];if(a){switch(n=f.data(p.form,"validator").settings,o=n.rules,l=f.validator.staticRules(p),a){case"add":f.extend(l,f.validator.normalizeRule(k)),delete l.messages,o[p.name]=l,k.messages&&(n.messages[p.name]=f.extend(n.messages[p.name],k.messages));break;case"remove":return k?(r={},f.each(k.split(/\s/),function(h,g){r[g]=l[g],delete l[g],"required"===g&&f(p).removeAttr("aria-required")}),r):(delete o[p.name],l)}}return m=f.validator.normalizeRules(f.extend({},f.validator.classRules(p),f.validator.attributeRules(p),f.validator.dataRules(p),f.validator.staticRules(p)),p),m.required&&(q=m.required,delete m.required,m=f.extend({required:q},m),f(p).attr("aria-required","true")),m.remote&&(q=m.remote,delete m.remote,m=f.extend(m,{remote:q})),m}}),f.extend(f.expr[":"],{blank:function(a){return !f.trim(""+f(a).val())},filled:function(a){return !!f.trim(""+f(a).val())},unchecked:function(a){return !f(a).prop("checked")}}),f.validator=function(g,a){this.settings=f.extend(!0,{},f.validator.defaults,g),this.currentForm=a,this.init()},f.validator.format=function(g,a){return 1===arguments.length?function(){var b=f.makeArray(arguments);return b.unshift(g),f.validator.format.apply(this,b)}:(arguments.length>2&&a.constructor!==Array&&(a=f.makeArray(arguments).slice(1)),a.constructor!==Array&&(a=[a]),f.each(a,function(h,b){g=g.replace(new RegExp("\\{"+h+"\\}","g"),function(){return b})}),g)},f.extend(f.validator,{defaults:{messages:{},groups:{},rules:{},errorClass:"error",validClass:"valid",errorElement:"label",focusInvalid:!0,errorContainer:f([]),errorLabelContainer:f([]),onsubmit:!0,ignore:":hidden",ignoreTitle:!1,onfocusin:function(b){this.lastActive=b,this.settings.focusCleanup&&!this.blockFocusCleanup&&(this.settings.unhighlight&&this.settings.unhighlight.call(this,b,this.settings.errorClass,this.settings.validClass),this.hideThese(this.errorsFor(b)))},onfocusout:function(b){this.checkable(b)||!(b.name in this.submitted)&&this.optional(b)||this.element(b)},onkeyup:function(g,c){(9!==c.which||""!==this.elementValue(g))&&(g.name in this.submitted||g===this.lastElement)&&this.element(g)},onclick:function(b){b.name in this.submitted?this.element(b):b.parentNode.name in this.submitted&&this.element(b.parentNode)},highlight:function(g,h,a){"radio"===g.type?this.findByName(g.name).addClass(h).removeClass(a):f(g).addClass(h).removeClass(a)},unhighlight:function(g,h,a){"radio"===g.type?this.findByName(g.name).removeClass(h).addClass(a):f(g).removeClass(h).addClass(a)}},setDefaults:function(a){f.extend(f.validator.defaults,a)},messages:{required:"This field is required.",remote:"Please fix this field.",email:"Please enter a valid email address.",url:"Please enter a valid URL.",date:"Please enter a valid date.",dateISO:"Please enter a valid date ( ISO ).",number:"Please enter a valid number.",digits:"Please enter only digits.",creditcard:"Please enter a valid credit card number.",equalTo:"Please enter the same value again.",maxlength:f.validator.format("Please enter no more than {0} characters."),minlength:f.validator.format("Please enter at least {0} characters."),rangelength:f.validator.format("Please enter a value between {0} and {1} characters long."),range:f.validator.format("Please enter a value between {0} and {1}."),max:f.validator.format("Please enter a value less than or equal to {0}."),min:f.validator.format("Please enter a value greater than or equal to {0}.")},autoCreateRanges:!1,prototype:{init:function(){function g(j){var k=f.data(this[0].form,"validator"),i="on"+j.type.replace(/^validate/,""),l=k.settings;l[i]&&!this.is(l.ignore)&&l[i].call(k,this[0],j)}this.labelContainer=f(this.settings.errorLabelContainer),this.errorContext=this.labelContainer.length&&this.labelContainer||f(this.currentForm),this.containers=f(this.settings.errorContainer).add(this.settings.errorLabelContainer),this.submitted={},this.valueCache={},this.pendingRequest=0,this.pending={},this.invalid={},this.reset();var h,a=this.groups={};f.each(this.settings.groups,function(j,i){"string"==typeof i&&(i=i.split(/\s/)),f.each(i,function(k,b){a[b]=j})}),h=this.settings.rules,f.each(h,function(i,c){h[i]=f.validator.normalizeRule(c)}),f(this.currentForm).validateDelegate(":text, [type='password'], [type='file'], select, textarea, [type='number'], [type='search'] ,[type='tel'], [type='url'], [type='email'], [type='datetime'], [type='date'], [type='month'], [type='week'], [type='time'], [type='datetime-local'], [type='range'], [type='color'], [type='radio'], [type='checkbox']","focusin focusout keyup",g).validateDelegate("select, option, [type='radio'], [type='checkbox']","click",g),this.settings.invalidHandler&&f(this.currentForm).bind("invalid-form.validate",this.settings.invalidHandler),f(this.currentForm).find("[required], [data-rule-required], .required").attr("aria-required","true")},form:function(){return this.checkForm(),f.extend(this.submitted,this.errorMap),this.invalid=f.extend({},this.errorMap),this.valid()||f(this.currentForm).triggerHandler("invalid-form",[this]),this.showErrors(),this.valid()},checkForm:function(){this.prepareForm();for(var g=0,c=this.currentElements=this.elements();c[g];g++){this.check(c[g])}return this.valid()},element:function(g){var h=this.clean(g),a=this.validationTargetFor(h),i=!0;return this.lastElement=a,void 0===a?delete this.invalid[h.name]:(this.prepareElement(a),this.currentElements=f(a),i=this.check(a)!==!1,i?delete this.invalid[a.name]:this.invalid[a.name]=!0),f(g).attr("aria-invalid",!i),this.numberOfInvalids()||(this.toHide=this.toHide.add(this.containers)),this.showErrors(),i},showErrors:function(g){if(g){f.extend(this.errorMap,g),this.errorList=[];for(var a in g){this.errorList.push({message:g[a],element:this.findByName(a)[0]})}this.successList=f.grep(this.successList,function(b){return !(b.name in g)})}this.settings.showErrors?this.settings.showErrors.call(this,this.errorMap,this.errorList):this.defaultShowErrors()},resetForm:function(){f.fn.resetForm&&f(this.currentForm).resetForm(),this.submitted={},this.lastElement=null,this.prepareForm(),this.hideErrors(),this.elements().removeClass(this.settings.errorClass).removeData("previousValue").removeAttr("aria-invalid")},numberOfInvalids:function(){return this.objectLength(this.invalid)},objectLength:function(i){var g,h=0;for(g in i){h++}return h},hideErrors:function(){this.hideThese(this.toHide)},hideThese:function(b){b.not(this.containers).text(""),this.addWrapper(b).hide()},valid:function(){return 0===this.size()},size:function(){return this.errorList.length},focusInvalid:function(){if(this.settings.focusInvalid){try{f(this.findLastActive()||this.errorList.length&&this.errorList[0].element||[]).filter(":visible").focus().trigger("focusin")}catch(a){}}},findLastActive:function(){var a=this.lastActive;return a&&1===f.grep(this.errorList,function(b){return b.element.name===a.name}).length&&a},elements:function(){var g=this,a={};return f(this.currentForm).find("input, select, textarea").not(":submit, :reset, :image, [disabled]").not(this.settings.ignore).filter(function(){return !this.name&&g.settings.debug&&window.console&&console.error("%o has no name assigned",this),this.name in a||!g.objectLength(f(this).rules())?!1:(a[this.name]=!0,!0)})},clean:function(a){return f(a)[0]},errors:function(){var a=this.settings.errorClass.split(" ").join(".");return f(this.settings.errorElement+"."+a,this.errorContext)},reset:function(){this.successList=[],this.errorList=[],this.errorMap={},this.toShow=f([]),this.toHide=f([]),this.currentElements=f([])},prepareForm:function(){this.reset(),this.toHide=this.errors().add(this.containers)},prepareElement:function(b){this.reset(),this.toHide=this.errorsFor(b)},elementValue:function(g){var h,a=f(g),i=g.type;return"radio"===i||"checkbox"===i?f("input[name='"+g.name+"']:checked").val():"number"===i&&"undefined"!=typeof g.validity?g.validity.badInput?!1:a.val():(h=a.val(),"string"==typeof h?h.replace(/\r/g,""):h)},check:function(a){a=this.validationTargetFor(this.clean(a));var k,n,o,l=f(a).rules(),m=f.map(l,function(g,c){return c}).length,q=!1,r=this.elementValue(a);for(n in l){o={method:n,parameters:l[n]};try{if(k=f.validator.methods[n].call(this,r,a,o.parameters),"dependency-mismatch"===k&&1===m){q=!0;continue}if(q=!1,"pending"===k){return void (this.toHide=this.toHide.not(this.errorsFor(a)))}if(!k){return this.formatAndAdd(a,o),!1}}catch(p){throw this.settings.debug&&window.console&&console.log("Exception occurred when checking element "+a.id+", check the '"+o.method+"' method.",p),p}}if(!q){return this.objectLength(l)&&this.successList.push(a),!0}},customDataMessage:function(g,a){return f(g).data("msg"+a.charAt(0).toUpperCase()+a.substring(1).toLowerCase())||f(g).data("msg")},customMessage:function(i,g){var h=this.settings.messages[i];return h&&(h.constructor===String?h:h[g])},findDefined:function(){for(var b=0;b<arguments.length;b++){if(void 0!==arguments[b]){return arguments[b]}}return void 0},defaultMessage:function(g,a){return this.findDefined(this.customMessage(g.name,a),this.customDataMessage(g,a),!this.settings.ignoreTitle&&g.title||void 0,f.validator.messages[a],"<strong>Warning: No message defined for "+g.name+"</strong>")},formatAndAdd:function(g,h){var a=this.defaultMessage(g,h.method),i=/\$?\{(\d+)\}/g;"function"==typeof a?a=a.call(this,h.parameters,g):i.test(a)&&(a=f.validator.format(a.replace(i,"{$1}"),h.parameters)),this.errorList.push({message:a,element:g,method:h.method}),this.errorMap[g.name]=a,this.submitted[g.name]=a},addWrapper:function(b){return this.settings.wrapper&&(b=b.add(b.parent(this.settings.wrapper))),b},defaultShowErrors:function(){var i,g,h;for(i=0;this.errorList[i];i++){h=this.errorList[i],this.settings.highlight&&this.settings.highlight.call(this,h.element,this.settings.errorClass,this.settings.validClass),this.showLabel(h.element,h.message)}if(this.errorList.length&&(this.toShow=this.toShow.add(this.containers)),this.settings.success){for(i=0;this.successList[i];i++){this.showLabel(this.successList[i])}}if(this.settings.unhighlight){for(i=0,g=this.validElements();g[i];i++){this.settings.unhighlight.call(this,g[i],this.settings.errorClass,this.settings.validClass)}}this.toHide=this.toHide.not(this.toShow),this.hideErrors(),this.addWrapper(this.toShow).show()},validElements:function(){return this.currentElements.not(this.invalidElements())},invalidElements:function(){return f(this.errorList).map(function(){return this.element})},showLabel:function(a,j){var m,n,k,l=this.errorsFor(a),o=this.idOrName(a),p=f(a).attr("aria-describedby");l.length?(l.removeClass(this.settings.validClass).addClass(this.settings.errorClass),l.html(j)):(l=f("<"+this.settings.errorElement+">").attr("id",o+"-error").addClass(this.settings.errorClass).html(j||""),m=l,this.settings.wrapper&&(m=l.hide().show().wrap("<"+this.settings.wrapper+"/>").parent()),this.labelContainer.length?this.labelContainer.append(m):this.settings.errorPlacement?this.settings.errorPlacement(m,f(a)):m.insertAfter(a),l.is("label")?l.attr("for",o):0===l.parents("label[for='"+o+"']").length&&(k=l.attr("id"),p?p.match(new RegExp("\b"+k+"\b"))||(p+=" "+k):p=k,f(a).attr("aria-describedby",p),n=this.groups[a.name],n&&f.each(this.groups,function(h,g){g===n&&f("[name='"+h+"']",this.currentForm).attr("aria-describedby",l.attr("id"))}))),!j&&this.settings.success&&(l.text(""),"string"==typeof this.settings.success?l.addClass(this.settings.success):this.settings.success(l,a)),this.toShow=this.toShow.add(l)},errorsFor:function(g){var h=this.idOrName(g),a=f(g).attr("aria-describedby"),i="label[for='"+h+"'], label[for='"+h+"'] *";return a&&(i=i+", #"+a.replace(/\s+/g,", #")),this.errors().filter(i)},idOrName:function(b){return this.groups[b.name]||(this.checkable(b)?b.name:b.id||b.name)},validationTargetFor:function(b){return this.checkable(b)&&(b=this.findByName(b.name).not(this.settings.ignore)[0]),b},checkable:function(b){return/radio|checkbox/i.test(b.type)},findByName:function(a){return f(this.currentForm).find("[name='"+a+"']")},getLength:function(g,a){switch(a.nodeName.toLowerCase()){case"select":return f("option:selected",a).length;case"input":if(this.checkable(a)){return this.findByName(a.name).filter(":checked").length}}return g.length},depend:function(g,c){return this.dependTypes[typeof g]?this.dependTypes[typeof g](g,c):!0},dependTypes:{"boolean":function(b){return b},string:function(g,a){return !!f(g,a.form).length},"function":function(g,c){return g(c)}},optional:function(g){var a=this.elementValue(g);return !f.validator.methods.required.call(this,a,g)&&"dependency-mismatch"},startRequest:function(b){this.pending[b.name]||(this.pendingRequest++,this.pending[b.name]=!0)},stopRequest:function(g,a){this.pendingRequest--,this.pendingRequest<0&&(this.pendingRequest=0),delete this.pending[g.name],a&&0===this.pendingRequest&&this.formSubmitted&&this.form()?(f(this.currentForm).submit(),this.formSubmitted=!1):!a&&0===this.pendingRequest&&this.formSubmitted&&(f(this.currentForm).triggerHandler("invalid-form",[this]),this.formSubmitted=!1)},previousValue:function(a){return f.data(a,"previousValue")||f.data(a,"previousValue",{old:null,valid:!0,message:this.defaultMessage(a,"remote")})}},classRuleSettings:{required:{required:!0},email:{email:!0},url:{url:!0},date:{date:!0},dateISO:{dateISO:!0},number:{number:!0},digits:{digits:!0},creditcard:{creditcard:!0}},addClassRules:function(g,a){g.constructor===String?this.classRuleSettings[g]=a:f.extend(this.classRuleSettings,g)},classRules:function(g){var h={},a=f(g).attr("class");return a&&f.each(a.split(" "),function(){this in f.validator.classRuleSettings&&f.extend(h,f.validator.classRuleSettings[this])}),h},attributeRules:function(a){var h,k,l={},i=f(a),j=a.getAttribute("type");for(h in f.validator.methods){"required"===h?(k=a.getAttribute(h),""===k&&(k=!0),k=!!k):k=i.attr(h),/min|max/.test(h)&&(null===j||/number|range|text/.test(j))&&(k=Number(k)),k||0===k?l[h]=k:j===h&&"range"!==j&&(l[h]=!0)}return l.maxlength&&/-1|2147483647|524288/.test(l.maxlength)&&delete l.maxlength,l},dataRules:function(g){var h,a,i={},j=f(g);for(h in f.validator.methods){a=j.data("rule"+h.charAt(0).toUpperCase()+h.substring(1).toLowerCase()),void 0!==a&&(i[h]=a)}return i},staticRules:function(g){var h={},a=f.data(g.form,"validator");return a.settings.rules&&(h=f.validator.normalizeRule(a.settings.rules[g.name])||{}),h},normalizeRules:function(g,a){return f.each(g,function(b,c){if(c===!1){return void delete g[b]}if(c.param||c.depends){var h=!0;switch(typeof c.depends){case"string":h=!!f(c.depends,a.form).length;break;case"function":h=c.depends.call(a,a)}h?g[b]=void 0!==c.param?c.param:!0:delete g[b]}}),f.each(g,function(c,b){g[c]=f.isFunction(b)?b(a):b}),f.each(["minlength","maxlength"],function(){g[this]&&(g[this]=Number(g[this]))}),f.each(["rangelength","range"],function(){var b;g[this]&&(f.isArray(g[this])?g[this]=[Number(g[this][0]),Number(g[this][1])]:"string"==typeof g[this]&&(b=g[this].replace(/[\[\]]/g,"").split(/[\s,]+/),g[this]=[Number(b[0]),Number(b[1])]))}),f.validator.autoCreateRanges&&(g.min&&g.max&&(g.range=[g.min,g.max],delete g.min,delete g.max),g.minlength&&g.maxlength&&(g.rangelength=[g.minlength,g.maxlength],delete g.minlength,delete g.maxlength)),g},normalizeRule:function(g){if("string"==typeof g){var a={};f.each(g.split(/\s/),function(){a[this]=!0}),g=a}return g},addMethod:function(g,h,a){f.validator.methods[g]=h,f.validator.messages[g]=void 0!==a?a:f.validator.messages[g],h.length<3&&f.validator.addClassRules(g,f.validator.normalizeRule(g))},methods:{required:function(g,h,a){if(!this.depend(a,h)){return"dependency-mismatch"}if("select"===h.nodeName.toLowerCase()){var i=f(h).val();return i&&i.length>0}return this.checkable(h)?this.getLength(g,h)>0:f.trim(g).length>0},email:function(g,c){return this.optional(c)||/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(g)},url:function(g,c){return this.optional(c)||/^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(g)},date:function(g,c){return this.optional(c)||!/Invalid|NaN/.test(new Date(g).toString())},dateISO:function(g,c){return this.optional(c)||/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(g)},number:function(g,c){return this.optional(c)||/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(g)},digits:function(g,c){return this.optional(c)||/^\d+$/.test(g)},creditcard:function(j,h){if(this.optional(h)){return"dependency-mismatch"}if(/[^0-9 \-]+/.test(j)){return !1}var i,m,n=0,k=0,l=!1;if(j=j.replace(/\D/g,""),j.length<13||j.length>19){return !1}for(i=j.length-1;i>=0;i--){m=j.charAt(i),k=parseInt(m,10),l&&(k*=2)>9&&(k-=9),n+=k,l=!l}return n%10===0},minlength:function(g,h,a){var i=f.isArray(g)?g.length:this.getLength(f.trim(g),h);return this.optional(h)||i>=a},maxlength:function(g,h,a){var i=f.isArray(g)?g.length:this.getLength(f.trim(g),h);return this.optional(h)||a>=i},rangelength:function(g,h,a){var i=f.isArray(g)?g.length:this.getLength(f.trim(g),h);return this.optional(h)||i>=a[0]&&i<=a[1]},min:function(i,g,h){return this.optional(g)||i>=h},max:function(i,g,h){return this.optional(g)||h>=i},range:function(i,g,h){return this.optional(g)||i>=h[0]&&i<=h[1]},equalTo:function(g,h,a){var i=f(a);return this.settings.onfocusout&&i.unbind(".validate-equalTo").bind("blur.validate-equalTo",function(){f(h).valid()}),g===i.val()},remote:function(a,h,k){if(this.optional(h)){return"dependency-mismatch"}var l,i,j=this.previousValue(h);return this.settings.messages[h.name]||(this.settings.messages[h.name]={}),j.originalMessage=this.settings.messages[h.name].remote,this.settings.messages[h.name].remote=j.message,k="string"==typeof k&&{url:k}||k,j.old===a?j.valid:(j.old=a,l=this,this.startRequest(h),i={},i[h.name]=a,f.ajax(f.extend(!0,{url:k,mode:"abort",port:"validate"+h.name,dataType:"json",data:i,context:l.currentForm,success:function(b){var n,c,g,m=b===!0||"true"===b;l.settings.messages[h.name].remote=j.originalMessage,m?(g=l.formSubmitted,l.prepareElement(h),l.formSubmitted=g,l.successList.push(h),delete l.invalid[h.name],l.showErrors()):(n={},c=b||l.defaultMessage(h,"remote"),n[h.name]=j.message=f.isFunction(c)?c(a):c,l.invalid[h.name]=!0,l.showErrors(n)),j.valid=m,l.stopRequest(h,m)}},k)),"pending")}}}),f.format=function(){throw"$.format has been deprecated. Please use $.validator.format instead."};var d,e={};f.ajaxPrefilter?f.ajaxPrefilter(function(i,g,c){var h=i.port;"abort"===i.mode&&(e[h]&&e[h].abort(),e[h]=c)}):(d=f.ajax,f.ajax=function(a){var b=("mode" in a?a:f.ajaxSettings).mode,c=("port" in a?a:f.ajaxSettings).port;return"abort"===b?(e[c]&&e[c].abort(),e[c]=d.apply(this,arguments),e[c]):d.apply(this,arguments)}),f.extend(f.fn,{validateDelegate:function(g,h,a){return this.bind(h,function(b){var i=f(b.target);return i.is(g)?a.apply(i,arguments):void 0})}})});;
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
if (term == i) var selected_dlt = "selected";else var selected_dlt = "";
} else {
if ($("#DLM_default_value1").val() == i) var selected_dlt = "selected";else var selected_dlt = "";
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
					   "LA_min_value": {required : true,max_lessThan: '#HM_default_value'},
					   "LA_max_value": {required : true,max_lessThan: '#HM_default_value',greaterThan1: '#LA_min_value'},					   
					   "LA_default_value": {required : true,greaterThan: '#LA_min_value',lessThan: '#LA_max_value'},
					   "DP_min_value": {required : true,min_greaterThan: '#HM_default_value'},
					   "DP_max_value": {required : true,max_lessThan1: '#HM_default_value',greaterThan1: '#DP_min_value'},
					   "DP_default_value" : {required : true,greaterThan: '#DP_min_value',lessThan: '#DP_max_value'},
					   "DP_per_min_value": {required : true,min:0,max:10},
					   "DP_per_max_value": {required : true,min:0,max:10,greaterThan1: '#DP_per_min_value'},
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
					   "EXP_default_value" : {required : true,lessThan1: '#LA_default_value'},
					   "GI_min_value": {required : true,max: 999999999},
					   "GI_max_value": {required : true,max: 999999999,greaterThan1: '#GI_min_value'},
					   "GI_default_value": {required : true,greaterThan: '#GI_min_value',lessThan: '#GI_max_value'},
					   "CMB_min_value": {required : true,max: 999999999},
					   "CMB_max_value": {required : true,max: 999999999,greaterThan1: '#CMB_min_value'},
					   "CMB_default_value": {required : true,greaterThan: '#CMB_min_value',lessThan: '#CMB_max_value'},
					   "CMB2_min_value": {required : true,max: 999999999},
					   "CMB2_max_value": {required : true,max: 999999999,greaterThan1: '#CMB2_min_value'},
					   "HOA_min_value" : {required : true,lessThan1: '#LA_default_value'},
					   "HOA_max_value" : {required : true,lessThan1: '#LA_default_value',greaterThan1: '#HOA_min_value'},
					   "HOA_default_value" : {required : true,greaterThan: '#HOA_min_value',lessThan: '#HOA_max_value'},
					   "HA_API_min_value" : {required : true,max:2},
					   "HA_API_max_value" : {required : true,greaterThan1: '#HA_API_min_value',max: 2},
					   "HA_API_default_value" : {required : true,greaterThan: '#HA_API_min_value',lessThan: '#HA_API_max_value'},
					   "HA_APT_min_value" : {required : true,max:2},
					   "HA_APT_max_value" : {required : true,greaterThan1: '#HA_APT_min_value',max: 2},
					   "HA_APT_default_value" : {required : true,greaterThan: '#HA_APT_min_value',lessThan: '#HA_APT_max_value'},
					   "RTM_default_value" : {required : true,maxV: '#LT_default_value'},
					   "MR_min_value" : {required : true,min:1,max: 999999999},
					   "MR_max_value" : {required : true,min:1,max: 999999999,greaterThan1: '#MR_min_value'},
					   "MR_default_value" : {required : true,greaterThan: '#MR_min_value',lessThan: '#MR_max_value'},
					   "AHA_min_value" : {required : true,min:0,max: 99},
					   "AHA_max_value" : {required : true,greaterThan1: '#AHA_min_value',min:0,max: 99},
					   "AHA_default_value" : {required : true,greaterThan: '#AHA_min_value',lessThan: '#AHA_max_value'},
					   "ME_min_value": {required : true,lessThan1: '#HM_default_value'},
					   "ME_max_value": {required : true,lessThan1: '#HM_default_value',greaterThan1: '#ME_min_value'},
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
					   "DISPS_min_value" : {required : true,min:0,max: 99},
					   "DISPS_max_value" : {required : true,greaterThan1: '#DISPS_min_value',min:0,max: 99},
					   "DISPS_default_value" : {required : true,greaterThan: '#DISPS_min_value',lessThan: '#DISPS_max_value'},
					   "LNCS_min_value" : {required : true,lessThan1: '#LA_default_value'},
					   "LNCS_max_value" : {required : true,lessThan1: '#LA_default_value',greaterThan1: '#LNCS_min_value'},
					   "LNCS_default_value" : {required : true,greaterThan: '#LNCS_min_value',lessThan: '#LNCS_max_value'},
	               },
	               messages: {					   
					   "HM_min_value": {required : "Please provide a valid value.",max:"The home value should not be more than 999999999."},
					   "HM_max_value": {required : "Please provide a valid value.",max:"The home value should not be more than 999999999.",greaterThan1: "The maximum value should be more than min value"},
					   "HM_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "LA_min_value": {required : "Please provide a valid value.",max_lessThan: "Max value cannot be more than 97% of home value"},
					   "LA_max_value": {required : "Please provide a valid value.",max_lessThan: "Max value cannot be more than 97% of home value",greaterThan1: "The maximum value should be more than min value"},
					   "LA_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "DP_min_value": {required : "Please provide a valid value.",min_greaterThan: "the minimum value cannot be less than 3% of home value"},
					   "DP_max_value": {required : "Please provide a valid value.",max_lessThan1: "The maximum value cannot be more than 10% of home value",greaterThan1: "The maximum value should be more than min value"},
					   "DP_default_value" : {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
					   "DP_per_min_value": {required : "Please provide a valid value.",max: "The maximum value cannot be mor then 10%"},
					   "DP_per_max_value": {required : "Please provide a valid value.",max: "The maximum value cannot be mor then 10%",greaterThan1: "The maximum value should be more than min value"},
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
					   "DISPS_min_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99."},
					   "DISPS_max_value": {required : "Please provide a valid value.",max:"Max value cannot be more than 99.",greaterThan1: "The maximum value should be more than min value"},
					   "DISPS_default_value": {required : "Please provide a valid value.",greaterThan: "Default value cannot be less than min value",lessThan: "Default value cannot be more than max value"},
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
;
