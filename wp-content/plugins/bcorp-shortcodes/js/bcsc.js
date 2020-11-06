jQuery(function($){
	"use strict";
	$('body').imagesLoaded(function(){
		if(!(/MSIE\s/.test(navigator.userAgent) && parseFloat(navigator.appVersion.split("MSIE")[1]) < 10)){
			$('.bcorp-animated').each(function(){
				this.style.opacity = '0';
				new Waypoint({
					element: this,
					handler: function(direction) {
						this.element.style.opacity = '1';
						this.element.classList.add('animated', this.element.getAttribute('data-animation'));
					},
					offset: '100%'
				});
			});
		}
		$('.bcorp-blog-masonry').each(function(){
			$bcorp_blogs[$(this).attr('id')].isotope({ itemSelector: '.bcorp-blog-item',masonry: {columnWidth: '.grid-sizer', gutter: '.gutter-sizer'	} });
		});
	});

	var $bcorp_blogs = [];
	$('.bcorp-blog-content').each(function(){
		$bcorp_blogs[$(this).attr('id')] = $(this);
	});

	$('.bcorp-blog-filter').on( 'click', 'a', function( event ) {
		var id = $(this).parent().next().attr('id');
		$bcorp_blogs[id].isotope({ filter: $(this).attr('data-filter') });
		return false;
	});

	$('button.bcorp-blog-more').click(bcorpBlogMore);
	function bcorpBlogMore() {
		$(this).attr('data-clicks',parseInt($(this).attr('data-clicks'))+1);
		$(this).html('LOADING...').attr("disabled", true);
	  var data = {
	    'action': 'bcorp_blog_more',
			'data' : $(this).attr('data-info'),
			'clicks' : $(this).attr('data-clicks'),
			'blogID' : $(this).attr('data-blogID'),
			'bcorp_nonce': bcorp_shortcodes.bcorp_blog_more
	  };
	  $.post(bcorp_shortcodes.admin_ajax, data, function(response) {
			if (response['success']) {
				var $newItems = $(response['html']);
				$newItems.find('.bcorp-slider').each(function(){ startSlider(this); });
				$newItems.find('audio').css('visibility','visible');
				if ($bcorp_blogs[response['blogID']].hasClass('bcorp-blog-masonry')) {
					$newItems.hide();
					$bcorp_blogs[response['blogID']].append($newItems).imagesLoaded(function(){
						$bcorp_blogs[response['blogID']].isotope( 'appended', $newItems)
							.next().html('LOAD MORE').attr("disabled", false);
//						scaleSlider();
						$bcorp_blogs[response['blogID']].isotope();
					});
				} else {
						$bcorp_blogs[response['blogID']].append($newItems).next().html('LOAD MORE').attr("disabled", false);
				}
			}
			if (!response['more']) $bcorp_blogs[response['blogID']].next().remove();
	  });
	 return false;
	}

	$(".bcorp-gallery a[data-rel^='prettyPhoto']").bcorpAddPreviewViewer();
	$(".bcorp-gallery-thumbs").each(function(){
		$(this).magnificPopup({
		  delegate: 'a',
		  type: 'image',
			gallery:{
				enabled:true
			},
			mainClass: 'mfp-with-zoom',
			zoom: {
				enabled: true,
				duration: 300,
				easing: 'ease-in-out',
				opener: function(openerElement) {
					return openerElement.is('img') ? openerElement : openerElement.find('img');
				}
			}
		});
	});

	$(".bcorp-gallery-preview").bcorpPreviewBox();

	tabs('');
	function tabs(tabs) {
		$(tabs+'.bcorp-tabs').each(function(){
			if ($(this).hasClass('bcorp-vertical-tab')) $(this).css('min-height',$(this).find('ul').height());
			$(this).find('li:first').addClass('bcorp-active');
			$(this).find('.bcorp-tab-panel').hide();
			$(this).find('.bcorp-tab-panel:first').show();

			$(this).find('li').click(function(){
				if(!$(this).hasClass('bcorp-active')){
					$(this).parent().find('li').removeClass('bcorp-active');
					$(this).addClass('bcorp-active');
					$(this).parent().parent().find('.bcorp-tab-panel').hide();
					$($(this).find('a').attr('href')).show().find("div:first-child").hide().fadeIn();
				}
				return false;
			});
		});
	}

	accordion('');
	function accordion(accordion) {
		$(accordion+'.bcorp-accordion .bcorp-accordion-header').click(function() {
			if($(this).next("div").is(":visible")){
				$(this).toggleClass("bcorp-accordion-header-visible").toggleClass("bcorp-accordion-header-hidden").next("div").slideUp();
			} else {
				if ($('#'+$(this).parent().attr('id')).hasClass('bcorp-accordion-multiple-false')) $('#'+$(this).parent().attr('id')+' .bcorp-accordion-content').slideUp();
				$(this).toggleClass("bcorp-accordion-header-visible").toggleClass("bcorp-accordion-header-hidden").next("div").slideToggle();
			}
		});
		$(accordion+".bcorp-accordion-open-true").toggleClass("bcorp-accordion-header-visible").toggleClass("bcorp-accordion-header-hidden").next("div").slideToggle();
	}

});


jQuery.fn.bcorpPreviewBox=function(){
	"use strict";
	return this.each(function(){
		var $anchor=jQuery(this)
		$anchor.unbind('click').bind('click', function(){
			jQuery(this).next().magnificPopup({
			  delegate: 'a',
			  type: 'image',
				gallery:{
					enabled:true
				},
				mainClass: 'mfp-with-zoom',
				zoom: {
					enabled: true,
					duration: 300,
					easing: 'ease-in-out',
					opener: function(openerElement) {
						return openerElement.is('img') ? openerElement : openerElement.find('img');
					}
				}
			}).magnificPopup('open',parseInt(jQuery(this).find("div:first-child").attr('data-image-count')));
		})
	})
}

jQuery.fn.bcorpAddPreviewViewer=function(){
	"use strict";
	return this.each(function(){ //return jQuery obj
		var $anchor=jQuery(this)
		var $loadarea=jQuery('#'+$anchor.attr('data-preview-area'));
		var $hiddenimagediv=jQuery('<div />').css({position:'absolute',visibility:'hidden',left:-10000,top:-10000}).appendTo(document.body) //hidden div to load enlarged image in
		$anchor.unbind('mouseover').bind('mouseover', function(){
			if ($loadarea.data('$curanchor')==$anchor) return; //if mouse moves over same element again
			$loadarea.data('$curanchor', $anchor);
			if ($loadarea.data('$queueimage')) $loadarea.data('$queueimage').unbind('load') //if image is in the queue stop it first before showing current image
//			$loadarea.html('<img src="'+bcorp_shortcodes.bcorp_shortcodes_loading+'" /><br />Loading Large Image...'); // Load Message
			var $hiddenimage=$hiddenimagediv.find('img')
			if ($hiddenimage.length==0){ //if this is the first time moving over anchor
				var $hiddenimage=jQuery('<img src="'+$anchor.attr('data-preview-url')+'" />').appendTo($hiddenimagediv) //populate hidden div with enlarged image
				$hiddenimage.bind('bcorp_preload_image_evt', function(e){ //when enlarged image has fully loaded
					var imghtml='<img src="'+$anchor.attr('data-preview-url')+'" style="border-width:0" />'
					imghtml='<div class="bcorp-image-preview" data-image-count="'+$anchor.attr('data-image-count')+'">'+imghtml+(($anchor.attr('data-title')!='false' && $anchor.attr('title')!='')? '<br /><div class="bcorp-gallery-preview-caption">'+$anchor.attr('title')+'</div></div>' : '')+'</div>';
					var $targetimage=jQuery(imghtml).hide(); //create/reference actual enlarged image
					$loadarea.empty().append($targetimage) //show enlarged image
					$targetimage.stop()['fadeIn']('500', function(){
						if (this.style && this.style.removeAttribute) this.style.removeAttribute('filter') //fix IE clearType problem when animation is fade-in
					})
				})
				$loadarea.data('$queueimage', $hiddenimage) //remember currently loading image as queueimage
			}
			if ($hiddenimage.get(0).complete) $hiddenimage.trigger('bcorp_preload_image_evt')
			else $hiddenimage.bind('load',function(){$hiddenimage.trigger('bcorp_preload_image_evt')})
		})
	})
}
