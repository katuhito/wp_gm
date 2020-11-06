jQuery(function($){
  "use strict"
  $(window).scroll(function() {
    if ($(window).scrollTop()>1) $('.bcorp-header-transparent').toggleClass('bcorp-header-transparent bcorp-header-transparency'); else
      $('.bcorp-header-transparency').toggleClass('bcorp-header-transparency bcorp-header-transparent');
  });

  if ($('.bcorp-wrapper').width() > 767) {
    $("#bcorp-floating-nav").sticky({topSpacing:$('.bcorp-wrapper').position().top});
  }

  $(window).resize(function() {
    if ($('.bcorp-wrapper').width() > 767) {
        $("#bcorp-floating-nav").show().unstick().sticky({topSpacing:$('.bcorp-wrapper').position().top});
    } else {
        $("#bcorp-floating-nav").hide().unstick();
    }
	});
});
