jQuery(function($){
	"use strict";

	$("body").append('<div id="bcsc-dialog-overlay"></div><div id="bcsc-dialog" class="bcsc-dialog"></div><div id="bcsc-dialog-2" class="bcsc-dialog"></div><div id="bcsc-dialog-3" class="bcsc-dialog"></div>');

	bcsc.width_titles = {"1-6":"1/6","1-5":"1/5","1-4":"1/4","1-3":"1/3","2-5":"2/5","1-2":"1/2",
                           "3-5":"3/5","2-3":"2/3","3-4":"3/4","4-5":"4/5","5-6":"5/6","1-1":"1/1"};

	for (var sc in bcsc.sc) {
	 for (var key in bcsc.sc[sc]['variables']) {
	   if (typeof bcsc.sc[sc]['variables'][key] === 'string') {
	     var replacedvar = bcsc.sc[sc]['variables'][key];
	     for (var commonvar in bcsc.vars['commonvars'][bcsc.sc[sc]['variables'][key]]) {
	       bcsc.sc[sc]['variables'][commonvar] = jQuery.extend(true, {}, bcsc.vars['commonvars'][replacedvar][commonvar]);
	     }
	   }
	 }
	}

	for (var sc in bcsc.sc) {
	 for (var key in bcsc.sc[sc]['variables']) {
	   if (bcsc.sc[sc]['variables'][key].hasOwnProperty("dependents")) {
	     for (var dependent in bcsc.sc[sc]['variables'][key]['dependents']) {
	       for (var i in bcsc.sc[sc]['variables'][key]['dependents'][dependent]) {
	         var mydependent = bcsc.sc[sc]['variables'][key]['dependents'][dependent][i];
	         bcsc.sc[sc]['variables'][mydependent]['isdependent'] = true;
	       }
	     }
	   }
	 }
	}

  $('#bcorp-shortcodes-button').on('click',function(){
		if($('#wp-content-wrap').hasClass('html-active')) $('#content-tmce').trigger('click');
		chooseElement($(this));
		return false;
	});

function chooseElement($this) {
	var elements_dialog_html = '<div class="bcsc-dialog-header"><h2>Choose Element</h2><span class="bcsc-dialog-cancel dashicons dashicons-no"></span></div><div class="bcsc-element-list">';

	for (var key in bcsc.sc) {
			if (key == "bcorp_cell") {
					var icons = ["&#xe811;","&#xe812;","&#xe813;","&#xe814;","&#xe815;","&#xe816;","&#xe817;","&#xe818;","&#xe819;","&#xe81A;","&#xe81B;","&#xe81C;"];
					var i =0;
					for (var width in bcsc.width_titles) {
						elements_dialog_html += '<a class="bcsc-editor-button ' + bcsc.sc[key]['type'] + '" bcsc-shortcode="' + key + '" bcsc-width="' + width + '"><i class="bcsc-icon bcsc-select-icon-icon">'+icons[i]+'</i><br />' + bcsc.width_titles[width] + '</a>';
						i++;
					}
			} else {
					 elements_dialog_html += '<a class="bcsc-editor-button ' + bcsc.sc[key]['type'] + '" bcsc-shortcode="' + key + '" bcsc-width="' + bcsc.sc[key]['width'] + '"><i class="bcsc-icon bcsc-select-icon-icon">'+bcsc.sc[key]['admin_icon']+'</i><br />'  + bcsc.sc[key]['title'] + '</a>';
			}
	}
	elements_dialog_html += '</div>';

	var elements_dialog_id = '#bcsc-dialog';
	var $element_selection_page = $('#bcsc-dialog').html(elements_dialog_html);
	$element_selection_page.find('.bcsc-editor-button').click(function() {
		if($(this).attr("bcsc-shortcode") == 'bcorp_cell') {
			closeElementsDialog('#bcsc-dialog');
			tinymce.activeEditor.insertContent('[bcorp_cell width="' + $(this).attr("bcsc-width")+'"][/bcorp_cell]');
		} else if ($(this).attr("bcsc-shortcode") == 'bcorp_row') {
			closeElementsDialog('#bcsc-dialog');
			tinymce.activeEditor.insertContent('[bcorp_row][/bcorp_row]');
		} else {
			editLayoutItem($(this).attr("bcsc-shortcode"));
		}
	});
	$("#bcsc-dialog-overlay").click(function(){closeElementsDialog(elements_dialog_id)});
	$("#bcsc-dialog-overlay").css({"display":"block",opacity:0});
	$("#bcsc-dialog-overlay").fadeTo(200,0.3);
	$(elements_dialog_id).css({"display":"block"});
	$(elements_dialog_id).removeClass('zoomOut').addClass('zoomIn');
	$(elements_dialog_id+" .bcsc-dialog-cancel").click(function(){ closeElementsDialog(elements_dialog_id); });

}

function closeElementsDialog(elements_dialog_id){
	$("#bcsc-dialog-overlay").fadeOut(200);
	$(elements_dialog_id).addClass('zoomOut');
	setTimeout(function(){ $(elements_dialog_id).css({"display":"none"}); }, 200);
}

function editLayoutItem(sc) {
	$("#bcsc-dialog-overlay").click(function(){closeModal(modal_id)});
	$("#bcsc-dialog-2").html(editElementHTML(sc));
	if ($('#bcsc-rich-text-editor').length > 0) {
		var mytext = $('#bcsc-rich-text-editor').html();
		$('#bcsc-rich-text-editor').html($('#wp-bcsc-text-editor-wrap'));
		tinymce.EditorManager.execCommand('mceAddEditor',true, 'bcsc-text-editor');
		$('#wp-bcsc-text-editor-wrap').removeClass('html-active').addClass('tmce-active');
		if (tinymce.get('bcsc-text-editor') === null) setTimeout(function(){ tinymce.get('bcsc-text-editor').setContent(mytext); }, 200);
		else tinymce.get('bcsc-text-editor').setContent(mytext);
		$('#wp-bcsc-text-editor-wrap').show();
	}
	$('.bcsc-dialog-cancel').click(function(){closeModal(modal_id)});
	$('.bcsc-dialog-save').click(function(){saveModal(modal_id)});
	var modal_id = '#bcsc-dialog-2';
	$("#bcsc-dialog-overlay").css({"display":"block",opacity:0});
	$("#bcsc-dialog-overlay").fadeTo(200,0.7);
	$(modal_id).css({"display":"block"});
	$(modal_id).removeClass('zoomOut').addClass('zoomIn');
	function closeModal(modal_id){
		$("#bcsc-dialog-overlay").fadeOut(200);
		$(modal_id).addClass('zoomOut');
		setTimeout(function(){ $(modal_id).css({"display":"none"}); }, 200);
	}
	function saveModal(modal_id){
		processElement(sc);
		closeModal(modal_id);
		closeElementsDialog('#bcsc-dialog');
	}
	tabs();
	triggerEditButtons();
}

function triggerEditButtons () {
	$('button.bcsc-select-image').unbind("click");
	$('button.bcsc-clear-image').unbind("click");
	$('button.bcsc-insert-video').unbind("click");
	$('button.bcsc-select-icon').unbind("click");
	$('button.bcsc-select-gallery').unbind("click");
	$('.bcsc-color-picker').unbind();

	$('button.bcsc-iris-color-picker-button').unbind("click");
	$('button.bcsc-iris-color-picker-clear-color').unbind("click");
	$('.bcsc-dependent').unbind("change");

	$('.bcsc-select-image').click(selectImage);
	$('.bcsc-clear-image').click(clearImage);
	$('button.bcsc-insert-video').click(chooseVideo);
	$('button.bcsc-select-icon').click(chooseIcon);
	$('.bcsc-select-gallery').click(selectGallery);

	$('.bcsc-color-picker').each(function(){
		var irisPlaceHolder = '#'+$(this).attr("id")+'-iris-placeholder';
		var irisColorSwatch = '#'+$(this).attr("id")+'-iris-swatch';
		$(this).iris({
			width: 300,
			hide: true,
			target: irisPlaceHolder,
			palettes: true,
			change: function(event, ui) {
				$(irisColorSwatch).css( 'background-color', ui.color.toString());
			},
		})
	});
	$('.bcsc-color-picker').click(showColorPicker);
	$('button.bcsc-iris-color-picker-clear-color').click(clearColor);
	$('button.bcsc-iris-color-picker-button').click(toggleColorPicker);

	$('.bcsc-dependent').change(dependentDropDown);
}

function clearColor() { $(this).prev().val('').iris('hide').prev().find('div').css('background-color',''); }

function toggleColorPicker() { $(this).next().iris('toggle'); }

function showColorPicker() { $(this).iris('show'); }

function chooseVideo() {
	var id = this.id
	var frame = wp.media({
		title: 'Select Video',
		library: {
			type: 'video'
		},
	});
	frame.on( 'select', function() {
		var attachment = frame.state().get('selection').first();
		$('#'+id).val(attachment.attributes.url )
	});
	frame.open();
}

function selectImage() {
	var id = $(this).attr("bcsc-key");
	var frame = wp.media({
		title: 'Select Image',
		library: {
			type: 'image'
		},
	});
	frame.on( 'select', function() {
		var attachment = frame.state().get('selection').first();
		if(!attachment.attributes.sizes.thumbnail) {
			var thumb_url = attachment.attributes.sizes.full.url;
		} else {
			var thumb_url = attachment.attributes.sizes.thumbnail.url;
		}
		$('#bcsc-'+id+'-placeholder').html('<img src="'+thumb_url+'">');
		$('#bcsc-'+id).val(attachment.attributes.id );
	});
	frame.open();
}

function clearImage() {
	var id = $(this).attr("bcsc-key");
	$('#bcsc-'+id+'-placeholder').html('<div class="bcsc-image-placeholder">&#xe804;</div>');
	$('#bcsc-'+id).val("");
}
function chooseIcon() {
	var id = this.id
	var iconpage = '<div class="bcsc-dialog-header"><h2>Select Icon</h2><span class="bcsc-dialog-cancel dashicons dashicons-no"></span></div><div class="bcsc-icon-list">';
	var missingicons = [61455,61471,61472,61503,61519,61535,61551,61567,61583,61599,61615,61619,61620,61621,61622,61623,61624,61625,61626,61627,61628,61629,61630,61631,61647,61663,61679,61695,61711,61718,61719,61727,61743,61759,61775,61791,61807,61823,61839,61855,61871,61887,61903,61919,61935,61951,61967,61983,61984,61998,61999,62015,62031,62047,62063,62079,62095]
	for (var i = 61440; i < 62102; i++) {
		while(missingicons.indexOf(i) > -1) { i++; }
		iconpage += '<span class="bcsc-icons" data-key="'+i.toString(16)+'" data-icon="&#x'+ i.toString(16)+';"></span>';
	}
	iconpage += '</div>';

	var icon_dialog_id = '#bcsc-dialog-3';
	var $icon_selection_page = $(icon_dialog_id).html(iconpage);

	$icon_selection_page.find('.bcsc-icons').click(function() {
		var icon_value = $(this).attr('data-key');
		$(icon_dialog_id).addClass('zoomOut');
		setTimeout(function(){ $(icon_dialog_id).css({"display":"none"}); }, 200);
		$('#'+id+'-placeholder').html('<span data-icon="&#x'+ icon_value+';"></span>');
		$('#'+id).val(icon_value);
	});
	$("#bcsc-dialog-overlay").click(function(){ $(icon_dialog_id).css({"display":"none"}); });
	$(icon_dialog_id+" .bcsc-dialog-cancel").click(function(){
		$(icon_dialog_id).addClass('zoomOut');
		setTimeout(function(){ $(icon_dialog_id).css({"display":"none"}); }, 200);
	});
	$(icon_dialog_id).css({"display":"block"});
	$(icon_dialog_id).removeClass('zoomOut').addClass('zoomIn');
}

function selectGallery() {
	var id = $(this).attr("bcsc-key");
	var selection = loadGalleryImages($('#bcsc-ids').val());
	var frame  = wp.media({
		state:      'gallery-edit',
		frame:      'post',
		library: {
			type: 'image',
		},
		selection:selection,
	});
	frame.on( 'update', function() {
		var ids = frame.states.get('gallery-edit').get('library').pluck('id');
		var galleryhtml = '';
		var attachments = frame.states.get('gallery-edit').get('library').models;
		for (var x in attachments) {
			if(!attachments[x].attributes.sizes.thumbnail) {
				var thumb_url = attachments[x].attributes.sizes.full.url;
			} else {
				var thumb_url = attachments[x].attributes.sizes.thumbnail.url;
			}
			galleryhtml += '<img class="attachment-thumbnail" src="'+ thumb_url +'">';
		}
		jQuery("#bcsc-ids").val(ids.join(","));
		$('#bcsc-'+id+'-placeholder').html(galleryhtml);
	});
	frame.open();
}

function loadGalleryImages(images){
	if (images){
			var shortcode = new wp.shortcode({
					tag:      'gallery',
					attrs:    { ids: images },
					type:     'single'
			});
			var attachments = wp.media.gallery.attachments( shortcode );
			var selection = new wp.media.model.Selection( attachments.models, {
					props:    attachments.props.toJSON(),
					multiple: true
			});
			selection.gallery = attachments.gallery;
			selection.more().done( function() {
					selection.props.set({ query: false });
					selection.unmirror();
					selection.props.unset('orderby');
			});
			return selection;
	}
	return false;
}

function dependentDropDown() {
	var id = $(this).attr("id");
	var key = $(this).attr("bcsc-key");
	var sc = $(this).attr("bcsc-shortcode");
	var param = bcsc.sc[sc]['variables'][key];
	if (param['type'] != 'checkbox') var value = $(this).val(); else if ($(this).attr('checked')) value=true; else value=false;
	var dependentHTML = '';
	for (var dependent in param['dependents'][value]) dependentHTML += addParam(param['dependents'][value][dependent],sc);
	$(".bcsc-dependent-"+id).html(dependentHTML);
	triggerEditButtons();
}

function addParam(key,sc){
	var param = bcsc.sc[sc]['variables'][key];
	var shortcode_value = bcsc.sc[sc]['variables'][key]['default'];
	if (param.hasOwnProperty("dependents")) var dependent = ' class="bcsc-dependent" bcsc-key="' + key +'" bcsc-shortcode="' + sc + '" '; else var dependent = '';
	switch (param['type']) {
		case "icon":
			if (shortcode_value) var bcorp_icon = '&#x' + shortcode_value + ';'; else var bcorp_icon = '&#61445;';
			var dialogHTML = '<div class="bcsc-icon-placeholder" id="bcsc-' + key + '-placeholder"><span data-icon="'+ bcorp_icon + '"></span></div>';
			dialogHTML += '<input type="hidden" id="bcsc-' + key +'" value="'+ shortcode_value + '"><button id="bcsc-' + key + '" class="bcsc-select-icon" type="button">Select Icon</button><br />';
			break;
		case "textfield":
			var dialogHTML = '<label>'+ param['name'] +' </label><br /><input class="bcsc-dialog-textfield" type="text" id="bcsc-' + key +'" value="'+ shortcode_value + '"><br />';
			break;
		case "color":
			dialogHTML = '<label>'+ param['name'] +' </label><br /><button class="bcsc-iris-color-picker-button"><div id="bcsc-'+key+'-iris-swatch" class="bcsc-iris-color-picker-swatch" style="background-color:'+shortcode_value+';"></div>Select Color</button><input class="bcsc-color-picker" type="text" id="bcsc-' + key +'" value="'+ shortcode_value + '"><button class="bcsc-iris-color-picker-clear-color" type="button">Clear</button>';
			dialogHTML += '<div class="bcsc-iris-color-picker-container"><div class="bcsc-iris-color-picker-placeholder" id="bcsc-'+key+'-iris-placeholder"></div></div>';
			break;
		case "image":
			var dialogHTML = '<div class="bcsc-image-selector"><div id="bcsc-' + key + '-placeholder"></div>';
			dialogHTML += '<input type="hidden" id="bcsc-' + key + '" value="'+ shortcode_value + '">';
			dialogHTML += '<button class="bcsc-select-image" bcsc-key="' + key + '" type="button">Select</button><button class="bcsc-clear-image" bcsc-key="' + key + '" type="button">Clear</button></div>';
			break;
		case "video":
			var dialogHTML = '<label>'+ param['name'] +' </label><br /><input class="bcsc-dialog-textfield" type="text" id="bcsc-' + key +'" value="'+ shortcode_value + '"><br />';
			dialogHTML += '<button id="bcsc-' + key + '" class="bcsc-insert-video" type="button">Insert Video</button><br />';
			break;
		case "gallery":

			var dialogHTML = '<div id="bcsc-' + key + '-placeholder" class="bcsc-gallery-placeholder"></div><button class="bcsc-select-gallery" bcsc-key="' + key + '" type="button">Select Gallery</button><br />';
			dialogHTML += '<input type="hidden" id="bcsc-' + key +'" value="'+ shortcode_value + '">';
			break;
		case "dropdown":
			if (param['selectmultiple']) {
				var selectmultiple = ' multiple="multiple" size="6"';
				if (shortcode_value) var shortcode_values = shortcode_value.split(',');
				else shortcode_values = '';
			} else {
				selectmultiple = '';
				var shortcode_values = [shortcode_value];
			}
			if (typeof param['values'] === 'string') param['values'] = bcsc.vars[param['values']];
			var dialogHTML = '<label>'+ param['name'] +' </label><select id="bcsc-' + key +'"' + selectmultiple + dependent + '>';
			for (var option in param['values']) {
				if (shortcode_values.indexOf(option) >-1 ) var selected=' selected'; else var selected = '';
				dialogHTML += '<option' + selected + ' value="'+ option +'">'  + param['values'][option] + '</option>';
			}
			dialogHTML += '</select><br />';
			break;
		case "checkbox":
			var dialogHTML = '<input id="bcsc-' + key +'" type="checkbox"';
			if (shortcode_value === 'true') dialogHTML += ' checked' ;
			dialogHTML += dependent + '><label>'+ param['name'] +' </label><br />';
			break;
		case "textarea":
			var dialogHTML = '<label>'+ param['name'] +' </label><br />';
			dialogHTML += '<textarea id="textblock" class="bcsc-code-area"></textarea>';
			break;
	}
	if (param['description']) dialogHTML += '<div class="bcsc-description">' + param['description'] + '</div>';
	dialogHTML += '<br />';
	if (param.hasOwnProperty("dependents")) {
		var dependents = param['dependents'][shortcode_value];
		dialogHTML += '<div class="bcsc-dependent-bcsc-'+key+'">';
		for (var dependent in dependents) {
			dialogHTML += addParam(dependents[dependent],sc);
		}
		dialogHTML += '</div>';
	}
	return dialogHTML;
}

function editElementHTML(sc) {
	var dialogHTML = '<div class="bcsc-dialog-tab"><ul>';
	var dialogTab = [];
	for (var key in bcsc.sc[sc]['variables']) {
		if (bcsc.sc[sc]['variables'][key]['isdependent']!==true) {
			if (bcsc.sc[sc]['variables'][key]['admin_tab'] == undefined) var admin_tab = bcsc.sc[sc]['title'];
				else var admin_tab = bcsc.sc[sc]['variables'][key]['admin_tab'];
			if (dialogTab[admin_tab] == undefined) dialogTab[admin_tab] = '';
			dialogTab[admin_tab] += addParam(key,sc);
		}
	}

	var tabindex=1;
	for (var key in dialogTab){
		dialogHTML += '<li><a href="#bcsc-tab-'+tabindex+'">'+key+'</a></li>';
		tabindex++;
	}
	dialogHTML += '</ul>';
	dialogHTML += '<span class="bcsc-dialog-cancel dashicons dashicons-no"></span>';
	tabindex=1;
	for (var key in dialogTab){
		dialogHTML += '<div class="bcsc-dialog-tabbed" id="bcsc-tab-'+tabindex+'">';
		dialogHTML += dialogTab[key];
		dialogHTML += '</div>';
		tabindex++;
	}
dialogHTML += '</div>';
dialogHTML +='<div class="bcsc-dialog-buttons"><button class="bcsc-dialog-save button button-primary button-large">Save</button></div>';
return dialogHTML;
}

function tabs() {
	$('.bcsc-dialog-tab').each(function(){
		$(this).find('li:first').addClass('bcorp-active');
		$(this).find('.bcsc-dialog-tabbed').hide();
		$(this).find('.bcsc-dialog-tabbed:first').show();
		$(this).find('li').click(function(){
			if(!$(this).hasClass('bcorp-active')){
				$(this).parent().find('li').removeClass('bcorp-active');
				$(this).addClass('bcorp-active');
				$(this).parent().parent().find('.bcsc-dialog-tabbed').hide();
				$($(this).find('a').attr('href')).show().find("div:first-child").hide().fadeIn();
				$('.bcsc-color-picker').iris('hide');
			}
			return false;
		});
	});
}

function processElement(sc) {
	console.log(sc)
	var mysetting = "";
	var textBlockText = "";
	for (var key in bcsc.sc[sc]['variables']) {
		var param = bcsc.sc[sc]['variables'][key];
		if (sc === 'bcorp_cell' && key == 'width') {
			var cell_width = $('#bcorp_width').val();
		}
		var mysc = "#bcsc-" + key;
		if (param['type'] == 'textarea') {
				textBlockText = '<p>'+$('#textblock').val()+'</p>';
		} else {
			if (param['type'] != 'checkbox') var myvalue = $(mysc).val();
			else if ($(mysc).attr('checked')) myvalue=true; else myvalue=false;
			if ((typeof myvalue != 'undefined') && (myvalue != null)) {
				mysetting += " " + key + '="' + myvalue + '"';
			}
		}
	}
	tinymce.activeEditor.insertContent('[' + sc +mysetting+']'+textBlockText);
	if (bcsc.sc[sc]['closing_tag'] === true) tinymce.activeEditor.insertContent('[/' + sc + ']');
}

});
