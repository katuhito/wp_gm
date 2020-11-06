var bcorp_youtube = [];
var bcorp_youtube_ready = false;

jQuery(function($){
	"use strict";

	$('.bcorp-video-controls').on( 'click',function( event ) {
		var player = $(this).find('iframe');
		if (player.hasClass('bcorp-video-vimeo')) {
			if (!$(this).find('.bcorp-video-playpause').hasClass('bcorp-video-playpaused')) player[0].contentWindow.postMessage({method:'pause'},'*');
			else  player[0].contentWindow.postMessage({method:'play'},'*');
		} else if (player.hasClass('bcorp-video-youtube')) {
			if (bcorp_youtube[player.attr('id')]) {
				if (!$(this).find('.bcorp-video-playpause').hasClass('bcorp-video-playpaused')) {
					if (bcorp_youtube[player.attr('id')].getPlayerState()==YT.PlayerState.PLAYING) bcorp_youtube[player.attr('id')].pauseVideo();
				}
				else  bcorp_youtube[player.attr('id')].playVideo();
			} else {
				bcorp_youtube[player.attr('id')] = new YT.Player(player.attr('id'), {
					events: {
						onReady: function(event){
							if (player.hasClass("bcorp-video-mute")) event.target.mute();
							if (player.hasClass("bcorp-video-autoplay")) event.target.playVideo();
						},
						onStateChange: function(event){
							if ((event.data == YT.PlayerState.ENDED) &&  (player.hasClass("bcorp-video-loop"))) event.target.playVideo();
						}
					}
				});
			}
		}
		var $playpause = $(this).find('.bcorp-video-playpause').removeClass('bcorp-video-playpause-flash');
		if ($playpause.hasClass('bcorp-video-playpaused')) $playpause.removeClass('bcorp-video-playpaused');
		else $playpause.addClass('bcorp-video-playpaused');
		$(this).width(); // Trigger Reflow for play-pause animation
		$playpause.addClass('bcorp-video-playpause-flash');
	});


	var bcorp_parallax = [];
	var bcorp_parallax_speed = [];
	var $bcorp_parallax = [];
	var i = 0;
	$('.bcorp-scroll-parallax .bcorp-background-image').each(function(){
		bcorp_parallax[i] = this;
		$bcorp_parallax[i] = $(this);
		bcorp_parallax_speed[i]= $bcorp_parallax[i].parent().attr('data-parallax-speed');
		i++;
	});

	var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
	var transforms = ["transform","msTransform","webkitTransform","mozTransform","oTransform"];
	var transformProperty = getSupportedPropertyName(transforms);
	function getSupportedPropertyName(properties) {
    for (var i = 0; i < properties.length; i++) {
      if (typeof document.body.style[properties[i]] != "undefined") {
          return properties[i];
      }
    }
    return null;
	}

	if (requestAnimationFrame) bcorp_do_parallax();
	var scrolling = true;
	window.addEventListener("scroll", function(){scrolling = true; }, false);

	function bcorp_do_parallax() {
		if (scrolling) {
			for (var i = 0; i < bcorp_parallax.length; ++i) {
				if (bcorp_parallax_speed[i] <0) {
					var offset = -$bcorp_parallax[i].height()/2 + $bcorp_parallax[i].parent().height()/2;
						bcorp_parallax[i].style[transformProperty] = "translate3d(0px, " + (offset + (  ($bcorp_parallax[i].parent().position().top - window.pageYOffset) * bcorp_parallax_speed[i]))  + "px" + ",0px)";
				} else{
					var top = $bcorp_parallax[i].parent().position().top;
					var offset =  ( top - window.pageYOffset) * ( parseFloat(bcorp_parallax_speed[i])) - parseFloat(bcorp_parallax_speed[i]) * Math.min(window.innerHeight,top);
					bcorp_parallax[i].style[transformProperty] = "translate3d(0px, " + offset + "px,0px)";
				}
			}
			scrolling = false;
		}
		requestAnimationFrame(bcorp_do_parallax);
	}

	$(window).resize(function() {
			bcorp_window_resize();
	});
	bcorp_window_resize();

	function bcorp_window_resize(){
	var w=$('.bcorp-wrapper').width();
	$('.bcorp-scroll-parallax').each(function(){
		var bcorp_parallax_speed = $(this).attr('data-parallax-speed');
		var h=$(this).height();
		var bw=$(this).attr('data-background-width');
		var bh=$(this).attr('data-background-height');
		if (bw && bh) {
			if (bcorp_parallax_speed <0) {
				var nh = h - bcorp_parallax_speed * ($(window).height()-h);
				var nh = $(window).height() + bcorp_parallax_speed * ($(window).height()-h);
			}	else var nh = Math.ceil(bcorp_parallax_speed *(Math.min($(this).position().top,$(window).height())+h)+h);
			var nw = Math.ceil(nh * bw / bh);
			if (nw < w) {
				nh = Math.ceil(w * bh / bw);
				nw = w;
			}
			if (nw > w) $(this).find('.bcorp-background-image').css('left',-(nw-w)/2 +'px');
				else $(this).find('.bcorp-background-image').css('left','0');
			$(this).find('.bcorp-background-image img').css('width',nw+'px').css('height',nh+'px');
		}
	});

	$('.bcorp-scroll-fixed').each(function(){
		var bw=$(this).attr('data-background-width');
		var bh=$(this).attr('data-background-height');
		if (bw && bh) {
			var nh = $(window).height();
			var nw = Math.ceil(nh * bw / bh);
			if (nw < w) {
				nh = Math.ceil(w * bh / bw);
				nw = w;
			}
				if (nw > w) $(this).find('.bcorp-background-image').css('left',-(nw-w)/2 +'px');
					else $(this).find('.bcorp-background-image').css('left','0');
			$(this).find('.bcorp-background-image img').css('width',nw+'px').css('height',nh+'px');
		}
	});

	if ($('#main-content').length > 0) var maincontenty = $('#main-content').position().top; else var maincontenty = 0;
	var h=$(window).height();

	$('.bcorp-fullwidth-video').each(function(){
		var videoratio = $(this).attr('data-video-ratio');
		$(this).parent().css('height',w * videoratio /100 +'px');
	});

	$('.bcorp-fullscreen').each(function(){
		$(this).css('height',h-maincontenty+'px');
	});

	$('.bcorp-video-stretch').each(function(){
		var videoratio = $(this).attr('data-video-ratio');
		var currentratio = $(this).parent().height()/ w*100;
		var enlargement = Math.max(currentratio / videoratio, videoratio /currentratio )*112 - 100;
		$(this).css('width',100 + enlargement+'%');
		$(this).css('height',100 + enlargement+'%');
		$(this).css('left',-(enlargement/2)+'%');
		$(this).css('top',-(enlargement/2)+'%');
	});

	$('.bcorp-video-slide').each(function(){
		var videoratio = $(this).attr('data-video-ratio');
		var currentratio = $(this).parent().parent().parent().height()/ w*100;
		var enlargement = Math.max(currentratio / videoratio, videoratio /currentratio )*112 - 100;
		$(this).css('width',100 + enlargement+'%');
		$(this).css('height',100 + enlargement+'%');
		$(this).css('left',-(enlargement/2)+'%');
		$(this).css('top',-(enlargement/2)+'%');
	});

	$('.bcorp-video-slide-fullwidth').each(function(){
		var videoratio = $(this).attr('data-video-ratio');
		var currentratio = $(this).parent().parent().parent().height()/ w*100;
		var enlargement = Math.max(currentratio / videoratio, videoratio /currentratio )*112 - 100;
		$(this).css('width',100 + enlargement+'%');
		$(this).css('height',100 + enlargement+'%');
		$(this).css('left',-(enlargement/2)+'%');
		$(this).css('top',-(enlargement/2)+'%');
	});

	$('.bcorp-video-slide-fullscreen').each(function(){
		var videoratio = $(this).attr('data-video-ratio');
		var currentratio = $(this).parent().parent().parent().height()/ w*100;
		var enlargement = Math.max(currentratio / videoratio, videoratio /currentratio )*112 - 100;
		$(this).css('width',100 + enlargement+'%');
		$(this).css('height',100 + enlargement+'%');
		$(this).css('left',-(enlargement/2)+'%');
		$(this).css('top',-(enlargement/2)+'%');
	});

	$('.bcorp-slide-fullwidth').each(function(){
		var bw=$(this).attr('data-background-width');
		var bh=$(this).attr('data-background-height');
		if (bw && bh) {
			var nh = Math.ceil(bh / bw * w);
			if (nh < 300) nh = 300;
			$(this).css('height',nh+'px');
		}
	});

	$('.bcorp-slide-standard').each(function(){
		var ww = $(this).parent().parent().parent().parent().parent().width();
		var bw=$(this).attr('data-background-width');
		var bh=$(this).attr('data-background-height');
		if (bw && bh) {
			var nh = Math.ceil(bh / bw * ww);
			if (nh < 320) nh = 320;
			$(this).css('height',nh+'px');
		}
	});

}

});

function onYouTubeIframeAPIReady() {
	"use strict";
	jQuery('.bcorp-youtube-autoplay-onload').each(function(){
		var player = jQuery(this);
		player.parent().css('background-color','#000000');
		bcorp_youtube[player.attr('id')] = new YT.Player(player.attr('id'), {
			events: {
				onReady: function(event){
					if (player.hasClass("bcorp-video-mute")) event.target.mute();
					event.target.seekTo(player.attr('data-starttime'));
					setTimeout(function() { player.parent().css('background-color','transparent'); }, 1000);
				},
				onStateChange: function(event){
					if ((event.data == YT.PlayerState.ENDED) &&  (player.hasClass("bcorp-video-loop"))) event.target.playVideo();
				}
			}
		});
	});
	bcorp_youtube_ready = true;
	jQuery(window).trigger('resize');
}
