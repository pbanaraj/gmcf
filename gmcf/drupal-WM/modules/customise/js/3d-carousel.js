var Conclave=(function(){
    var buArr =[],arlen;
    return {
      init:function(){
        this.addCN();this.clickReg();
      },
      addCN:function(){
		var pr_count = $("#products_count").val();
		if(pr_count < 4) {			
			switch(pr_count) {
				case "1":
					var buarr=["holder_bu_center"];
					break;
				case "2":
					var buarr=["holder_bu_awayL1","holder_bu_center"];
					break;
				case "3":
					var buarr=["holder_bu_awayL1","holder_bu_center","holder_bu_awayR1"];
					break;
				default:
					var buarr=["holder_bu_awayL1","holder_bu_center","holder_bu_awayR1"];
					break;
			}			
		} else {
			var buarr=["holder_bu_awayL3","holder_bu_awayL2","holder_bu_awayL1","holder_bu_center","holder_bu_awayR1","holder_bu_awayR2","holder_bu_awayR3"];
		}
        for(var i=1;i<=buarr.length;++i){
          $("#bu"+i).removeClass().addClass(buarr[i-1]+" holder_bu");
        }
		var center_id = $(".holder_bu_center").data('prodid');				
		$(".know-slide").removeClass("active");
		$("."+center_id).addClass("active");
      },
      clickReg:function(){
        $(".holder_bu").each(function(){
          buArr.push($(this).attr('class'))
        });
        arlen=buArr.length;
        for(var i=0;i<arlen;++i){
          buArr[i]=buArr[i].replace(" holder_bu","")
        };
        $(".holder_bu").click(function(buid){
          var me=this,id=this.id||buid,joId=$("#"+id),joCN=joId.attr("class").replace(" holder_bu","");
          var cpos=buArr.indexOf(joCN),mpos=buArr.indexOf("holder_bu_center");
          if(cpos!=mpos){
              tomove=cpos>mpos?arlen-cpos+mpos:mpos-cpos;
              while(tomove){
                var t=buArr.shift();
                buArr.push(t);
                for(var i=1;i<=arlen;++i){
                  $("#bu"+i).removeClass().addClass(buArr[i-1]+" holder_bu");
                }
                --tomove;
              }
          }
		  var center_id = $(".holder_bu_center").data('prodid');		
		  $(".know-slide").removeClass("active");
		  $("."+center_id).addClass("active");
        })
      },
      auto:function(){
        for(i=1;i<=1;++i){
          $(".holder_bu").delay(4000).trigger('click',"bu"+i).delay(4000);
          console.log("called");
        }
      }
    };
})();

$(document).ready(function(){
    window['conclave']=Conclave;
    Conclave.init();
    var purl = window.location.href;
    purl = purl.split('#');//alert($(".pro"+purl[1]).length);
    if($(".bu"+purl[1]).length) {
        $(".bu"+purl[1]).parent().trigger('click');
    }
})