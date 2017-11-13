/* jQuery('ul li:has(ul)').addClass('has-submenu');
jQuery('ul li ul').addClass('sub-menu');
jQuery('ul.dropdown li').hover(function () {
  jQuery(this).addClass('hover');
}, function () {
  jQuery(this).removeClass('hover');
});
var jQuerymenu = jQuery('#block-approvernewmenu-3'), jQuerymenulink = jQuery('#spinner-form'), jQuerymenuTrigger = jQuery('.has-submenu > a');
jQuerymenulink.click(function (e) {
  jQuerymenulink.toggleClass('active');
  jQuerymenu.toggleClass('active');
});
jQuerymenuTrigger.click(function (e) {
  e.preventDefault();
  var t = jQuery(this);
  t.toggleClass('active').next('ul').toggleClass('active');
});
jQuery('ul li:has(ul)');
jQuery(function () {
  var e = jQuery(document).scrollTop();
  var t = jQuery('.region-sidebar-first').outerHeight();
  jQuery(window).scroll(function () {
    var n = jQuery(document).scrollTop();
    if (jQuery(document).scrollTop() >= 50) {
        jQuery('.region-sidebar-first').css('position', 'fixed');
    } else {
        jQuery('.region-sidebar-first').css('position', 'fixed');
    }
    if (n > t) {
        jQuery('.region-sidebar-first').addClass('scroll');
    } else {
        jQuery('.region-sidebar-first').removeClass('scroll');
    }
    if (n > e) {
        jQuery('.region-sidebar-first').removeClass('no-scroll');
    } else {
        jQuery('.region-sidebar-first').addClass('no-scroll');
    }
    e = jQuery(document).scrollTop();
  });
});
 */
 jQuery(".menu--approver-new-menu ul li").first().removeClass("menu-item--expanded");
 jQuery(".menu--editor-menu ul li").first().removeClass("menu-item--expanded");