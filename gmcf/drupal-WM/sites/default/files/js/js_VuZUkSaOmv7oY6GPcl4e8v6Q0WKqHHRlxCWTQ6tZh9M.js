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
});
(function($) {
  $.fn.cascadeSlider = function(opt) {
    var $this = this,
      itemClass = opt.itemClass || 'cascade-slider_item',
      arrowClass = opt.arrowClass || 'cascade-slider_arrow',
      $item = $this.find('.' + itemClass),
      $arrow = $this.find('.' + arrowClass),
      itemCount = $item.length;

    var defaultIndex = 0;

    changeIndex(defaultIndex);

    $arrow.on('click', function() {
      var action = $(this).data('action'),
        nowIndex = $item.index($this.find('.now'));

      if(action == 'next') {
        if(nowIndex == itemCount - 1) {
          changeIndex(0);
        } else {
          changeIndex(nowIndex + 1);
        }
      } else if (action == 'prev') {
        if(nowIndex == 0) {
          changeIndex(itemCount - 1);
        } else {
          changeIndex(nowIndex - 1);
        }
      }

      $('.cascade-slider_dot').removeClass('cur');
      //$('.cascade-slider_dot').next().addClass('cur');
    });
    
    // add data attributes
    for (var i = 0; i < itemCount; i++) {
      $('.cascade-slider_item').each(function(i) {
        $(this).attr('data-slide-number', [i]);
      });
    }
    
    // dots
    $('.cascade-slider_dot').bind('click', function(){
      // add class to current dot on click
      $('.cascade-slider_dot').removeClass('cur');
      $(this).addClass('cur');

      var index = $(this).index();

      $('.cascade-slider_item').removeClass('now prev next');
      var slide = $('.cascade-slider_slides').find('[data-slide-number=' + index + ']');
      slide.prev().addClass('prev');
      slide.addClass('now');
      slide.next().addClass('next');

      if(slide.next().length == 0) {
        $('.cascade-slider_item:first-child').addClass('next');
      }

      if(slide.prev().length == 0) {
        $('.cascade-slider_item:last-child').addClass('prev');
      }
    });

    function changeIndex(nowIndex) {
      // clern all class
      $this.find('.now').removeClass('now');
      $this.find('.next').removeClass('next');
      $this.find('.prev').removeClass('prev');
      if(nowIndex == itemCount -1){
        $item.eq(0).addClass('next');
      }
      if(nowIndex == 0) {
        $item.eq(itemCount -1).addClass('prev');
      }

      $item.each(function(index) {
        if(index == nowIndex) {
          $item.eq(index).addClass('now');
        }
        if(index == nowIndex + 1 ) {
          $item.eq(index).addClass('next');
        }
        if(index == nowIndex - 1 ) {
          $item.eq(index).addClass('prev');
        }
      });
    }
  };
})(jQuery);
;
(function ($) {   
   'use strict';
   $(document).ready(function() {
				$('#cascade-slider').cascadeSlider({
			
			});
			});
   
})(jQuery);
;
