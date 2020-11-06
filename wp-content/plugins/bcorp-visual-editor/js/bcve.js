jQuery(function($){
  "use strict";
  if($('#wp-content-wrap').hasClass('tmce-active') || $('#bcorp-visual-editor-active').attr('value') == 'false') {
    $('#bcorp-visual-editor-meta').hide();
    $('#postdivrich').before('<br /><button class="button button-primary button-large bcve-toggle-editor type="button">Visual Editor</button>');
    $('#postdivrich').css({"height":"100%","visibility":"visible"});
    $('.bcve-toggle-editor').on('click',function(){
      $('#bcorp-visual-editor-active').attr('value','true');
      $('#content-html').trigger('click');
    });
    return false;
  }
  var helper_sc;
  $('#postdivrich').css({"height":"0","visibility":"hidden"});
  $('#postdivrich').before('<br /><button class="button button-primary button-large bcve-toggle-editor type="button">Default Editor</button>');
  $('#bcorp-visual-editor').html('<button class="bcve-add-element" data-element-type="first" data-shortcode="bcorp_section" data-width="1-1" type="button"><span class="dashicons dashicons-plus"></span></button><div id="bcve-container" class="bcve bcve-editor" data-shortcode="bcorp_section" data-element-type="first"></div>');
  $("body").append('<div id="bcve-processed"></div><div id="bcve-dialog-overlay"></div><div id="bcve-dialog" class="bcve-dialog"></div><div id="bcve-dialog-2" class="bcve-dialog"></div>');
  bcve.width_titles = {"1-6":"1/6","1-5":"1/5","1-4":"1/4","1-3":"1/3","2-5":"2/5","1-2":"1/2",
                           "3-5":"3/5","2-3":"2/3","3-4":"3/4","4-5":"4/5","5-6":"5/6","1-1":"1/1"};
  setTimeout(function(){ $('#content-html').trigger('click'); }, 1000);
  $('.bcve-toggle-editor').on('click',function(){
    if($(this).text() == 'Default Editor') {
      $(this).text('Visual Editor');
      $('#bcorp-visual-editor-active').attr('value','false');
      $('#bcorp-visual-editor-meta').hide();
      $('#content-html').trigger('click');
      $('#postdivrich').css({"height":"100%","visibility":"visible"});
    } else {
      $('#bcorp-visual-editor-active').attr('value','true');
      $(this).text('Default Editor');
      $('#bcorp-visual-editor-meta').show();
      $('#postdivrich').css({"height":"0","visibility":"hidden"});
    }
    processShortCode();
    return false;
  });

  if ($('#content').length != 0) {
    var content = $("#content").val();
    var contents = '';
    var elements = [];
    for (var i = 0, len = content.length; i < len; i++) {
      if (content[i]=='[' && (content.substr(i, 7)=='[bcorp_' || content.substr(i, 8)=='[/bcorp_')){
        if (contents.length) elements.push(contents.slice(1));
        var shortcode = '';
        for (var i = i, len = content.length; i < len; i++) {
          if (content[i]==']') {
            shortcode += content[i];
            contents = '';
            elements.push(shortcode);
            break;
          } else {
            shortcode += content[i];
          }
        }
      }
      contents+=content[i];
    }

    for (var sc in bcve.sc) {
      for (var key in bcve.sc[sc]['variables']) {
        if (typeof bcve.sc[sc]['variables'][key] === 'string') {
          var replacedvar = bcve.sc[sc]['variables'][key];
          for (var commonvar in bcve.vars['commonvars'][bcve.sc[sc]['variables'][key]]) {
            bcve.sc[sc]['variables'][commonvar] = jQuery.extend(true, {}, bcve.vars['commonvars'][replacedvar][commonvar]);
          }
        }
      }
    }

    for (var sc in bcve.sc) {
      for (var key in bcve.sc[sc]['variables']) {
        if (bcve.sc[sc]['variables'][key].hasOwnProperty("dependents")) {
          for (var dependent in bcve.sc[sc]['variables'][key]['dependents']) {
            for (var i in bcve.sc[sc]['variables'][key]['dependents'][dependent]) {
              var mydependent = bcve.sc[sc]['variables'][key]['dependents'][dependent][i];
              bcve.sc[sc]['variables'][mydependent]['isdependent'] = true;
            }
          }
        }
      }
    }

    var visualdata = '';
    for (var i in elements) {
      if (elements[i][0]=='[') {
        if ( elements[i][1]!='/') {
          var sc = elements[i].substr(1,elements[i].length-2);
          if (sc.indexOf(' ')>0) sc = sc.substr(0,sc.indexOf(' '));
          if (sc == "bcorp_cell") {
            var split_variable = new RegExp(' width="([^"]+)', 'i');
            var variableValue = split_variable.exec( elements[i] );
            var width = variableValue[1];
            var lastwidth = width;
          } else if (typeof bcve.sc[sc] === 'undefined' ) var width = '1-1';
            else var width = bcve.sc[sc]['width'];

          if (typeof bcve.sc[sc] != 'undefined' ) {
            visualdata += startElement(sc,width,elements[i],'');
            if (!bcve.sc[sc]['closing_tag']) visualdata += endElement(sc,width);
          }
        } else {
          var sc = elements[i].substr(2,elements[i].length-3);
          if (typeof bcve.sc[sc] != 'undefined' ) visualdata += endElement(sc,lastwidth);
        }
      } else {
        visualdata += '<div class="bcve-shortcode-data">'+elements[i]+'</div>';
      }
    }

    $('#bcve-container').append(visualdata);
    var ajax_id = 0;
    var images = {};
    $('.bcve-element').each(function(){
      var $editedElement = $(this);
      var sc = $editedElement.find('.bcve-element-shortcode:first').text();
      $editedElement.find('.bcve-admin-content:first > div').removeClass().addClass("bcve-"+sc);
      var full_sc = $editedElement.find('.bcve-shortcode-data:first').text();
      for (var key in bcve.sc[sc]['variables']) {
        var param = bcve.sc[sc]['variables'][key];
        var split_variable = new RegExp( ' '+key+'="([^"]+)', 'i');
        var values = split_variable.exec( full_sc );
        if (values) var myvalue = values[1]; else var myvalue ='';
        if (param['type'] == 'textarea') {
          if (param['editor'] === 'tinymce') {
            myvalue = $editedElement.find(".bcve-element-content .bcve-shortcode-data").html();
            $editedElement.find(".bcve-"+sc+'-'+key).html(myvalue);
          } else {
            myvalue = $editedElement.find(".bcve-element-content .bcve-shortcode-data").text();
            $editedElement.find(".bcve-"+sc+'-'+key).html(unescape(myvalue));
          }
        } else {
          if ((typeof myvalue != 'undefined') && (myvalue != null)) {
            if (param['type'] == 'icon') {
              if(!myvalue) myvalue = 'f005';
              $editedElement.find(".bcve-"+sc+'-'+key).attr('data-icon',String.fromCharCode('0x'+myvalue));
            } else {
              if ((param['type'] === 'image') || (param['type'] === 'gallery')) {
                ajax_id++;
                $editedElement.find(".bcve-"+sc+'-'+key).attr('id', 'bcve-ajax-id-'+ajax_id);
                if(myvalue) {
                  images['bcve-ajax-id-'+ajax_id]=myvalue;
                }
              } else {
                if(!myvalue) myvalue = bcve.sc[sc]['variables'][key]['default'];
                $editedElement.find(".bcve-"+sc+'-'+key).html(myvalue);
              }
            }
            if (param['admin_class'] === true) $editedElement.find('.bcve-admin-content:first > div').addClass("bcve-"+sc+"-"+key+"-"+myvalue);
          }
        }
      }
    });

    if (Object.keys(images).length) {
      var data = {
        'action': 'bcve_ajax',
        'images': images,
        'bcve_nonce': bcve.nonce
      };
      $.post(ajaxurl, data, function(response) {
          if (response['success'] == 'true') {
          $.each(response['images'], function(key, value) {
            if (value.constructor === Array) {
              var html = '';
              for (i = 0; i < value.length; i++) {
                html +='<img src="'+value[i]+'">';
              }
              $('#'+key).html(html);
            } else if (value) $('#'+key).html('<img src="'+value+'">')
          });
        }
      });
    }
  }

  $('.bcve-add-element').click(function(){ addElement($(this));});

  $('#bcve-container').find( ".bcve-copy-cell" ).on("click",cloneLayoutItem).end()
      .find( ".bcve-close-cell" ).on("click",closeLayoutItem).end()
      .find( ".bcve-edit-cell" ).on("click",editLayoutItem).end()
      .find( ".bcve-shrink-cell" ).on("click",shrinkCell).end()
      .find( ".bcve-expand-cell" ).on("click",expandCell).end()
  $( "#bcve-container" ).disableSelection();  // Turn off Selections
  $(".bcve-editor").each(function(){ makeSortable($(this)); })

  function processShortCode() {
    $('#bcve-processed').html($('.bcve-shortcode-data').clone().contents().append());
    $("#content").val($('#bcve-processed').html());
  }

  function highlightAcceptableLocations(ui) {
    $(".bcve-element-content").css("background-color","White");
    var sc = ui.helper.attr("data-shortcode");
      if (bcve.sc[sc]['parent_element']) {
        $("."+bcve.sc[sc]['parent_element']+"_content").css("background-color","#f0f8ff");
      } else if ((bcve.sc[sc]['type']) === 'media') {
        $(".bcorp_cell_content").css("background-color","#e0f0ff");
        $(".bcorp_row_content").css("background-color","#e5f3ff");
        $(".bcorp_section_content").css("background-color","#eaf5ff");
        $("#bcve-container").css("background-color","#f0f8ff");
      } else if (sc === 'bcorp_cell') {
        $(".bcorp_row_content").css("background-color","#e5f3ff");
        $(".bcorp_section_content").css("background-color","#eaf5ff");
        $("#bcve-container").css("background-color","#f0f8ff");
      } else if (sc === 'bcorp_row') {
        $(".bcorp_section_content").css("background-color","#eaf5ff");
        $("#bcve-container").css("background-color","#f0f8ff");
      } else if (sc === 'bcorp_section') {
        $("#bcve-container").css("background-color","#f0f8ff");
      }
  }

  function makeSortable($my_sortable) {
    $my_sortable.sortable({
      placeholder: "bcve-placeholder",
      helper: "clone",
      connectWith: ".bcve-editor",
      cursor: "move",
      tolerance: "pointer",
      cursorAt: { top: 0, left: 80 },
      receive: function( event, ui ) {
        var sc = $(this).attr("data-shortcode");
        if ((bcve.sc[sc]['child_element']) || (bcve.sc[helper_sc]['only_child'])){
          if (bcve.sc[sc]['child_element'] != helper_sc) $(ui.sender).sortable('cancel');
        } else if ((sc === 'bcorp_cell' && (helper_sc === 'bcorp_cell' || helper_sc === 'bcorp_row' || helper_sc === 'bcorp_section')) ||    // Cannot drag cells,rows and sections into a cell
                  (sc === 'bcorp_row' && ( helper_sc === 'bcorp_row' || helper_sc === 'bcorp_section')) ||   // Cannot drag rows and sections into a row
                  (sc === 'bcorp_section' && helper_sc === 'bcorp_section') //||   // Cannot drag sections into a section
                ) $(ui.sender).sortable('cancel'); else {
          var append = true;
          $(this).children().each(function(){
              var position = $(this).offset();
              if (append && ((ui.offset.top < position.top) || ((ui.offset.left < position.left) && (ui.offset.top < parseInt(position.top+$(this).height()-50)))))
              {
                $(this).before(ui.item);
                append = false;
              }
          })
          if (append) $(this).append(ui.item);
        }
      },
      start: function ( event, ui) {
        highlightAcceptableLocations(ui);
        ui.helper.removeClass().addClass("bcve-helper");
      },
      stop: function (event,ui) { processShortCode(); },
      update: function ( event, ui) { },
      beforeStop: function ( event, ui) {
        $(".bcve-editor").css("background-color","white");
      },
      over: function( event, ui ) {
        highlightAcceptableLocations(ui);
        var sc = $(this).attr("data-shortcode");
        helper_sc = $('.bcve-helper').attr("data-shortcode");
        if (sc=='bcorp_section') { if(!bcve.sc[helper_sc]['only_child']); $(this).css("background-color", "#f7fff2"); }
        else if ((bcve.sc[sc]['child_element']) || (bcve.sc[helper_sc]['only_child'])){
          if (bcve.sc[sc]['child_element'] === helper_sc) $(this).css("background-color", "#f8fff0");
        } else if ((sc === 'bcorp_cell' && (helper_sc === 'bcorp_cell' || helper_sc === 'bcorp_row' || helper_sc === 'bcorp_section')) ||    // Cannot drag cells,rows and sections into a cell
                   (sc === 'bcorp_row' && ( helper_sc === 'bcorp_row' || helper_sc === 'bcorp_section')) ||   // Cannot drag rows and sections into a row
                   (sc === 'bcorp_section' && helper_sc === 'bcorp_section')     // Cannot drag sections into a section
                 ){

                 } else{
                   $(this).css("background-color", "#f7fff2");
                 }
      },
      out: function( event, ui ) { },
      change: function( event, ui ) { },
    })
  }

  function chooseElement($this) {
    var parent_sc = $this.parent().parent().attr("data-shortcode");
    var parent_type = $this.parent().parent().attr("data-element-type");
    if (!parent_sc) {  // Cannot get from parent so get from button
      parent_sc = $this.attr("data-shortcode");
      parent_type = $this.attr("data-element-type");
    }
    var elements_dialog_html = '<div class="bcve-dialog-header"><h2>Choose Element</h2><span class="bcve-dialog-cancel dashicons dashicons-no"></span></div><div class="bcve-element-list">';

    for (var key in bcve.sc) {
      if (!bcve.sc[key]['only_child']) {
        if (key == "bcorp_cell") {
          if (parent_sc == 'bcorp_section' || parent_sc == 'bcorp_row')
          {
            var icons = ["&#xe811;","&#xe812;","&#xe813;","&#xe814;","&#xe815;","&#xe816;","&#xe817;","&#xe818;","&#xe819;","&#xe81A;","&#xe81B;","&#xe81C;"];
            var i =0;
            for (var width in bcve.width_titles) {
              elements_dialog_html += '<a class="bcve-editor-button ' + bcve.sc[key]['type'] + '" data-shortcode="' + key + '" data-width="' + width + '"><i class="bcve-icon bcve-select-icon-icon">'+icons[i]+'</i><br />' + bcve.width_titles[width] + '</a>';
              i++;
            }
          }
        } else {
           if (!((key == 'bcorp_section' && parent_type != 'first') || (key == 'bcorp_row' && parent_sc != 'bcorp_section'))) {
             if (bcve.sc[key]['admin_icon'])  elements_dialog_html += '<a class="bcve-editor-button ' + bcve.sc[key]['type'] + '" data-shortcode="' + key + '" data-width="' + bcve.sc[key]['width'] + '"><i class="bcve-icon bcve-select-icon-icon">'+bcve.sc[key]['admin_icon']+'</i><br />'  + bcve.sc[key]['title'] + '</a>';
             else elements_dialog_html += '<a class="bcve-editor-button ' + bcve.sc[key]['type'] + '" data-shortcode="' + key + '" data-width="' + bcve.sc[key]['width'] + '"><img src="' + bcve.icons_url + key + '.png"></img><br />' + bcve.sc[key]['title'] + '</a>';
           }
        }
      }
    }
    elements_dialog_html += '</div>';

    var elements_dialog_id = '#bcve-dialog';
    var $element_selection_page = $('#bcve-dialog').html(elements_dialog_html);
    $element_selection_page.find('.bcve-editor-button').click(function() {
      closeElementsDialog(elements_dialog_id);
      if ($this.attr("data-element-type") == "first") {
        $this=$(this).clone().prependTo($('#bcve-container'));
      } else if ($this.attr("data-element-type") == "top") {
          $this=$(this).clone().prependTo($this.parent().next());
      } else {
         $this=$(this).clone().insertAfter($this.parent());
      }
      createElement($this);
      processShortCode();
    });

    $("#bcve-dialog-overlay").click(function(){closeElementsDialog(elements_dialog_id)});
    $("#bcve-dialog-overlay").css({"display":"block",opacity:0});
    $("#bcve-dialog-overlay").fadeTo(200,0.3);
    $(elements_dialog_id).css({"display":"block"});
    $(elements_dialog_id).removeClass('zoomOut').addClass('zoomIn');
    $(elements_dialog_id+" .bcve-dialog-cancel").click(function(){ closeElementsDialog(elements_dialog_id); });
    function closeElementsDialog(elements_dialog_id){
      $("#bcve-dialog-overlay").fadeOut(200);
      $(elements_dialog_id).addClass('zoomOut');
      setTimeout(function(){ $(elements_dialog_id).css({"display":"none"}); }, 200);
    }
  }

  function addElement($this) {
    if ($this.attr("data-element-type") == "child_top") {
      $this=$this.clone().prependTo($this.parent().next());
      createElement($this);
      processShortCode();
    } else if ($this.attr("data-element-type") == "child") {
      $this=$this.clone().insertAfter($this.parent());
      createElement($this);
      processShortCode();
    } else chooseElement($this);
  }

  function createElement ($this) {
    var sc = $this.attr("data-shortcode");
    var cell_width = $this.attr("data-width");
    if (sc == "bcorp_cell") {
      var full_sc = '['+sc+' width="'+cell_width+'"]';
    } else var full_sc = '['+sc+']';
    var element = startElement(sc,cell_width,full_sc,'temp-bcve-element') + endElement(sc,cell_width);
    $this.replaceWith(element);
    $this = $(".temp-bcve-element").removeClass('temp-bcve-element');
    addLayoutEvents($this);
  }

  function startElement(sc,cell_width,sc_full,tempclass) {
    var element = '<div class="bcve-element '+tempclass+' ' + bcve.sc[sc]['type'] +' bcorp-'+ cell_width +'" data-width="'+ cell_width +'" data-shortcode="' + sc + '"><div class="bcve-element-frame"><div class="bcve-element-header"></span><span class="bcve-close-cell dashicons dashicons-no"></span>';
    if (sc == "bcorp_cell") element += '<span class="bcve-shrink-cell dashicons dashicons-arrow-left-alt2"></span>';
    element += '<span class="bcve-cell-title">';
    if (sc == "bcorp_cell") element += bcve.width_titles[cell_width]; else element += bcve.sc[sc]['title'];
    element += '</span><span class="bcve-copy-cell dashicons dashicons-admin-page"></span>';
    element += '<span class="bcve-edit-cell dashicons dashicons-edit"></span>';
    if (sc == "bcorp_cell") element += '<span class="bcve-expand-cell dashicons dashicons-arrow-right-alt2"></span>'
    element += '</div><div class="bcve-element-shortcode">' + sc +'</div><div class="bcve-shortcode-data">';
    element += sc_full;
    element +='</div><div class="bcve-admin-content">' + bcve.sc[sc]['admin_default'];
    if (bcve.sc[sc]['child_element']) element += '<button class="bcve-add-element" data-element-type="child_top" data-shortcode="'+bcve.sc[sc]['child_element']+'" data-width="'+bcve.sc[bcve.sc[sc]['child_element']]['width']+'" type="button"><span class="dashicons dashicons-plus"></span></button>';
    if (sc == "bcorp_cell" || sc == "bcorp_row" || sc == "bcorp_section") element += '<button class="bcve-add-element" data-element-type="top" data-shortcode="'+sc+'" data-width="'+cell_width+'" type="button"><span class="dashicons dashicons-plus"></span></button>';
    element += '</div>';
    if (bcve.sc[sc]['closing_tag']) {
      element += '<div class="bcve-element-content ';
      if (bcve.sc[sc]['accept_content']) element += "bcve-editor ";
      element += ' ' + sc + '_content" data-shortcode="' + sc + '">';
    }
    return element;
  }

  function endElement(sc,cell_width) {
    var element = '';
    if (bcve.sc[sc]['closing_tag']) element += '</div><div class="bcve-shortcode-data">[/' + sc + ']</div>';
    element += '</div>';
    if (sc == "bcorp_cell") element += '<div class="bcve-cell-width"><div class="bcve-cell-width-left"></div><div class="bcve-cell-width-right"></div><div class="bcve-cell-width-line"></div><div class="bcve-cell-width-value"><span>' + bcve.width_titles[cell_width] +'</span></div></div>';
    element += '<button class="bcve-add-element" data-element-type="';
    if (bcve.sc[sc]['only_child']) element += 'child'; else element+= 'new';
    element += '" data-shortcode="'+sc+'" data-width="'+bcve.sc[sc]['width']+'" type="button"><span class="dashicons dashicons-plus"></span></button></div>';
    return element;
  }

  function addLayoutEvents ($item) {
    $item.find( ".bcve-copy-cell" ).on("click",cloneLayoutItem).end()
      .find( ".bcve-close-cell" ).on("click",closeLayoutItem).end()
      .find( ".bcve-edit-cell" ).on("click",editLayoutItem).end()
      .find( ".bcve-shrink-cell" ).on("click",shrinkCell).end()
      .find( ".bcve-expand-cell" ).on("click",expandCell).end();
    if ($item.find( ".bcve-element-content" ).is(".bcve-editor")) makeSortable( $item.find( ".bcve-element-content" ) ); // Only Make it a sortable if it has class bcve-editor
    $item.find('.bcve-add-element').click(function(){ addElement($(this));});
  };

  function shrinkCell () {
    var $editedElement = $(this).parent().parent().parent();
    var old_cell_width = $editedElement.attr('data-width');
    var new_cell_width;
    for (var key in bcve.width_titles)
    {
      if (key == old_cell_width && key != '1-6') {
          $editedElement.find('.bcve-cell-title:first').html(bcve.width_titles[new_cell_width]);
          $editedElement.find('.bcve-cell-width-value:first').html('<span>'+bcve.width_titles[new_cell_width]+'</span>');
          var mobilewidth = getSCVariableValue('bcorp_cell','mobilewidth',$editedElement.find('.bcve-shortcode-data:first').html());
          $editedElement.find('.bcve-shortcode-data:first').html('[bcorp_cell width="'+new_cell_width+'" mobilewidth="'+mobilewidth+'"]');
          $editedElement.removeClass('bcorp-'+old_cell_width).addClass('bcorp-'+new_cell_width).attr('data-width',new_cell_width);
          processShortCode();
      }
      new_cell_width = key;
    }
  }

  function expandCell () {
    var $editedElement = $(this).parent().parent().parent();
    var old_cell_width = $editedElement.attr('data-width');
    var new_cell_width;
    for (var key in bcve.width_titles)
    {
      if (new_cell_width) {
          $editedElement.find('.bcve-cell-title:first').html(bcve.width_titles[key]);
          $editedElement.find('.bcve-cell-width-value:first').html('<span>'+bcve.width_titles[key]+'</span>');
          var mobilewidth = getSCVariableValue('bcorp_cell','mobilewidth',$editedElement.find('.bcve-shortcode-data:first').html());
          $editedElement.find('.bcve-shortcode-data:first').html('[bcorp_cell width="'+key+'" mobilewidth="'+mobilewidth+'"]');
          $editedElement.removeClass('bcorp-'+old_cell_width).addClass('bcorp-'+key).attr('data-width',key);
          processShortCode();
      }
      if (key == old_cell_width && key != '1-1') {
          new_cell_width = key;
      } else new_cell_width = '';
    }
  }

  function cloneLayoutItem() {
    addLayoutEvents($( this ).parent().parent().parent().clone().insertAfter($( this ).parent().parent().parent()));
    processShortCode();
  }

  function closeLayoutItem() {
    var $element = $( this ).parent().parent().parent().addClass('zoomOut');;
    $( this ).parent().parent().parent().addClass('animated zoomOut');
    setTimeout(function(){ $element.remove(); processShortCode(); }, 300);
  }

  function editLayoutItem() {
    var $editedElement = $( this ).parent().parent().parent();
    var sc = $editedElement.find('.bcve-element-shortcode:first').text();
    $("#bcve-dialog-overlay").click(function(){closeModal(modal_id)});
    $("#bcve-dialog").html(editElementHTML($editedElement));
    if ($('#bcve-rich-text-editor').length > 0) {
      var mytext = $('#bcve-rich-text-editor').html()
      $('#bcve-rich-text-editor').html($('#wp-bcve-text-editor-wrap'));
      tinymce.EditorManager.execCommand('mceAddEditor',true, 'bcve-text-editor');
      $('#wp-bcve-text-editor-wrap').removeClass('html-active').addClass('tmce-active');
      if (tinymce.get('bcve-text-editor') === null) setTimeout(function(){ tinymce.get('bcve-text-editor').setContent(mytext); }, 200);
      else tinymce.get('bcve-text-editor').setContent(mytext);
      $('#wp-bcve-text-editor-wrap').show();
    }
    $('.bcve-dialog-cancel').click(function(){closeModal(modal_id)});
    $('.bcve-dialog-save').click(function(){saveModal(modal_id)});
    var modal_id = '#bcve-dialog';
    $("#bcve-dialog-overlay").css({"display":"block",opacity:0});
    $("#bcve-dialog-overlay").fadeTo(200,0.7);
    $(modal_id).css({"display":"block"});
    $(modal_id).removeClass('zoomOut').addClass('zoomIn');
    function closeModal(modal_id){
      $("#bcve-dialog-overlay").fadeOut(200);
      $(modal_id).addClass('zoomOut');
      setTimeout(function(){ $(modal_id).css({"display":"none"}); }, 200);
      if ($('#bcve-rich-text-editor').length > 0) {
        $('#bcve-text-editor-html').trigger('click');
        $('#wp-bcve-text-editor-wrap').hide();
        tinymce.EditorManager.execCommand('mceRemoveEditor',false, 'bcve-text-editor');
        $('#bcorp-visual-editor-meta').after($('#wp-bcve-text-editor-wrap'));
      }
    }
    function saveModal(modal_id){
      processElement($editedElement);
      closeModal(modal_id);
      processShortCode();
    }
    tabs();
    triggerEditButtons();
  }

  function triggerEditButtons () {
    $('button.bcve-select-image').unbind("click");
    $('button.bcve-clear-image').unbind("click");
    $('button.bcve-insert-video').unbind("click");
    $('button.bcve-select-icon').unbind("click");
    $('button.bcve-select-gallery').unbind("click");
    $('.bcve-color-picker').unbind();

    $('button.bcve-iris-color-picker-button').unbind("click");
    $('button.bcve-iris-color-picker-clear-color').unbind("click");
    $('.bcve-dependent').unbind("change");

    $('.bcve-select-image').click(selectImage);
    $('.bcve-clear-image').click(clearImage);
    $('button.bcve-insert-video').click(chooseVideo);
    $('button.bcve-select-icon').click(chooseIcon);
    $('.bcve-select-gallery').click(selectGallery);

    $('.bcve-color-picker').each(function(){
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
    $('.bcve-color-picker').click(showColorPicker);
    $('button.bcve-iris-color-picker-clear-color').click(clearColor);
    $('button.bcve-iris-color-picker-button').click(toggleColorPicker);

    $('.bcve-dependent').change(dependentDropDown);
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
    var id = $(this).attr("bcve-key");
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
      $('#bcve-'+id+'-placeholder').html('<img src="'+thumb_url+'">');
      $('#bcve-'+id).val(attachment.attributes.id );
    });
    frame.open();
  }

  function clearImage() {
    var id = $(this).attr("bcve-key");
    $('#bcve-'+id+'-placeholder').html('<div class="bcve-image-placeholder">&#xe804;</div>');
    $('#bcve-'+id).val("");
  }
  function chooseIcon() {
    var id = this.id
    var iconpage = '<div class="bcve-dialog-header"><h2>Select Icon</h2><span class="bcve-dialog-cancel dashicons dashicons-no"></span></div><div class="bcve-icon-list">';
    var missingicons = [61455,61471,61472,61503,61519,61535,61551,61567,61583,61599,61615,61619,61620,61621,61622,61623,61624,61625,61626,61627,61628,61629,61630,61631,61647,61663,61679,61695,61711,61718,61719,61727,61743,61759,61775,61791,61807,61823,61839,61855,61871,61887,61903,61919,61935,61951,61967,61983,61984,61998,61999,62015,62031,62047,62063,62079,62095]
    for (var i = 61440; i < 62102; i++) {
      while(missingicons.indexOf(i) > -1) { i++; }
      iconpage += '<span class="bcve-icons" data-key="'+i.toString(16)+'" data-icon="&#x'+ i.toString(16)+';"></span>';
    }
    iconpage += '</div>';

    var icon_dialog_id = '#bcve-dialog-2';
    var $icon_selection_page = $(icon_dialog_id).html(iconpage);

    $icon_selection_page.find('.bcve-icons').click(function() {
      var icon_value = $(this).attr('data-key');
      $(icon_dialog_id).addClass('zoomOut');
      setTimeout(function(){ $(icon_dialog_id).css({"display":"none"}); }, 200);
      $('#'+id+'-placeholder').html('<span data-icon="&#x'+ icon_value+';"></span>');
      $('#'+id).val(icon_value);
    });
    $("#bcve-dialog-overlay").click(function(){ $(icon_dialog_id).css({"display":"none"}); });
    $(icon_dialog_id+" .bcve-dialog-cancel").click(function(){
      $(icon_dialog_id).addClass('zoomOut');
      setTimeout(function(){ $(icon_dialog_id).css({"display":"none"}); }, 200);
    });
    $(icon_dialog_id).css({"display":"block"});
    $(icon_dialog_id).removeClass('zoomOut').addClass('zoomIn');
  }

  function selectGallery() {
    var id = $(this).attr("bcve-key");
    var selection = loadGalleryImages($('#bcve-ids').val());
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
      jQuery("#bcve-ids").val(ids.join(","));
      $('#bcve-'+id+'-placeholder').html(galleryhtml);
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
    var key = $(this).attr("bcve-key");
    var sc = $(this).attr("data-shortcode");
    var scv='';
    var param = bcve.sc[sc]['variables'][key];
    if (param['type'] != 'checkbox') var value = $(this).val(); else if ($(this).attr('checked')) value=true; else value=false;
    var dependentHTML = '';
    for (var dependent in param['dependents'][value]) dependentHTML += addParam(param['dependents'][value][dependent],sc,scv);
    $(".bcve-dependent-"+id).html(dependentHTML);
    triggerEditButtons();
  }

  function addParam(key,sc,scv,$editedElement){
    var param = bcve.sc[sc]['variables'][key];
    var shortcode_value = getSCVariableValue(sc,key,scv);
    if (param.hasOwnProperty("dependents")) var dependent = ' class="bcve-dependent" bcve-key="' + key +'" data-shortcode="' + sc + '" '; else var dependent = '';
    switch (param['type']) {
      case "icon":
        if (shortcode_value) var bcorp_icon = '&#x' + shortcode_value + ';'; else var bcorp_icon = '&#61445;';
        var dialogHTML = '<div class="bcve-icon-placeholder" id="bcve-' + key + '-placeholder"><span data-icon="'+ bcorp_icon + '"></span></div>';
        dialogHTML += '<input type="hidden" id="bcve-' + key +'" value="'+ shortcode_value + '"><button id="bcve-' + key + '" class="bcve-select-icon" type="button">Select Icon</button><br />';
        break;
      case "textfield":
        var dialogHTML = '<label>'+ param['name'] +' </label><br /><input class="bcve-dialog-textfield" type="text" id="bcve-' + key +'" value="'+ shortcode_value + '"><br />';
        break;
      case "color":
        dialogHTML = '<label>'+ param['name'] +' </label><br /><button class="bcve-iris-color-picker-button"><div id="bcve-'+key+'-iris-swatch" class="bcve-iris-color-picker-swatch" style="background-color:'+shortcode_value+';"></div>Select Color</button><input class="bcve-color-picker" type="text" id="bcve-' + key +'" value="'+ shortcode_value + '"><button class="bcve-iris-color-picker-clear-color" type="button">Clear</button>';
        dialogHTML += '<div class="bcve-iris-color-picker-container"><div class="bcve-iris-color-picker-placeholder" id="bcve-'+key+'-iris-placeholder"></div></div>';
        break;
      case "image":
        var $imagePlaceHolder = $editedElement.find(".bcve-"+sc+'-'+key).html();
        var dialogHTML = '<div class="bcve-image-selector"><div id="bcve-' + key + '-placeholder">' + $imagePlaceHolder + '</div>';
        dialogHTML += '<input type="hidden" id="bcve-' + key + '" value="'+ shortcode_value + '">';
        dialogHTML += '<button class="bcve-select-image" bcve-key="' + key + '" type="button">Select</button><button class="bcve-clear-image" bcve-key="' + key + '" type="button">Clear</button></div>';
        break;
      case "video":
        var dialogHTML = '<label>'+ param['name'] +' </label><br /><input class="bcve-dialog-textfield" type="text" id="bcve-' + key +'" value="'+ shortcode_value + '"><br />';
        dialogHTML += '<button id="bcve-' + key + '" class="bcve-insert-video" type="button">Insert Video</button><br />';
        break;
      case "gallery":
        var $galleryPlaceHolder = $editedElement.find(".bcve-"+sc+'-'+key).html();
        var dialogHTML = '<div id="bcve-' + key + '-placeholder" class="bcve-gallery-placeholder">' + $galleryPlaceHolder +'</div><button class="bcve-select-gallery" bcve-key="' + key + '" type="button">Select Gallery</button><br />';
        dialogHTML += '<input type="hidden" id="bcve-' + key +'" value="'+ shortcode_value + '">';
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
        if (typeof param['values'] === 'string') param['values'] = bcve.vars[param['values']];
        var dialogHTML = '<label>'+ param['name'] +' </label><select id="bcve-' + key +'"' + selectmultiple + dependent + '>';
        for (var option in param['values']) {
          if (shortcode_values.indexOf(option) >-1 ) var selected=' selected'; else var selected = '';
          dialogHTML += '<option' + selected + ' value="'+ option +'">'  + param['values'][option] + '</option>';
        }
        dialogHTML += '</select><br />';
        break;
      case "checkbox":
        var dialogHTML = '<input id="bcve-' + key +'" type="checkbox"';
        if (shortcode_value === 'true') dialogHTML += ' checked' ;
        dialogHTML += dependent + '><label>'+ param['name'] +' </label><br />';
        break;
      case "textarea":
        if ($editedElement.find('.bcve-shortcode-data').length === 3) {
          var textBlockText = unescape($editedElement.find('.bcve-shortcode-data:eq(1)').html());
        } else var textBlockText = '';
        var dialogHTML = '<label>'+ param['name'] +' </label><br />';
        if (param['editor'] == 'code') dialogHTML += '<textarea id="textblock" class="bcve-code-area">' + textBlockText + '</textarea>';
        else dialogHTML +='<div id="bcve-rich-text-editor">' + textBlockText + '</div>';
        break;
    }
    if (param['description']) dialogHTML += '<div class="bcve-description">' + param['description'] + '</div>';
    dialogHTML += '<br />';
    if (param.hasOwnProperty("dependents")) {
      var dependents = param['dependents'][shortcode_value];
      dialogHTML += '<div class="bcve-dependent-bcve-'+key+'">';
      for (var dependent in dependents) {
        dialogHTML += addParam(dependents[dependent],sc,scv,$editedElement);
      }
      dialogHTML += '</div>';
    }
    return dialogHTML;
  }

  function editElementHTML($editedElement) {
    var sc = $editedElement.find('.bcve-element-shortcode:first').text();
    var scv = $editedElement.find('.bcve-shortcode-data:first').text();
    var dialogHTML = '<div class="bcve-dialog-tab"><ul>';
    var dialogTab = [];
    for (var key in bcve.sc[sc]['variables']) {
      if (bcve.sc[sc]['variables'][key]['isdependent']!==true) {
        if (bcve.sc[sc]['variables'][key]['admin_tab'] == undefined) var admin_tab = bcve.sc[sc]['title'];
          else var admin_tab = bcve.sc[sc]['variables'][key]['admin_tab'];
        if (dialogTab[admin_tab] == undefined) dialogTab[admin_tab] = '';
        dialogTab[admin_tab] += addParam(key,sc,scv,$editedElement);
      }
    }

    var tabindex=1;
    for (var key in dialogTab){
      dialogHTML += '<li><a href="#bcve-tab-'+tabindex+'">'+key+'</a></li>';
      tabindex++;
    }
    dialogHTML += '</ul>';
    dialogHTML += '<span class="bcve-dialog-cancel dashicons dashicons-no"></span>';
    tabindex=1;
    for (var key in dialogTab){
      dialogHTML += '<div class="bcve-dialog-tabbed" id="bcve-tab-'+tabindex+'">';
      dialogHTML += dialogTab[key];
      dialogHTML += '</div>';
      tabindex++;
    }
  dialogHTML += '</div>';
  dialogHTML +='<div class="bcve-dialog-buttons"><button class="bcve-dialog-save button button-primary button-large">Save</button></div>';
  return dialogHTML;
  }

	function tabs() {
		$('.bcve-dialog-tab').each(function(){
			$(this).find('li:first').addClass('bcorp-active');
			$(this).find('.bcve-dialog-tabbed').hide();
			$(this).find('.bcve-dialog-tabbed:first').show();

			$(this).find('li').click(function(){
				if(!$(this).hasClass('bcorp-active')){
					$(this).parent().find('li').removeClass('bcorp-active');
					$(this).addClass('bcorp-active');
					$(this).parent().parent().find('.bcve-dialog-tabbed').hide();
					$($(this).find('a').attr('href')).show().find("div:first-child").hide().fadeIn();
          $('.bcve-color-picker').iris('hide');
				}
				return false;
			});
		});
	}

  function processElement($editedElement) {
    var sc = $editedElement.find('.bcve-element-shortcode:first').text();
    $editedElement.find('.bcve-admin-content:first > div').removeClass().addClass("bcve-"+sc);
    var mysetting = "";
    for (var key in bcve.sc[sc]['variables']) {
      var param = bcve.sc[sc]['variables'][key];
      if (sc === 'bcorp_cell' && key == 'width') {
        var cell_width = $('#bcorp_width').val();
        var old_cell_width = $editedElement.attr('data-width');
        if (cell_width != old_cell_width) {
          $editedElement.find('.bcve-cell-title:first').html(bcve.width_titles[cell_width]);
          $editedElement.removeClass(old_cell_width).addClass(cell_width).attr('data-width',cell_width);
        }
      }
      var mysc = "#bcve-" + key;
      if (param['type'] == 'textarea') {
        if (param['editor'] === 'code') {
          var textBlockText = $('#textblock').val();
          $editedElement.find(".bcve-"+sc+'-'+key).html(escapeHtml(textBlockText));
        } else {
          var textBlockText = tinymce.activeEditor.getContent();
          $editedElement.find(".bcve-"+sc+'-'+key).html(textBlockText);
        }
        var $elementContent = $editedElement.find (".bcve-element-content");
        $elementContent.html('<div class="bcve-shortcode-data">' + textBlockText + '</div>');
        $elementContent.find('.bcve-shortcode-data').hide();
      } else {
        if (param['type'] != 'checkbox') var myvalue = $(mysc).val();
        else if ($(mysc).attr('checked')) myvalue=true; else myvalue=false;
        if ((typeof myvalue != 'undefined') && (myvalue != null)) {
          mysetting += " " + key + '="' + myvalue + '"';
          if (param['type'] == 'icon') $editedElement.find(".bcve-"+sc+'-'+key).attr('data-icon',String.fromCharCode('0x'+myvalue));
          else {
            if ((param['type'] == 'image') || (param['type'] == 'gallery')) myvalue = $("#bcve-" + key + "-placeholder").html();
            $editedElement.find(".bcve-"+sc+'-'+key).html(myvalue);
          }
          if (param['admin_class'] === true) $editedElement.find('.bcve-admin-content > div').addClass("bcve-"+sc+"-"+key+"-"+myvalue);
        }
      }
    }
    $editedElement.find('.bcve-shortcode-data:first').html('[' + sc + mysetting + ']');
  }

  // Provide a variable and complete shortcode and be returned its value
  function getSCVariableValue(sc,sc_variable,full_sc) {
    // Add code to accept variable spaces //
    var split_variable = new RegExp( ' '+sc_variable+ '="([^"]+)', 'i');
    var variableValue = split_variable.exec( full_sc );
    if (variableValue) return variableValue[1]; else return bcve.sc[sc]['variables'][sc_variable]['default'];
  }

  function escapeHtml(text) {
    var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }

});
