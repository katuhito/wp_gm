<?php

class BCorp_SC_Data {
  public function __construct ()
  {
    $this->bcsc_setup();
  }

  public function bcsc() { return $this->bcsc_shortcodes; }

  public function bcorp_sanitize_data($tag,$atts) {
    $data=array();
    $variables=array();
    foreach ($this->bcsc_shortcodes[$tag]["variables"] as $key => $value) {
      if (is_string($value)) foreach ($this->bcsc_vars['commonvars'][$value] as $commonkey => $commonvalue) $variables[$commonkey]=$commonvalue;
      else $variables[$key]=$value;
      if (!isset($variables[$key]['default'])) $variables[$key]['default']='';
    }
    foreach ($variables as $key => $value){ if (isset($atts[$key])) $data[$key]=$atts[$key]; else $data[$key]=$variables[$key]['default']; }

    foreach ($variables as $key => $value) {
      switch ($variables[$key]['type']){
        case 'checkbox':
          if (!in_array( $data[$key], array('true','false'))) $data[$key] = $variables[$key]['type'];
          break;
        case 'dropdown':
          if (isset($variables[$key]['selectmultiple'])) {
          } else {
            if (is_string($variables[$key]['values'])) {
             if (isset($this->bcsc_vars[$variables[$key]['values']]) && !key_exists( $data[$key], $this->bcsc_vars[$variables[$key]['values']])) $data[$key] = $variables[$key]['default'];
           } elseif (!key_exists( $data[$key], $variables[$key]['values'])) $data[$key] = $variables[$key]['default'];
         }
        case 'textfield':
          if(isset($variables[$key]['units'])) {
            if ($data[$key]=='') {
              $data[$key] = $variables[$key]['default'];
            } else {
              $value = intval($data[$key]);
              $units = preg_replace('/[0-9]+/', '', $data[$key]);
              if (!in_array( $units, $variables[$key]['units'])) $units = $variables[$key]['units'][0];
              $data[$key] = esc_attr($value.$units);
            }
          } else {
            $data[$key] = esc_attr($data[$key]);
          }
          break;
        case 'textarea':
          $data[$key] = esc_textarea($data[$key]);
          break;
        default:
          $data[$key] = esc_attr($data[$key]);
          break;
      }
    }
    return $data;
  }

  private $bcsc_shortcodes = array();
  private $bcsc_vars = array();
  private $bcsc_shortcode_defaults = array();

  public function bcsc_vars() { return $this->bcsc_vars; }

  public function bcorp_add_shortcode($tag,$variables,$plugin_instance = "bcorp_shortcodes") {
    $this->bcsc_shortcodes[$tag]=$this->bcsc_shortcode_defaults;
    $this->bcsc_shortcodes[$tag]["shortcode"]=$tag;
    $this->bcsc_shortcodes[$tag]["title"]=$tag;
    foreach ($variables as $key => $value) $this->bcsc_shortcodes[$tag][$key]=$value;
    add_shortcode($tag,array(&$GLOBALS[$plugin_instance],$tag.'_shortcode'));
  }

  public function bcorp_add_shortcode_var($var,$value) {
    $this->bcsc_vars[$var]=$value;
  }

  private function bcsc_setup () {
    $this->bcsc_shortcode_defaults = array(
      "type"=>"media",
      "width"=>"1-1",
      "accept_content"=>false,
      "closing_tag"=>true,
      "only_child"=>false,
      "variables"=>array(),
      "admin_default"=>'<h1 style="text-align:center">Default</h1>',
      "child_element"=>'',
    );

    /* Setup Common Variables Once */

    $this->bcsc_vars['widths']=array("1-6"=>"1/6","1-5"=>"1/5","1-4"=>"1/4","1-3"=>"1/3","2-5"=>"2/5","1-2"=>"1/2",
                         "3-5"=>"3/5","2-3"=>"2/3","3-4"=>"3/4","4-5"=>"4/5","5-6"=>"5/6","1-1"=>"1/1");
    $this->bcsc_vars['1to100all']['all']='All';
    $this->bcsc_vars['0to100']['0']='0';
    foreach (range(1, 100) as $number) {
      $this->bcsc_vars['1to100all'][(string)$number]=(string)$number;
      $this->bcsc_vars['0to100'][(string)$number]=(string)$number;
    }
    $terms = get_categories();
    foreach ( $terms as $term ) $this->bcsc_vars['categories'][$term->term_id ] = $term->name;
    $terms = get_tags();
    foreach ( $terms as $term ) $this->bcsc_vars['tags'][$term->term_id ] = $term->name;
    $terms = get_terms('post_format');
    foreach ( $terms as $term ) $this->bcsc_vars['post_formats'][$term->slug ] = $term->name;
    $this->bcsc_vars['post_formats']['standard'] = 'Standard';
    $terms = get_terms('portfolio-category');
    if (taxonomy_exists('portfolio-category')) {
      $terms = get_terms('portfolio-category');
      foreach ( $terms as $term ) $this->bcsc_vars['portfolios'][$term->slug ] = $term->name;
    }
    $terms = get_posts( array('post_type' => 'post', 'nopaging' => TRUE ));
    foreach ($terms as $term) $this->bcsc_vars['posts'][$term->ID] = $term->post_title;
    $terms = get_posts( array('post_type' => 'page', 'nopaging' => TRUE ));
    foreach ($terms as $term) $this->bcsc_vars['pages'][$term->ID] = $term->post_title;
    $terms = get_posts( array('post_type' => 'portfolio', 'nopaging' => TRUE ));
    foreach ($terms as $term) $this->bcsc_vars['portfolio'][$term->ID] = $term->post_title;

    global $_wp_additional_image_sizes;
    $sizes = array();
    foreach( get_intermediate_image_sizes() as $s ){
      $sizes[ $s ] = array( 0, 0 );
      if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
        $sizes[ $s ][0] = get_option( $s . '_size_w' );
        $sizes[ $s ][1] = get_option( $s . '_size_h' );
      }else{
        if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
          $sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
      }
    }
    foreach( $sizes as $size => $atts ){
      $this->bcsc_vars['bcorp_image_sizes'][$size] = $size . ' ' . implode( 'x', $atts );
    }
    $this->bcsc_vars['bcorp_image_sizes']['full'] = "Full Size Image";

    $this->bcsc_vars['commonvars']['idname']=array(
      'idname'=>array(
        'name'=>'ID Name',
        'admin_tab'=>'Extras',
        'description'=>'Optionally add an ID name to link to your element or refer to it in your CSS',
        'type'=>'textfield',
        'default'=>''),
      'class'=>array(
        'name'=>'Class Name',
        'admin_tab'=>'Extras',
        'description'=>'Optionally add a class name to refer to the element in your CSS',
        'type'=>'textfield',
        'default'=>''),
    );

    $this->bcsc_vars['commonvars']['align']=array(
      'align'=>array(
        'name'=>'Alignment',
        'type'=>'dropdown',
        'default'=>'center',
        'admin_class'=>true,
        'values'=>array(
          'left'=>'Left',
          'center'=>'Center',
          'right'=>'Right'))
    );

    $this->bcsc_vars['commonvars']['animation']=array(
      'animation'=>array(
        'name'=>'Animation',
        'type'=>'dropdown',
        'default'=>'none',
        'values'=>array(
          'none'=>'None',
          'bounce'=>'bounce',
          'flash'=>'flash',
          'pulse'=>'pulse',
          'rubberBand'=>'rubberBand',
          'shake'=>'shake',
          'swing'=>'swing',
          'tada'=>'tada',
          'wobble'=>'wobble',
          'jello'=>'jello',
          'bounceIn'=>'bounceIn',
          'bounceInDown'=>'bounceInDown',
          'bounceInLeft'=>'bounceInLeft',
          'bounceInRight'=>'bounceInRight',
          'bounceInUp'=>'bounceInUp',
          'bounceOut'=>'bounceOut',
          'bounceOutDown'=>'bounceOutDown',
          'bounceOutLeft'=>'bounceOutLeft',
          'bounceOutRight'=>'bounceOutRight',
          'bounceOutUp'=>'bounceOutUp',
          'fadeIn'=>'fadeIn',
          'fadeInDown'=>'fadeInDown',
          'fadeInDownBig'=>'fadeInDownBig',
          'fadeInLeft'=>'fadeInLeft',
          'fadeInLeftBig'=>'fadeInLeftBig',
          'fadeInRight'=>'fadeInRight',
          'fadeInRightBig'=>'fadeInRightBig',
          'fadeInUp'=>'fadeInUp',
          'fadeInUpBig'=>'fadeInUpBig',
          'fadeOut'=>'fadeOut',
          'fadeOutDown'=>'fadeOutDown',
          'fadeOutDownBig'=>'fadeOutDownBig',
          'fadeOutLeft'=>'fadeOutLeft',
          'fadeOutLeftBig'=>'fadeOutLeftBig',
          'fadeOutRight'=>'fadeOutRight',
          'fadeOutRightBig'=>'fadeOutRightBig',
          'fadeOutUp'=>'fadeOutUp',
          'fadeOutUpBig'=>'fadeOutUpBig',
          'flipInX'=>'flipInX',
          'flipInY'=>'flipInY',
          'flipOutX'=>'flipOutX',
          'flipOutY'=>'flipOutY',
          'lightSpeedIn'=>'lightSpeedIn',
          'lightSpeedOut'=>'lightSpeedOut',
          'rotateIn'=>'rotateIn',
          'rotateInDownLeft'=>'rotateInDownLeft',
          'rotateInDownRight'=>'rotateInDownRight',
          'rotateInUpLeft'=>'rotateInUpLeft',
          'rotateInUpRight'=>'rotateInUpRight',
          'rotateOut'=>'rotateOut',
          'rotateOutDownLeft'=>'rotateOutDownLeft',
          'rotateOutDownRight'=>'rotateOutDownRight',
          'rotateOutUpLeft'=>'rotateOutUpLeft',
          'rotateOutUpRight'=>'rotateOutUpRight',
          'hinge'=>'hinge',
          'rollIn'=>'rollIn',
          'rollOut'=>'rollOut',
          'zoomIn'=>'zoomIn',
          'zoomInDown'=>'zoomInDown',
          'zoomInLeft'=>'zoomInLeft',
          'zoomInRight'=>'zoomInRight',
          'zoomInUp'=>'zoomInUp',
          'zoomOut'=>'zoomOut',
          'zoomOutDown'=>'zoomOutDown',
          'zoomOutLeft'=>'zoomOutLeft',
          'zoomOutRight'=>'zoomOutRight',
          'zoomOutUp'=>'zoomOutUp',
          'slideInDown'=>'slideInDown',
          'slideInLeft'=>'slideInLeft',
          'slideInRight'=>'slideInRight',
          'slideInUp'=>'slideInUp',
          'slideOutDown'=>'slideOutDown',
          'slideOutLeft'=>'slideOutLeft',
          'slideOutRight'=>'slideOutRight',
          'slideOutUp'=>'slideOutUp',
        )
      )
    );

    $this->bcsc_vars['commonvars']['heading']=array(
      'heading'=>array(
        'name'=>'Heading Size',
        'type'=>'dropdown',
        'default'=>'h1',
        'admin_class'=>true,
        'dependents'=>array(
          'custom'=>array('headingsize')),
        'values'=>array(
          'custom'=>'Custom',
          'h1'=>'Heading 1',
          'h2'=>'Heading 2',
          'h3'=>'Heading 3',
          'h4'=>'Heading 4',
          'h5'=>'Heading 5',
          'h6'=>'Heading 6')),
      'headingsize'=>array(
        'name'=>'Custom Heading Size',
        'description'=>'Enter a size in px, em or %. Defaults to px if no unit is entered.',
        'type'=>'textfield',
        'units'=>array('px','em','%'),
        'default' =>'32px'),
    );

    $this->bcsc_vars['commonvars']['link']=array(
      'link'=>array(
        'name'=>'Link',
        'type'=>'dropdown',
        'dependents'=>array(
          'manual'=>array('linkurl','linktarget'),
          'section'=>array('linksection'),
          'post'=>array('linkpost','linktarget'),
          'page'=>array('linkpage','linktarget'),
          'portfolio'=>array('linkportfolio','linktarget'),
          'category'=>array('linkcategory','linktarget'),
          'tag'=>array('linktag','linktarget'),
          'portfoliocategory'=>array('linkportfoliocategory','linktarget'),
          'format'=>array('linkformat','linktarget')),
        'default'=>'none',
        'values'=>array(
          'none'=>'No Link',
          'manual'=>'Manual URL',
          'section'=>'Section Name',
          'post'=>'Post',
          'page'=>'Page',
          'portfolio'=>'Portfolio Entry',
          'category'=>'Category',
          'tag'=>'Tag',
          'portfoliocategory'=>'Portfolio Category',
          'format'=>'Post Format')),
      'linkurl'=>array(
        'name'=>'URL',
        'type'=>'textfield',
        'default' =>'http://'),
      'linksection'=>array(
        'name'=>'Section Name',
        'description'=>'Preset section names include next, previous, top, bottom.',
        'type'=>'textfield',
        'default' =>''),
      'linkpost'=>array(
        'name'=>'Link to Post',
        'type'=>'dropdown',
        'values'=>'posts',
        'default' =>''),
      'linkpage'=>array(
        'name'=>'Link to Page',
        'type'=>'dropdown',
        'values'=>'pages',
        'default' =>''),
      'linkportfolio'=>array(
        'name'=>'Link to Portfolio Entry',
        'type'=>'dropdown',
        'values'=>'portfolio',
        'default' =>''),
      'linkcategory'=>array(
        'name'=>'Link to Category',
        'type'=>'dropdown',
        'values'=>'categories',
        'default' =>''),
      'linktag'=>array(
        'name'=>'Link to Tag',
        'type'=>'dropdown',
        'values'=>'tags',
        'default' =>''),
      'linkportfoliocategory'=>array(
        'name'=>'Link to Portfolio Category',
        'type'=>'dropdown',
        'values'=>'portfolios',
        'default' =>''),
      'linkformat'=>array(
        'name'=>'Link to Post Format',
        'type'=>'dropdown',
        'values'=>'post_formats',
        'default' =>''),
      'linktarget'=>array(
        'name'=>'Open Link in New Window',
        'type'=>'checkbox',
        'default'=>'false')
    );

    $this->bcsc_vars['commonvars']['margins']=array(
      'margintop'=>array(
        'name'=>'Top Margin',
        'admin_tab'=>'Margins',
        'units'=>array('px','em','%'),
        'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
        'type'=>'textfield',
        'default'=>''),
      'marginbottom'=>array(
        'name'=>'Bottom Margin',
        'admin_tab'=>'Margins',
        'units'=>array('px','em','%'),
        'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
        'default'=>'',
        'type'=>'textfield'),
    );

    $this->bcsc_vars['commonvars']['showicon']=array(
      'showicon'=>array(
        'name'=>'Display Icon',
        'admin_tab'=>'Icon',
        'type'=>'checkbox',
        'default'=>'false',
        'dependents'=>array(
        'true'=>array('icon','iconcolor'))),
      'iconcolor'=>array(
        'name'=>'Icon Color',
        'type'=>'color',
        'default'=>''),
      'icon'=>array(
        'name'=>'Icon',
        'type'=>'icon',
        'default'=>'f005')
    );

    $this->bcsc_vars['commonvars']['marginsall'] = $this->bcsc_vars['commonvars']['margins'];
    $this->bcsc_vars['commonvars']['marginsall']['marginleft']=array(
          'name'=>'Left Margin',
          'admin_tab'=>'Margins',
          'units'=>array('px','em','%'),
          'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
          'type'=>'textfield',
          'default'=>'');
    $this->bcsc_vars['commonvars']['marginsall']['marginright']=array(
          'name'=>'Right Margin',
          'admin_tab'=>'Margins',
          'units'=>array('px','em','%'),
          'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
          'type'=>'textfield',
          'default'=>'');

    /* Add Short Codes */
    global $bcorp_full_width_theme;
    if ($bcorp_full_width_theme) $this->bcorp_add_shortcode(
      "bcorp_section",array(
        "title"=>"Section",
        "admin_icon"=>"&#xe810;",
        "accept_content"=>true,
        "type"=>"section",
        "admin_default"=>'<div class="bcve-bcorp_section"><div class="bcve-bcorp_section-background"><div class="bcve-image-placeholder">&#xe804;</div></div>
        <div class="bcve-bcorp_section-details">Color Scheme: <span class="bcve-bcorp_section-colors">main</span><br />
          Scrolling: <span class="bcve-bcorp_section-effect">none</span><br />Size: <span class="bcve-bcorp_section-height">25</span><br />
          Section ID: <span class="bcve-bcorp_section-sectionid"></span><br />Video: <span class="bcve-bcorp_section-location">none</span><br /></div></div>',
        "variables"=>array(
          'colors'=>array(
            'name'=>'Color Scheme',
            'type'=>'dropdown',
            'default'=>'main',
            'values'=>array(
              'header'=>'Header',
              'main'=>'Main',
              'alt'=>'Alternate',
              'footer'=>'Footer',
              'base'=>'Base')),
          'background'=>array(
            'name'=>'Image ID',
            'type'=>'image',
            'default' =>''),
          'size'=>array(
            'name'=>'Size',
            'type'=>'dropdown',
            'default'=>'large',
            'values'=>'bcorp_image_sizes'),
          'location'=>array(
            'name'=>'Video Location',
            'type'=>'dropdown',
            'dependents'=>array(
              'youtube'=>array('video','starttime'),
              'vimeo'=>array('video')),
            'default'=>'none',
              'values'=>array(
                'none'=>'No Background Video',
                'youtube'=>'You Tube',
                'vimeo'=>'Vimeo')),
          'video'=>array(
            'name'=>'Video ID',
            'type'=>'textfield',
            'default' =>''),
          'starttime'=>array(
            'name'=>'Starting Time',
            'type'=>'textfield',
            'description'=>'Starting Time in Seconds',
            'default' =>'0'),
          'height'=>array(
            'name'=>'Minimum Height',
            'type'=>'dropdown',
            'default'=>'25',
            'dependents'=>array(
              'custom'=>array('pixels')),
            'values'=>array(
              'none'=>'No minimum height',
              '25'=>'25% Window Height',
              '50'=>'50% Window Height',
              '75'=>'75% Window Height',
              '100'=>'100% Window Height',
              'custom'=>'Custom Height',)),
          'pixels'=>array(
            'name'=>'Set a minimum height in pixels',
            'type'=>'textfield',
            'default'=>''),
          'effect'=>array(
            'name'=>'Background Scrolling Effects',
            'type'=>'dropdown',
            'default'=>'normal',
            'dependents'=>array(
              'parallax'=>array('speed')),
            'values'=>array(
              'normal'=>'Normal Scrolling',
              'parallax'=>'Parallax Scrolling',
              'fixed'=>'Fixed Image')
          ),
          'speed'=>array(
            'name'=>'Scroll Speed',
            'type'=>'dropdown',
            'default'=>'0.3',
            'values'=>array(
              '-0.5'=>'50%',
              '-0.3'=>'70%',
              '-0.1'=>'90%',
              '0.3'=>'130%',
              '0.5'=>'150%')),
          'topborder'=>array(
            'name'=>'Top Border',
            'type'=>'dropdown',
            'default'=>'shadow',
            'values'=>array(
              'none'=>'No Border',
              'shadow'=>'Light Shadow',
              'darkshadow'=>'Dark Shadow',
              'plain'=>'Plain Border')),
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_row",array(
        "title"=>"Row",
        "admin_icon"=>"&#xe810;",
        "accept_content"=>true,
        "type"=>"section",
        "admin_default"=>'<div class="bcve-bcorp_row">Color scheme: <span class="bcve-bcorp_row-colors">main</span></div>',
        "variables"=>array(
          'colors'=>array(
            'name'=>'Color Scheme',
            'type'=>'dropdown',
            'default'=>'main',
            'values'=>array(
              'header'=>'Header',
              'main'=>'Main',
              'alt'=>'Alternate',
              'footer'=>'Footer',
              'base'=>'Base')
          )
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_cell",array(
        "title"=>"Cell",
        "accept_content"=>true,
        "type"=>"layout",
        "admin_default"=>'',
        "variables"=>array(
          'width'=>array(
            'name'=>'Width',
            'type'=>'dropdown',
            'default'=>'1-1',
            'values'=>'widths'
          ),
          'mobilewidth'=>array(
            'name'=>'Mobile Landscape Width',
            'type'=>'dropdown',
            'default'=>'1-1',
            'values'=>'widths'
          )
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_alert_box",array(
        "title"=>"Alert Box",
        "admin_icon"=>"&#xe826;",
        "admin_default"=>'<div class="bcve-bcorp_alert_box"><span class="bcve-bcorp_alert_box-icon bcve-bcorp_heading-size-h6" data-icon="&#xe809;"></span><span class="bcve-bcorp_alert_box-message">Alert Box - Edit Me</div></span>',
        "variables"=>array(
          'type'=>array(
            'name'=>'Type',
            'type'=>'dropdown',
            'default'=>'success',
            'dependents'=>array(
              'custom'=>array('backgroundcolor','textcolor','bordercolor')),
            'values'=>array(
              'info'=>'Informational',
              'success'=>'Success',
              'warning'=>'Warning',
              'error'=>'Error',
              'custom'=>'Custom Colors')),
          'backgroundcolor'=>array(
            'name'=>'Background Color',
            'type'=>'color',
            'default'=>''),
          'textcolor'=>array(
            'name'=>'Text Color',
            'type'=>'color',
            'default'=>''),
          'bordercolor'=>array(
              'name'=>'Border Color',
              'type'=>'color',
              'default'=>''),
          'border'=>array(
            'name'=>'Border',
            'type'=>'dropdown',
            'default'=>'solid',
            'values'=>array(
              'none'=>'None',
              'solid'=>'Solid',
              'thick'=>'Thick',
              'dashed'=>'Dashed')),
          'size'=>array(
            'name'=>'Alert Box Padding',
            'type'=>'dropdown',
            'default'=>'medium',
              'values'=>array(
                'none'=>'None',
                'medium'=>'Medium',
                'large'=>'Large')),
          'align'=>'align',
          'message'=>array(
            'name'=>'Alert Message',
            'type'=>'textarea',
            'editor'=>'tinymce',
            'default' =>''),
          'showicon'=>'showicon',
          'idname'=>'idname'
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_accordion",array(
        "title"=>"Accordion",
        "admin_icon"=>"&#xe825;",
        "accept_content"=>true,
        "child_element"=>"bcorp_accordion_panel",
        "width" => "1-1",
        "closing_tag"=>true,
        "admin_default"=>'<div class="bcve-bcorp_accordion">
                            <i class="bcve-icon bcve-header-icon">&#xe825;</i>
                            <div class="bcve-bcorp_accordion-details">Multiple Open: <span class="bcve-bcorp_accordion-multiple">false</span></div>
                          </div>',
        "variables"=>array(
          'multiple'=>array(
            'name'=>'Allow Multiple Tabs Open At Once',
            'type'=>'checkbox',
            'default'=>'false'),
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_accordion_panel",array(
        "title"=>"Accordion Panel",
        "admin_icon"=>"&#xe825;",
        "only_child"=>true,
        "width"=>"1-3-min",
        "parent_element"=>"bcorp_accordion",
        "admin_default"=>'<div class="bcve-bcorp_accordion_panel">
                            <div class="bcve-bcorp_accordion_panel-details">
                              <span class="bcve-bcorp_accordion_panel-title bcve-bcorp_heading-text bcve-bcorp_heading-size-h3">Accordion - Edit Me</span>
                              <div class="bcve-bcorp_accordion_panel-textblock"><p>Accordion Content - Edit Me</p></div>
                            </div>
                          </div>',
        "variables"=>array(
          'open'=>array(
            'name'=>'Open Tab On Page Load',
            'type'=>'checkbox',
            'default'=>'false'),
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default' =>''),
          'textblock'=>array(
            'name'=>'Text Block',
            'type'=>'textarea',
            'editor'=>'tinymce',
            'default' =>''
          )
        )
      )
    );
    if (!$bcorp_full_width_theme) $this->bcorp_add_shortcode(
      "bcorp_blog",array(
        "title"=>"Blog",
        "admin_icon"=>"&#xe80B;",
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_blog"><i class="bcve-icon bcve-header-icon">&#xe80B;</i><div class="bcve-bcorp_blog-details">Filtered by: <span class="bcve-bcorp_blog-filterby">category</span><br />
    Fullwidth: <span class="bcve-bcorp_blog-fullwidth">false</span><br />
    Posts Per Page: <span class="bcve-bcorp_blog-count">12</span><br />
    Columns: <span class="bcve-bcorp_blog-columns">3</span></div></div>',
        "variables"=>array(
          'filterby'=>array(
            'name'=>'Filter Posts By',
            'type'=>'dropdown',
            'dependents'=>array(
              'category'=>array('categories'),
              'tag'=>array('tags'),
              'formats'=>array('formats'),
              'portfolios'=>array('portfolios','portfoliolink')),
            'default'=>'category',
            'values'=>array(
              'category'=>'Category',
              'tag'=>'Tags',
              'formats'=>'Post Format',
              'portfolios'=>'Portfolio Entries')),
          'categories'=>array(
            'name'=>'Categories',
            'type'=>'dropdown',
            'default'=>'',
            'selectmultiple'=>true,
            'values'=>'categories'),
          'tags'=>array(
            'name'=>'Tags',
            'type'=>'dropdown',
            'default'=>'',
            'selectmultiple'=>true,
            'values'=>'tags'),
          'formats'=>array(
            'name'=>'Post Formats',
            'type'=>'dropdown',
            'default'=>'',
            'selectmultiple'=>true,
            'values'=>'post_formats'),
          'portfolios'=>array(
            'name'=>'Portfolio Categories',
            'type'=>'dropdown',
            'default'=>'',
            'selectmultiple'=>true,
            'values'=>'portfolios'),
          'format'=>array(
            'name'=>'Format',
            'type'=>'dropdown',
            'default'=>'excerpt',
            'values'=>array(
              'full'=>'Full Blog Text',
              'excerpt'=>'Excerpt Only',
              'excerpt_more'=>'Excerpt + Read More',
              'none'=>'No Excerpt')),
          'portfoliolink'=>array(
            'name'=>'Link',
            'type'=>'dropdown',
            'default'=>'portfolio',
            'values'=>array(
              'ajax'=>'Open Portfolio Inline Above',
              'lightbox'=>'Open Preview Image in Lightbox',
              'portfolio'=>'Open Portfolio Entry')),
          'columns'=>array(
            'name'=>'Columns',
            'type'=>'dropdown',
            'default'=>'3',
            'dependents'=>array(
            '2'=>array('filter'),
            '3'=>array('filter'),
            '4'=>array('filter'),
            '5'=>array('filter'),
            '6'=>array('filter')),
            'values'=>array(
              '1'=>'1 Column',
              '2'=>'2 Columns',
              '3'=>'3 Columns',
              '4'=>'4 Columns',
              '5'=>'5 Columns',
              '6'=>'6 Columns')),
          'gutter'=>array(
            'name'=>'Gutter Size between Cells',
            'description'=>'Enter a gutter percentage size',
            'type'=>'textfield',
            'default' =>'1.5'),
          'bottommargin'=>array(
            'name'=>'Bottom Margin between Cells',
            'description'=>'Enter a bottom margin size (include units %, px)',
            'type'=>'textfield',
            'units'=>array('%','px'),
            'default' =>'1.5%'),
          'wrappermargin'=>array(
            'name'=>'Blog Outside Margin',
            'description'=>'Enter an outside percentage size',
            'type'=>'textfield',
            'default' =>'0'),
          'size'=>array(
            'name'=>'Image Size',
            'type'=>'dropdown',
            'dependents'=>array(
              'custom'=>array('customsize')),
            'default'=>'automatic',
            'values'=>array(
              'automatic'=>'Use Default Image Size',
              'custom'=>'Choose Custom Image Size')),
          'customsize'=>array(
            'name'=>'Custom Image Size',
            'type'=>'dropdown',
            'default'=>'medium',
            'values'=>'bcorp_image_sizes'),
          'count'=>array(
            'name'=>'Posts per Page',
            'type'=>'dropdown',
            'default'=>'12',
            'values'=>'1to100all'),
          'offset'=>array(
            'name'=>'Offset',
            'type'=>'dropdown',
            'default'=>'0',
            'values'=>'0to100'),
          'more'=>array(
            'name'=>'Additional Posts',
            'type'=>'dropdown',
            'default'=>'loadmore',
            'values'=>array(
              'none'=>'None',
              'loadmore'=>'Load More',
              'paged'=>'Paged')),
          'filter'=>array(
            'name'=>'Masonry Category Filter',
            'type'=>'checkbox',
            'default'=>'true'),
          'padding'=>array(
            'name'=>'Content Side Padding',
            'description'=>'Enter a content padding size (include units px, %, em)',
            'type'=>'textfield',
            'units'=>array('px','%','em'),
            'default' =>'0px'),
          'backgroundcolor'=>array(
            'name'=>'Background Color',
            'type'=>'dropdown',
            'default'=>'default',
            'values'=>array(
              'main'=>'Main Background',
              'alt'=>'Alternate Background')),
          'showheading'=>array(
              'name'=>'Show Heading',
              'admin_tab'=>'Heading',
              'type'=>'checkbox',
              'default'=>'true',
              'dependents'=>array(
              'true'=>array('heading','headingcolor','headingmargintop','headingmarginbottom','headingalign'))),
          'heading'=>'heading',
          'headingcolor'=>array(
            'name'=>'Heading Color',
            'admin_tab'=>'Colors',
            'type'=>'dropdown',
            'default'=>'heading',
            'dependents'=>array(
                'custom'=>array('headingcustomcolor')),
            'values'=>array(
                'heading'=>'Heading Color',
                'font'=>'Main Font Color',
                'altfont'=>'Alternate Font Color',
                'link'=>'Link Color',
                'custom'=>'Custom Color')),
            'headingcustomcolor'=>array(
              'name'=>'Heading Custom Color',
              'type'=>'color',
              'default'=>''),
          'headingmargintop'=>array(
            'name'=>'Heading Top Margin',
            'units'=>array('px','em','%'),
            'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'default' =>'5px'),
          'headingmarginbottom'=>array(
            'name'=>'Heading Bottom Margin',
            'units'=>array('px','em','%'),
            'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'default' =>'5px'),
          'headingalign'=>array(
            'name'=>'Heading Alignment',
            'type'=>'dropdown',
            'default'=>'left',
            'admin_class'=>true,
            'values'=>array(
              'left'=>'Left',
              'center'=>'Center',
              'right'=>'Right')),
          'animation'=>'animation',
          'metalocation'=>array(
            'name'=>'Meta Location',
            'admin_tab'=>'Meta',
            'type'=>'dropdown',
            'default'=>'below',
            'values'=>array(
              'above'=>'Above Thumbnail',
              'below'=>'Below Thumbnail')),
          'metamargintop'=>array(
            'name'=>'Post Meta Top Margin',
            'admin_tab'=>'Meta',
            'units'=>array('px','em','%'),
            'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'default' =>'5px'),
          'metamarginbottom'=>array(
            'name'=>'Post Meta Bottom Margin',
            'admin_tab'=>'Meta',
            'units'=>array('px','em','%'),
            'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'default' =>'5px'),
          'metastyle'=>array(
            'name'=>'Meta Style',
            'admin_tab'=>'Meta',
            'type'=>'dropdown',
            'default'=>'style1',
            'values'=>array(
              'style1'=>'Style 1',
              'style2'=>'Style 2')),
          'metaalign'=>array(
            'name'=>'Meta Alignment',
            'admin_tab'=>'Meta',
            'type'=>'dropdown',
            'default'=>'left',
            'admin_class'=>true,
            'values'=>array(
              'left'=>'Left',
              'center'=>'Center',
              'right'=>'Right')),
          'metasize'=>array(
            'name'=>'Meta Font Size',
            'admin_tab'=>'Meta',
            'description'=>'Enter a size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'units'=>array('px','em','%'),
            'default' =>'12px'),
          'metalineheight'=>array(
            'name'=>'Meta Line Height',
            'admin_tab'=>'Meta',
            'description'=>'Enter a line height in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'units'=>array('px','em','%'),
            'default' =>'18px'),
          'share'=>array(
            'name'=>'Social Media Share Boxes',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'false'),
          'metatype'=>array(
            'name'=>'Show Post Type',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'false'),
          'metadate'=>array(
            'name'=>'Show Post Date',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'true'),
          'metacategory'=>array(
            'name'=>'Show Post Category',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'true'),
          'metaauthor'=>array(
            'name'=>'Show Post Author',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'true'),
          'metatags'=>array(
            'name'=>'Show Post Tags',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'true'),
          'metacomments'=>array(
              'name'=>'Show Post Comments',
              'admin_tab'=>'Meta',
              'type'=>'checkbox',
              'default'=>'true'),
          'metaedit'=>array(
              'name'=>'Show Post Edit Link',
              'admin_tab'=>'Meta',
              'type'=>'checkbox',
              'default'=>'true'),
          'bordercolor'=>array(
            'name'=>'Border Custom Color',
            'admin_tab'=>'Border',
            'type'=>'color',
            'default'=>''),
          'bordertop'=>array(
            'name'=>'Border Top',
            'admin_tab'=>'Border',
            'description'=>'Enter a border width in px',
            'type'=>'textfield',
            'units'=>array('px'),
            'default' =>'0px'),
          'borderbottom'=>array(
            'name'=>'Border Bottom',
            'admin_tab'=>'Border',
            'description'=>'Enter a border width in px',
            'type'=>'textfield',
            'units'=>array('px'),
            'default' =>'0px'),
          'borderleft'=>array(
            'name'=>'Border Left',
            'admin_tab'=>'Border',
            'description'=>'Enter a border width in px',
            'type'=>'textfield',
            'units'=>array('px'),
            'default' =>'0px'),
          'borderright'=>array(
            'name'=>'Border Right',
            'admin_tab'=>'Border',
            'description'=>'Enter a border width in px',
            'type'=>'textfield',
            'units'=>array('px'),
            'default' =>'0px'),
        )
      )
    );
    else $this->bcorp_add_shortcode(
      "bcorp_blog",array(
        "title"=>"Blog",
        "admin_icon"=>"&#xe80B;",
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_blog"><i class="bcve-icon bcve-header-icon">&#xe80B;</i><div class="bcve-bcorp_blog-details">Filtered by: <span class="bcve-bcorp_blog-filterby">category</span><br />
    Fullwidth: <span class="bcve-bcorp_blog-fullwidth">false</span><br />
    Posts Per Page: <span class="bcve-bcorp_blog-count">12</span><br />
    Columns: <span class="bcve-bcorp_blog-columns">3</span></div></div>',
        "variables"=>array(
          'fullwidth'=>array(
            'name'=>'Enable Full Page Width',
            'type'=>'checkbox',
            'default'=>'false'),
          'filterby'=>array(
            'name'=>'Filter Posts By',
            'type'=>'dropdown',
            'dependents'=>array(
              'category'=>array('categories'),
              'tag'=>array('tags'),
              'formats'=>array('formats'),
              'portfolios'=>array('portfolios','portfoliolink')),
            'default'=>'category',
            'values'=>array(
              'category'=>'Category',
              'tag'=>'Tags',
              'formats'=>'Post Format',
              'portfolios'=>'Portfolio Entries')),
          'categories'=>array(
            'name'=>'Categories',
            'type'=>'dropdown',
            'default'=>'',
            'selectmultiple'=>true,
            'values'=>'categories'),
          'tags'=>array(
            'name'=>'Tags',
            'type'=>'dropdown',
            'default'=>'',
            'selectmultiple'=>true,
            'values'=>'tags'),
          'formats'=>array(
            'name'=>'Post Formats',
            'type'=>'dropdown',
            'default'=>'',
            'selectmultiple'=>true,
            'values'=>'post_formats'),
          'portfolios'=>array(
            'name'=>'Portfolio Categories',
            'type'=>'dropdown',
            'default'=>'',
            'selectmultiple'=>true,
            'values'=>'portfolios'),
          'format'=>array(
            'name'=>'Format',
            'type'=>'dropdown',
            'default'=>'excerpt',
            'values'=>array(
              'full'=>'Full Blog Text',
              'excerpt'=>'Excerpt Only',
              'excerpt_more'=>'Excerpt + Read More',
              'none'=>'No Excerpt')),
          'portfoliolink'=>array(
            'name'=>'Link',
            'type'=>'dropdown',
            'default'=>'portfolio',
            'values'=>array(
              'ajax'=>'Open Portfolio Inline Above',
              'lightbox'=>'Open Preview Image in Lightbox',
              'portfolio'=>'Open Portfolio Entry')),
          'columns'=>array(
            'name'=>'Columns',
            'type'=>'dropdown',
            'default'=>'3',
            'dependents'=>array(
            '2'=>array('filter'),
            '3'=>array('filter'),
            '4'=>array('filter'),
            '5'=>array('filter'),
            '6'=>array('filter')),
            'values'=>array(
              '1'=>'1 Column',
              '2'=>'2 Columns',
              '3'=>'3 Columns',
              '4'=>'4 Columns',
              '5'=>'5 Columns',
              '6'=>'6 Columns')),
          'gutter'=>array(
            'name'=>'Gutter Size between Cells',
            'description'=>'Enter a gutter percentage size',
            'type'=>'textfield',
            'default' =>'1.5'),
          'bottommargin'=>array(
            'name'=>'Bottom Margin between Cells',
            'description'=>'Enter a bottom margin size (include units %, px)',
            'type'=>'textfield',
            'units'=>array('%','px'),
            'default' =>'1.5%'),
          'wrappermargin'=>array(
            'name'=>'Blog Outside Margin',
            'description'=>'Enter an outside percentage size',
            'type'=>'textfield',
            'default' =>'0'),
          'size'=>array(
            'name'=>'Image Size',
            'type'=>'dropdown',
            'dependents'=>array(
              'custom'=>array('customsize')),
            'default'=>'automatic',
            'values'=>array(
              'automatic'=>'Use Default Image Size',
              'custom'=>'Choose Custom Image Size')),
          'customsize'=>array(
            'name'=>'Custom Image Size',
            'type'=>'dropdown',
            'default'=>'medium',
            'values'=>'bcorp_image_sizes'),
          'count'=>array(
            'name'=>'Posts per Page',
            'type'=>'dropdown',
            'default'=>'12',
            'values'=>'1to100all'),
          'offset'=>array(
            'name'=>'Offset',
            'type'=>'dropdown',
            'default'=>'0',
            'values'=>'0to100'),
          'more'=>array(
            'name'=>'Additional Posts',
            'type'=>'dropdown',
            'default'=>'loadmore',
            'values'=>array(
              'none'=>'None',
              'loadmore'=>'Load More',
              'paged'=>'Paged')),
          'filter'=>array(
            'name'=>'Masonry Category Filter',
            'type'=>'checkbox',
            'default'=>'true'),
          'padding'=>array(
            'name'=>'Content Side Padding',
            'description'=>'Enter a content padding size (include units px, %, em)',
            'type'=>'textfield',
            'units'=>array('px','%','em'),
            'default' =>'0px'),
          'backgroundcolor'=>array(
            'name'=>'Background Color',
            'type'=>'dropdown',
            'default'=>'default',
            'values'=>array(
              'main'=>'Main Background',
              'alt'=>'Alternate Background')),
          'showheading'=>array(
              'name'=>'Show Heading',
              'admin_tab'=>'Heading',
              'type'=>'checkbox',
              'default'=>'true',
              'dependents'=>array(
              'true'=>array('heading','headingcolor','headingmargintop','headingmarginbottom','headingalign'))),
          'heading'=>'heading',
          'headingcolor'=>array(
            'name'=>'Heading Color',
            'admin_tab'=>'Colors',
            'type'=>'dropdown',
            'default'=>'heading',
            'dependents'=>array(
                'custom'=>array('headingcustomcolor')),
            'values'=>array(
                'heading'=>'Heading Color',
                'font'=>'Main Font Color',
                'altfont'=>'Alternate Font Color',
                'link'=>'Link Color',
                'custom'=>'Custom Color')),
            'headingcustomcolor'=>array(
              'name'=>'Heading Custom Color',
              'type'=>'color',
              'default'=>''),
          'headingmargintop'=>array(
            'name'=>'Heading Top Margin',
            'units'=>array('px','em','%'),
            'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'default' =>'5px'),
          'headingmarginbottom'=>array(
            'name'=>'Heading Bottom Margin',
            'units'=>array('px','em','%'),
            'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'default' =>'5px'),
          'headingalign'=>array(
            'name'=>'Heading Alignment',
            'type'=>'dropdown',
            'default'=>'left',
            'admin_class'=>true,
            'values'=>array(
              'left'=>'Left',
              'center'=>'Center',
              'right'=>'Right')),
          'animation'=>'animation',
          'metalocation'=>array(
            'name'=>'Meta Location',
            'admin_tab'=>'Meta',
            'type'=>'dropdown',
            'default'=>'below',
            'values'=>array(
              'above'=>'Above Thumbnail',
              'below'=>'Below Thumbnail')),
          'metamargintop'=>array(
            'name'=>'Post Meta Top Margin',
            'admin_tab'=>'Meta',
            'units'=>array('px','em','%'),
            'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'default' =>'5px'),
          'metamarginbottom'=>array(
            'name'=>'Post Meta Bottom Margin',
            'admin_tab'=>'Meta',
            'units'=>array('px','em','%'),
            'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'default' =>'5px'),
          'metastyle'=>array(
            'name'=>'Meta Style',
            'admin_tab'=>'Meta',
            'type'=>'dropdown',
            'default'=>'style1',
            'values'=>array(
              'style1'=>'Style 1',
              'style2'=>'Style 2')),
          'metaalign'=>array(
            'name'=>'Meta Alignment',
            'admin_tab'=>'Meta',
            'type'=>'dropdown',
            'default'=>'left',
            'admin_class'=>true,
            'values'=>array(
              'left'=>'Left',
              'center'=>'Center',
              'right'=>'Right')),
          'metasize'=>array(
            'name'=>'Meta Font Size',
            'admin_tab'=>'Meta',
            'description'=>'Enter a size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'units'=>array('px','em','%'),
            'default' =>'12px'),
          'metalineheight'=>array(
            'name'=>'Meta Line Height',
            'admin_tab'=>'Meta',
            'description'=>'Enter a line height in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'units'=>array('px','em','%'),
            'default' =>'18px'),
          'share'=>array(
            'name'=>'Social Media Share Boxes',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'false'),
          'metatype'=>array(
            'name'=>'Show Post Type',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'false'),
          'metadate'=>array(
            'name'=>'Show Post Date',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'true'),
          'metacategory'=>array(
            'name'=>'Show Post Category',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'true'),
          'metaauthor'=>array(
            'name'=>'Show Post Author',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'true'),
          'metatags'=>array(
            'name'=>'Show Post Tags',
            'admin_tab'=>'Meta',
            'type'=>'checkbox',
            'default'=>'true'),
          'metacomments'=>array(
              'name'=>'Show Post Comments',
              'admin_tab'=>'Meta',
              'type'=>'checkbox',
              'default'=>'true'),
          'metaedit'=>array(
              'name'=>'Show Post Edit Link',
              'admin_tab'=>'Meta',
              'type'=>'checkbox',
              'default'=>'true'),
          'bordercolor'=>array(
            'name'=>'Border Custom Color',
            'admin_tab'=>'Border',
            'type'=>'color',
            'default'=>''),
          'bordertop'=>array(
            'name'=>'Border Top',
            'admin_tab'=>'Border',
            'description'=>'Enter a border width in px',
            'type'=>'textfield',
            'units'=>array('px'),
            'default' =>'0px'),
          'borderbottom'=>array(
            'name'=>'Border Bottom',
            'admin_tab'=>'Border',
            'description'=>'Enter a border width in px',
            'type'=>'textfield',
            'units'=>array('px'),
            'default' =>'0px'),
          'borderleft'=>array(
            'name'=>'Border Left',
            'admin_tab'=>'Border',
            'description'=>'Enter a border width in px',
            'type'=>'textfield',
            'units'=>array('px'),
            'default' =>'0px'),
          'borderright'=>array(
            'name'=>'Border Right',
            'admin_tab'=>'Border',
            'description'=>'Enter a border width in px',
            'type'=>'textfield',
            'units'=>array('px'),
            'default' =>'0px'),
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_button",array(
        "title"=>"Button",
        "admin_icon"=>"&#xe824;",
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_button bcorp-button-medium">
                            <button>
                              <span class="bcve-bcorp_button-icon" data-icon="&#xe809;"></span>
                              <span class="bcve-bcorp_button-label">Button - Edit Me</span>
                            </button>
                          </div>',
        "variables"=>array(
          'label'=>array(
            'name'=>'Button Label',
            'type'=>'textfield',
            'default' =>''),
          'showicon'=>array(
            'name'=>'Display Icon',
            'admin_tab'=>'Icon',
            'type'=>'dropdown',
            'default'=>'left',
            'dependents'=>array(
              'left'=>array('icon','iconcolor'),
              'right'=>array('icon','iconcolor')),
            'values'=>array(
              'left'=>'Left',
              'right'=>'Right',
              'none'=>'None')),
          'iconcolor'=>array(
            'name'=>'Icon Color',
            'type'=>'color',
            'default'=>''),
          'icon'=>array(
            'name'=>'Icon',
            'type'=>'icon',
            'default'=>'f005'),
          'link'=>'link',
          'colors'=>array(
            'name'=>'Color Presets',
            'admin_tab'=>'Colors',
            'type'=>'dropdown',
            'default'=>'custom',
            'dependents'=>array(
              'custom'=>array('color','hovercolor','bordercolor','hoverbordercolor','textcolor','hovertextcolor')),
            'values'=>array(
              'custom'=>'Custom',
              'default'=>'Default',
              'red'=>'Red',
              'green'=>'Green',
              'blue'=>'Blue',
              'orange'=>'Orange')),
          'color'=>array(
            'name'=>'Button Color',
            'admin_tab'=>'Colors',
            'type'=>'color',
            'default'=>''),
          'hovercolor'=>array(
            'name'=>'Hover Button Color',
            'admin_tab'=>'Colors',
            'type'=>'color',
            'default'=>''),
          'transparent'=>array(
            'name'=>'Transparent Center',
            'type'=>'checkbox',
            'default'=>'false'),
          'bordercolor'=>array(
            'name'=>'Border Color',
            'admin_tab'=>'Colors',
            'type'=>'color',
            'default'=>'inherit'),
          'hoverbordercolor'=>array(
            'name'=>'Hover Border Color',
            'admin_tab'=>'Colors',
            'type'=>'color',
            'default'=>'inherit'),
          'thickness'=>array(
            'name'=>'Border Thickness',
            'type'=>'textfield',
            'units'=>array('px'),
            'description'=>'Border thickness in px.',
            'default' =>'0px'),
          'radius'=>array(
            'name'=>'Border Radius',
            'type'=>'textfield',
            'units'=>array('px','%'),
            'description'=>'Border Radius in px or %. (Defaults to px).',
            'default' =>'0px'),
          'textcolor'=>array(
            'name'=>'Text Color',
            'admin_tab'=>'Colors',
            'type'=>'color',
            'default'=>''),
          'hovertextcolor'=>array(
            'name'=>'Hover Text Color',
            'admin_tab'=>'Colors',
            'type'=>'color',
            'default'=>''),
          'size'=>array(
            'name'=>'Size',
            'type'=>'dropdown',
            'default'=>'medium',
            'values'=>array(
              'small'=>'Small',
              'medium'=>'Medium',
              'large'=>'Large',
              'xlarge'=>'Extra Large')),
          'bold'=>array(
            'name'=>'Bold Font',
            'type'=>'checkbox',
            'default'=>'true'),
          'align'=>'align',
          'animation'=>'animation',
          'margintop'=>'marginsall'
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_divider",array(
        "title"=>"Divider",
        "admin_icon"=>"&#xe820;",
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcorp_divider bcorp-cell bcorp-1-1 bcve-bcorp_divider-type-blank" style="padding-top:8px; padding-bottom:8px; border-color:#888;">
                <div class="bcve-bcorp_divider-details">Blank Space: <span class="bcve-bcorp_divider-height">32px</span></div>
                  <div class="bcve-bcorp_divider-divider" style="width:33%; margin:0 auto; border-color:inherit;">
                    <div style="overflow:hidden;border-color:inherit;">
                      <div style="margin-left:-9px; width:50%; padding:2px; border-bottom: 1px solid; border-color:inherit; float:left;"></div>
                      <div style="width:5px; height:5px; border: 2px solid; border-color:inherit; border-radius: 5px; float:left;"></div>
                      <div style="margin-right:-9px; width:50%; padding:2px; border-bottom: 1px solid; border-color:inherit; float:left;"></div>
                    </div>
                  </div>
                </div>',
        "variables"=>array(
          'type'=>array(
            'name'=>'Divider Type',
            'type'=>'dropdown',
            'admin_class'=>true,
            'dependents'=>array(
              'plain'=>array('margintop','marginbottom'),
              'small'=>array('align','margintop','marginbottom'),
              'blank'=>array('height'),
              'custom'=>array('align','thickness','margintop','marginbottom','showicon')),
            'default'=>'blank',
            'values'=>array(
              'plain'=>'Plain',
              'blank'=>'Blank Space',
              'small'=>'Small Divider',
              'custom'=>'Custom')),
          'align'=>array(
            'name'=>'Alignment',
            'type'=>'dropdown',
            'default'=>'center',
            'values'=>array(
              'left'=>'Left',
              'center'=>'Center',
              'right'=>'Right')),
          'height'=>array(
            'name'=>'Height',
            'default'=>'32px',
            'units'=>array('px','em'),
            'description'=>'Enter a height size in px or em. Defaults to px if no unit is entered.',
            'type'=>'textfield'),
          'thickness'=>array(
            'name'=>'Line Type',
            'type'=>'dropdown',
            'dependents'=>array(
              'thin'=>array('linewidth','linecolor'),
              'thick'=>array('linewidth','linecolor')),
            'default'=>'thin',
            'values'=>array(
              'none'=>'none',
              'thin'=>'Thin',
              'thick'=>'Thick')),
          'linecolor'=>array(
            'name'=>'Line Color',
            'type'=>'color',
            'default'=>''),
          'linewidth'=>array(
            'name'=>'Separator Width',
            'type'=>'textfield',
            'description'=>'Enter a size in px, em or %. Defaults to % if no unit is entered.',
            'type'=>'textfield',
            'units'=>array('%','px','em'),
            'default'=>'33%'),
          'margintop'=>'margins',
          'showicon'=>'showicon',
          'hide'=>array(
            'name'=>'Hide',
            'type'=>'dropdown',
            'default'=>'never',
            'values'=>array(
              'never'=>'Never',
              'mobile'=>'On Mobile',
              'desktop'=>'On Desktop')),
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_gallery",array(
        "title"=>"Gallery",
        "closing_tag"=>false,
        "admin_icon"=>'&#xe805;',
        "admin_default"=>'<div class="bcve-bcorp_gallery"><div class="bcve-bcorp_gallery-header"><i class="bcve-icon bcve-header-icon">&#xe805;</i>
            <div class="bcve-bcorp_gallery-details">Large Preview: <span class="bcve-bcorp_gallery-preview">true</span><br>
              Columns: <span class="bcve-bcorp_gallery-columns">6</span><br>
              Link: <span class="bcve-bcorp_gallery-link">lightbox</span></div></div><div class="bcve-bcorp_gallery-ids"></div></div>',
        "variables"=>array(
          'ids'=>array(
            'name'=>'Image IDs',
            'type'=>'gallery',
            'default' =>''),
          'preview'=>array(
            'name'=>'Show Large Preview Image Above',
            'type'=>'checkbox',
            'dependents'=>array(
              'true'=>array('previewsize')),
            'default'=>'true'),
          'caption'=>array(
            'name'=>'Display Picture Title',
            'type'=>'checkbox',
            'default'=>'false'),
          'previewsize'=>array(
            'name'=>'Preview Image Size',
            'type'=>'dropdown',
            'default'=>'large',
            'values'=>'bcorp_image_sizes'),
          'columns'=>array(
            'name'=>'Columns',
            'type'=>'dropdown',
            'default'=>'6',
            'values'=>array(
              '1'=>'1 Column',
              '2'=>'2 Columns',
              '3'=>'3 Columns',
              '4'=>'4 Columns',
              '5'=>'5 Columns',
              '6'=>'6 Columns',
              '7'=>'7 Columns',
              '8'=>'8 Columns',
              '9'=>'9 Columns',
              '10'=>'10 Columns',
              '11'=>'11 Columns',
              '12'=>'12 Columns')),
          'thumbsize'=>array(
            'name'=>'Thumbnail Image Size',
            'type'=>'dropdown',
            'default'=>'thumbnail',
            'values'=>'bcorp_image_sizes'),
          'link'=>array(
            'name'=>'Link',
            'type'=>'dropdown',
            'default'=>'lightbox',
            'values'=>array(
              'lightbox'=>'Lightbox',
              'new'=>'New Window',
              'same'=>'Same Window',
              'none'=>'No Link')),
          'largesize'=>array(
            'name'=>'Full sized Version to Display',
            'type'=>'dropdown',
            'default'=>'large',
            'values'=>'bcorp_image_sizes'),
          'animation'=>array(
            'name'=>'Animation',
            'type'=>'dropdown',
            'default'=>'none',
            'values'=>array(
              'none'=>'None'
            )
          )
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_heading",array(
        "title"=>"Heading",
        "admin_icon"=>"&#xe80E;",
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_heading bcve-bcorp_heading-heading-h1 bcve-bcorp_heading-align-center"><div class="bcve-bcorp_heading-text">Heading</div></div>',
        "variables"=>array(
          'text'=>array(
            'name'=>'Heading Text',
            'type'=>'textfield',
            'default' =>''),
          'heading'=>'heading',
          'margintop'=>'margins',
          'bold'=>array(
            'name'=>'Bold',
            'type'=>'checkbox',
            'default' => 'true'),
          'italic'=>array(
            'name'=>'Italic',
            'type'=>'checkbox',
            'default' => 'false'),
          'fontcolor'=>array(
            'name'=>'Font Color',
            'type'=>'dropdown',
            'default'=>'heading',
            'dependents'=>array(
                'custom'=>array('color')),
            'values'=>array(
                'heading'=>'Heading Color',
                'font'=>'Main Font Color',
                'altfont'=>'Alternate Font Color',
                'link'=>'Link Color',
                'custom'=>'Custom Color')),
          'color'=>array(
            'name'=>'Color',
            'type'=>'color',
            'default'=>''),
          'align'=>array(
            'name'=>'Alignment',
            'type'=>'dropdown',
            'default'=>'center',
            'admin_class'=>true,
            'values'=>array(
              'left'=>'Left',
              'center'=>'Center',
              'right'=>'Right')),
          'animation'=>'animation',
          'separator'=>array(
            'name'=>'Separator Type',
            'admin_tab'=>'Separator',
            'type'=>'dropdown',
            'default'=>'none',
            'dependents'=>array(
              'inline-single'=>array('linestyle','linecolor','linethickness'),
              'inline-double'=>array('linestyle','linecolor'),
              'underline'=>array('linestyle','linecolor'),
            ),
            'values'=>array(
              'none'=>'None',
              'inline-single'=>'Inline Single',
              'inline-double'=>'Inline Double',
              'underline'=>'Underline')),
          'linestyle'=>array(
            'name'=>'Separator Line Type',
            'admin_tab'=>'Separator',
            'type'=>'dropdown',
            'default'=>'solid',
            'values'=>array(
              'solid'=>'Solid',
              'dotted'=>'Dotted',
              'dashed'=>'Dashed')),
          'linethickness'=>array(
            'name'=>'Line Thickness',
            'type'=>'dropdown',
            'default' =>'thin',
            'values'=>array(
              'thin'=>'Thin',
              'thick'=>'Thick')),
          'linecolor'=>array(
            'name'=>'Separator Color',
            'type'=>'color',
            'default'=>''),
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_icon",array(
        "title"=>"Icon",
        "admin_icon"=>"&#xe80C;",
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_icon bcve-bcorp_icon-align-center bcve-bcorp_icon-border-true"><div class="bcve-bcorp_icon-icon" data-icon="&#61445;">&#61445;</div><div class="bcve-bcorp_icon-title">Icon - Edit Me</div></div>',
        "variables"=>array(
          'icon'=>array(
            'name'=>'Icon',
            'type'=>'icon',
            'default'=>'f005'),
          'size'=>array(
            'name'=>'Icon Size',
            'description'=>'Enter a size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'units'=>array('px','em','%'),
            'default' =>'50px'),
          'iconcolor'=>array(
            'name'=>'Icon Color',
            'type'=>'color',
            'default'=>''),
          'border'=>array(
            'name'=>'Circular Border',
            'description'=>'Please note that not every icon is perfectly aligned especially at very small icon and border sizes.',
            'admin_tab'=>'Border',
            'type'=>'checkbox',
            'default'=>'false',
            'admin_class'=>true,
            'values'=>array('true'=>'True','false'=>'False'),
            'dependents'=>array(
              'true'=>array('title','heading','headingcolor','headingmargintop','bordersize','bordercolor','backgroundcolor','borderthickness'))),
          'bordersize'=>array(
            'name'=>'Border Size',
            'type'=>'textfield',
            'description'=>'Enter a border size in %. Recommended at least 140% for larger icons and even larger value for small icons.',
            'default'=>'140%',
            'units'=>array('%'),
            ),
          'bordercolor'=>array(
            'name'=>'Border Color',
            'type'=>'color',
            'default'=>''),
          'backgroundcolor'=>array(
            'name'=>'Background Color',
            'type'=>'color',
            'default'=>''),
          'borderthickness'=>array(
            'name'=>'Border Thickness',
            'units'=>array('px'),
            'description'=>'Enter a thickness in px.',
            'type'=>'textfield',
            'default' =>'3px'),
          'title'=>array(
            'name'=>'Title',
            'description'=>'Optionally enter a title',
            'type'=>'textfield',
            'default' =>''),
          'heading'=>'heading',
          'headingcolor'=>array(
            'name'=>'Heading Color',
            'type'=>'color',
            'default'=>''),
          'headingmargintop'=>array(
            'name'=>'Heading Top Margin',
            'units'=>array('px','em','%'),
            'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'default' =>'5px'),
          'align'=>array(
            'name'=>'Alignment',
            'type'=>'dropdown',
            'default'=>'left',
            'admin_class'=>true,
            'values'=>array(
              'left'=>'Left',
              'center'=>'Center',
              'right'=>'Right')),
          'link'=>'link',
        'tooltip'=>array(
          'name'=>'Tooltip',
          'type'=>'textfield',
          'default' =>''
        ),
        'animation'=>'animation',
        'margintop'=>'marginsall'
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_icon_box",array(
        "title"=>"Icon Box",
        "admin_icon"=>"&#xe809;",
        "admin_default"=>'<div class="bcve-bcorp_icon_box bcve-bcorp_heading-heading-h1">
                            <span class="bcve-bcorp_icon_box-icon" aria-hidden="true" data-icon="&#59401;"></span>
                            <span class="bcve-bcorp_icon_box-title bcve-bcorp_heading-text">Icon Box - Edit Me</span>
                            <div class="bcve-bcorp_icon_box-textblock"><p>Content - Edit Me</p></div>
                          </div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default' =>''),
          'heading'=>'heading',
          'headingcolor'=>array(
            'name'=>'Heading Color',
            'admin_tab'=>'Colors',
            'type'=>'dropdown',
            'default'=>'heading',
            'dependents'=>array(
                'custom'=>array('headingcustomcolor')),
            'values'=>array(
                'heading'=>'Heading Color',
                'font'=>'Main Font Color',
                'altfont'=>'Alternate Font Color',
                'link'=>'Link Color',
                'custom'=>'Custom Color')),
            'headingcustomcolor'=>array(
              'name'=>'Heading Custom Color',
              'type'=>'color',
              'default'=>''),
            'textcolor'=>array(
              'name'=>'Text Color',
              'admin_tab'=>'Colors',
              'type'=>'dropdown',
              'default'=>'heading',
              'dependents'=>array(
                  'custom'=>array('textcustomcolor')),
              'values'=>array(
                  'heading'=>'Heading Color',
                  'font'=>'Main Font Color',
                  'altfont'=>'Alternate Font Color',
                  'link'=>'Link Color',
                  'custom'=>'Custom Color')),
            'textcustomcolor'=>array(
              'name'=>'Text Custom Color',
              'type'=>'color',
              'default'=>''),
          'showicon'=>'showicon',
          'align'=>array(
            'name'=>'Alignment',
            'type'=>'dropdown',
            'default'=>'left',
            'values'=>array(
              'left'=>'Left Aligned',
              'center'=>'Centered')),
          'textblock'=>array(
            'name'=>'Text Block',
            'type'=>'textarea',
            'editor'=>'tinymce',
            'default' =>''
          ),
          'background'=>array(
            'name'=>'Type',
            'admin_tab'=>'Box',
            'type'=>'dropdown',
            'default'=>'success',
            'dependents'=>array(
              'custom'=>array('backgroundcolor')),
            'values'=>array(
              'none'=>'None',
              'alt'=>'Alternate Background Color',
              'custom'=>'Custom Background Color')),
          'backgroundcolor'=>array(
            'name'=>'Background Color',
            'type'=>'color',
            'default'=>''),
          'padding'=>array(
            'name'=>'Padding',
            'admin_tab'=>'Box',
            'units'=>array('px','em','%'),
            'description'=>'Enter a margin size in px, em or %. Defaults to px if no unit is entered.',
            'type'=>'textfield',
            'default'=>''),


        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_icon_list",array(
        "title"=>"Icon List",
        "admin_icon"=>"&#xe80A;",
        "accept_content"=>true,
        "child_element"=>"bcorp_icon_list_icon",
        "width" => "1-1",
        "admin_default"=>'<div class="bcve-bcorp_icon_list"><i class="bcve-icon bcve-header-icon">&#xe80A;</i>
      <div class="bcve-bcorp_icon_list-details">Heading Size: <span class="bcve-bcorp_icon_list-headingsize">h3</span><br />
      Icon Size: <span class="bcve-bcorp_icon_list-iconsize">50px</span><br />
      Alignment: <span class="bcve-bcorp_icon_list-align">left</span></div></div>',
        "variables"=>array(
          'align'=>array(
            'name'=>'Alignment',
            'type'=>'dropdown',
            'default'=>'left',
            'values'=>array(
              'left'=>'Left',
              'right'=>'Right')
          ),
          'iconsize'=>array(
            'name'=>'Icon Size',
            'description'=>'Enter a size in px, % or em',
            'type'=>'textfield',
            'default' =>'50px'),
          'headingsize'=>array(
            'name'=>'Size',
            'type'=>'dropdown',
            'default'=>'h3',
            'values'=>array(
              'h1'=>'Heading 1',
              'h2'=>'Heading 2',
              'h3'=>'Heading 3',
              'h4'=>'Heading 4',
              'h5'=>'Heading 5',
              'h6'=>'Heading 6')),

        )
      )
    );

    $this->bcorp_add_shortcode(
    "bcorp_icon_list_icon",array(
      "title"=>"Icon List Icon",
      "admin_icon"=>"&#xe80A;",
      "only_child"=>true,
      "admin_default"=>'<div class="bcve-bcorp_icon_list_icon"><span class="bcve-bcorp_icon_list_icon-icon bcve-bcorp_heading-size-h3" aria-hidden="true" data-icon="&#59401;"></span><span class="bcve-bcorp_icon_list_icon-title bcve-bcorp_heading-text bcve-bcorp_heading-size-h3">Icon List - Edit Me</span><div class="bcve-bcorp_icon_list_icon-textblock"><p>Content - Edit Me</p></div></div>',
      "variables"=>array(
        'title'=>array(
          'name'=>'Title',
          'type'=>'textfield',
          'default' =>''),
        'icon'=>array(
          'name'=>'Icon',
          'type'=>'icon',
          'default'=>''),
        'link'=>'link',
        'textblock'=>array(
          'name'=>'Text Block',
          'type'=>'textarea',
          'editor'=>'tinymce',
          'default' =>''
          )
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_image",array(
        "title"=>"Image",
        "closing_tag"=>false,
        "admin_icon"=>"&#xe804;",
        "admin_default"=>'<div class="bcve-bcorp_image">
                            <div class="bcve-bcorp_image-id">
                              <div class="bcve-image-placeholder">&#xe804;</div>
                            </div>
                          </div>',
        "variables"=>array(
          'id'=>array(
            'name'=>'Image ID',
            'type'=>'image',
            'default' =>''),
          'size'=>array(
            'name'=>'Size',
            'type'=>'dropdown',
            'default'=>'large',
            'values'=>'bcorp_image_sizes'),
          'align'=>array(
            'name'=>'Alignment',
            'type'=>'dropdown',
            'default'=>'stretch',
            'values'=>array(
              'stretch'=>'Stretch to Width',
              'left'=>'Left',
              'center'=>'Center',
              'right'=>'Right')),
          'link'=>'link',
          'animation'=>'animation',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_tabs",array(
        "title"=>"Tabs",
        "admin_icon"=>"&#xe822;",
        "accept_content"=>true,
        "child_element"=>"bcorp_tab_panel",
        "width" => "1-1",
        "admin_default"=>'<div class="bcve-bcorp_tabs">
                            <i class="bcve-icon bcve-header-icon">&#xe822;</i>
                            <div class="bcve-bcorp_tabs-details">Position: <span class="bcve-bcorp_tabs-position">top</span></div>
                          </div>',
        "variables"=>array(
          'position'=>array(
            'name'=>'Position',
            'type'=>'dropdown',
            'default'=>'top',
            'dependents'=>array(
              'left'=>array('width'),
              'right'=>array('width')),
            'values'=>array(
              'top'=>'Top',
              'left'=>'Left',
              'right'=>'Right')),
            'width'=>array(
              'name'=>'Tab Width',
              'type'=>'textfield',
              'description'=>'Enter a size in px.',
              'type'=>'textfield',
              'units'=>array('px'),
              'default'=>'150px'),
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_tab_panel",array(
        "title"=>"Tab Panel",
        "admin_icon"=>"&#xe822;",
        "only_child"=>true,
        "width"=>"1-3-min",
        "parent_element"=>"bcorp_tabs",
        "admin_default"=>'<div class="bcve-bcorp_tab_panel">
                            <span class="bcve-bcorp_tab_panel-title bcve-bcorp_heading-text bcve-bcorp_heading-size-h3">Tab - Edit Me</span>
                            <div class="bcve-bcorp_tab_panel-textblock"><p>Tab Content - Edit Me</p></div>
                          </div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default' =>''),
          'showicon'=>array(
            'name'=>'Display Icon',
            'admin_tab'=>'Icon',
            'type'=>'checkbox',
            'default'=>'false',
            'dependents'=>array(
            'true'=>array('icon'))),
          'icon'=>array(
            'name'=>'Icon',
            'type'=>'icon',
            'default'=>'f005'),
          'textblock'=>array(
            'name'=>'Text Block',
            'type'=>'textarea',
            'editor'=>'tinymce',
            'default' =>''
          )
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_text",array(
        "title"=>"Text Block",
        "admin_icon"=>"&#xe828;",
        "admin_default"=>'<div class="bcve-bcorp_text">
                            <div class="bcve-bcorp_text-textblock">
                              <h2 style="text-align:center">Text Block - Edit Me</h2>
                            </div>
                          </div>',
        "variables"=>array(
          'textblock'=>array(
            'name'=>'Text Block',
            'type'=>'textarea',
            'editor'=>'tinymce',
            'default' =>''),
          'animation'=>'animation',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_archives",array(
        "title"=>"Archives",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_archives">
                            <div class="bcve-bcorp_wp_widgets">
                              <i class="bcve-icon bcve-header-icon">&#xe800;</i>
                              <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_archives-title">Archives</span></div>
                            </div>
                          </div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'Archives'),
          'heading'=>'heading',
          'count'=>array(
            'name'=>'Show post count',
            'type'=>'checkbox',
            'default'=>'false'),
          'dropdown'=>array(
            'name'=>'Display as dropdown',
            'type'=>'checkbox',
            'default'=>'false'),
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_calendar",array(
        "title"=>"Calendar",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_calendar">
                            <div class="bcve-bcorp_wp_widgets">
                              <i class="bcve-icon bcve-header-icon">&#xe800;</i>
                              <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_calendar-title">Calendar</span></div>
                            </div>
                          </div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'Calendar'),
          'heading'=>'heading',
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_categories",array(
        "title"=>"Categories",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_categories">
                            <div class="bcve-bcorp_wp_widgets">
                              <i class="bcve-icon bcve-header-icon">&#xe800;</i>
                              <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_categories-title">Categories</span></div>
                            </div>
                          </div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'Categories'),
          'heading'=>'heading',
          'showcount'=>array(
            'name'=>'Show post count',
            'type'=>'checkbox',
            'default'=>'false'),
          'dropdown'=>array(
            'name'=>'Display as dropdown',
            'type'=>'checkbox',
            'default'=>'false'),
          'hierarchical'=>array(
            'name'=>'Show hierarchy',
            'type'=>'checkbox',
            'default'=>'false'),
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_meta",array(
        "title"=>"Meta",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_meta">
                            <div class="bcve-bcorp_wp_widgets">
                              <i class="bcve-icon bcve-header-icon">&#xe800;</i>
                              <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_meta-title">Meta</span></div>
                            </div>
                          </div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'Meta'),
          'heading'=>'heading',
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_pages",array(
        "title"=>"Pages",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_pages"><div class="bcve-bcorp_wp_widgets"><i class="bcve-icon bcve-header-icon">&#xe800;</i>
      <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_pages-title">Pages</span></div></div></div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'Pages'),
          'heading'=>'heading',
          'sortby'=>array(
            'name'=>'Sortby',
            'type'=>'dropdown',
            'default'=>'title',
            'values'=>array(
              'title'=>'Page title',
              'menu_order'=>'Page order',
              'ID'=>'Page ID')),
          'exclude'=>array(
            'name'=>'Exclude',
            'type'=>'textfield',
            'default'=>''
          ),
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_recent_comments",array(
        "title"=>"Recent Comments",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_recent_comments"><div class="bcve-bcorp_wp_widgets"><i class="bcve-icon bcve-header-icon">&#xe800;</i>
      <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_recent_comments-title">Recent Comments</span></div></div></div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'Recent Comments'),
          'heading'=>'heading',
          'number'=>array(
            'name'=>'Number of comments to show',
            'type'=>'textfield',
            'default'=>'5'),
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_recent_posts",array(
        "title"=>"Recent Posts",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_recent_posts"><div class="bcve-bcorp_wp_widgets"><i class="bcve-icon bcve-header-icon">&#xe800;</i>
      <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_recent_posts-title">Recent Posts</span></div></div></div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'Recent Posts'),
          'heading'=>'heading',
          'number'=>array(
            'name'=>'Number of posts to show',
            'type'=>'textfield',
            'default'=>'10'),
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_rss",array(
        "title"=>"RSS",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_rss"><div class="bcve-bcorp_wp_widgets"><i class="bcve-icon bcve-header-icon">&#xe800;</i>
      <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_rss-title">RSS</span></div></div></div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'RSS'),
          'heading'=>'heading',
          'url'=>array(
            'name'=>'RSS or Atom feed URL to include',
            'type'=>'textfield',
            'default'=>''),
          'items'=>array(
            'name'=>'the number of RSS or Atom items to display',
            'type'=>'textfield',
            'default'=>'10'),
          'show_summary'=>array(
            'name'=>'Show Summary',
            'type'=>'checkbox',
            'default'=>'false'),
          'show_author'=>array(
            'name'=>'Show Author',
            'type'=>'checkbox',
            'default'=>'false'),
          'show_date'=>array(
            'name'=>'Show Date',
            'type'=>'checkbox',
            'default'=>'false'),
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_search",array(
        "title"=>"Search",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_search"><div class="bcve-bcorp_wp_widgets"><i class="bcve-icon bcve-header-icon">&#xe800;</i>
      <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_search-title">Search</span></div></div></div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'Search'),
          'heading'=>'heading',
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_tag_cloud",array(
        "title"=>"Tag Cloud",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_tag_cloud"><div class="bcve-bcorp_wp_widgets"><i class="bcve-icon bcve-header-icon">&#xe800;</i>
      <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_tag_cloud-title">Tag Cloud</span></div></div></div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'Tag Cloud'),
          'heading'=>'heading',
          'taxonomy'=>array(
            'name'=>'Taxonomy',
            'type'=>'dropdown',
            'default'=>'post_tag',
            'values'=>array(
              'category'=>'Categories',
              'post_tag'=>'Tags')),
          'idname'=>'idname',
        )
      )
    );

    $this->bcorp_add_shortcode(
      "bcorp_wp_text",array(
        "title"=>"Text",
        "admin_icon"=>'&#xe800;',
        "closing_tag"=>false,
        "admin_default"=>'<div class="bcve-bcorp_wp_text"><div class="bcve-bcorp_wp_widgets"><i class="bcve-icon bcve-header-icon">&#xe800;</i>
      <div class="bcve-bcorp_wp_widgets-details">Title: <span class="bcve-bcorp_wp_text-title">Text</span></div></div></div>',
        "variables"=>array(
          'title'=>array(
            'name'=>'Title',
            'type'=>'textfield',
            'default'=>'Text'),
          'heading'=>'heading',
          'text'=>array(
            'name'=>'Text',
            'type'=>'textfield',
            'default'=>''),
          'idname'=>'idname',
        )
      )
    );

    if ($bcorp_full_width_theme) $this->bcorp_add_shortcode(
      "bcorp_video",array(
        "title"=>"Video",
        "admin_icon"=>"&#xe827;",
        "admin_default"=>'<div class="bcve-bcorp_video"><i class="bcve-icon bcve-header-icon">&#xe827;</i><div class="bcve-bcorp_video-details">Location: <span class="bcve-bcorp_video-location">youtube</span><br />
      Video: <span class="bcve-bcorp_video-video">Video ID - Edit Me</span><br />
      Ratio: <span class="bcve-bcorp_video-ratio">wide</span><br />
      Size: <span class="bcve-bcorp_video-size">standard</span><br /></div></div>',
        "variables"=>array(
          'location'=>array(
            'name'=>'Video Location',
            'type'=>'dropdown',
            'dependents'=>array(
              'local'=>array('videourl'),
              'youtube'=>array('video','ratio','size'),
              'vimeo'=>array('video','ratio','size')),
            'default'=>'youtube',
              'values'=>array(
                'local'=>'Locally Hosted Video',
                'youtube'=>'You Tube',
                'vimeo'=>'Vimeo')),
          'video'=>array(
            'name'=>'Video ID',
            'type'=>'textfield',
            'default' =>''),
          'videourl'=>array(
            'name'=>'Local Video',
            'type'=>'video',
            'default' =>''),
          'ratio'=>array(
            'name'=>'Video Ratio',
            'type'=>'dropdown',
            'dependents'=>array(
              'custom'=>array('width','height')),
            'default'=>'wide',
            'values'=>array(
              'standard'=>'4:3',
              'wide'=>'16:9',
              'custom'=>'Custom')),
            'width'=>array(
              'name'=>'Width',
              'type'=>'textfield',
              'default' =>'16'),
            'height'=>array(
              'name'=>'Height',
              'type'=>'textfield',
              'default' =>'9'),
          'size'=>array(
            'name'=>'Video Size',
            'type'=>'dropdown',
            'default'=>'standard',
            'values'=>array(
              'standard'=>'Standard Video',
              'fullwidth'=>'Full Width Video',
              'fullscreen'=>'Full Screen Video',
            )
          )
        )
      )
    );
    else $this->bcorp_add_shortcode(
      "bcorp_video",array(
        "title"=>"Video",
        "admin_icon"=>"&#xe827;",
        "admin_default"=>'<div class="bcve-bcorp_video"><i class="bcve-icon bcve-header-icon">&#xe827;</i><div class="bcve-bcorp_video-details">Location: <span class="bcve-bcorp_video-location">youtube</span><br />
      Video: <span class="bcve-bcorp_video-video">Video ID - Edit Me</span><br />
      Ratio: <span class="bcve-bcorp_video-ratio">wide</span><br />
      Size: <span class="bcve-bcorp_video-size">standard</span><br /></div></div>',
        "variables"=>array(
          'location'=>array(
            'name'=>'Video Location',
            'type'=>'dropdown',
            'dependents'=>array(
              'local'=>array('videourl'),
              'youtube'=>array('video','ratio','size'),
              'vimeo'=>array('video','ratio','size')),
            'default'=>'youtube',
              'values'=>array(
                'local'=>'Locally Hosted Video',
                'youtube'=>'You Tube',
                'vimeo'=>'Vimeo')),
          'video'=>array(
            'name'=>'Video ID',
            'type'=>'textfield',
            'default' =>''),
          'videourl'=>array(
            'name'=>'Local Video',
            'type'=>'video',
            'default' =>''),
          'ratio'=>array(
            'name'=>'Video Ratio',
            'type'=>'dropdown',
            'dependents'=>array(
              'custom'=>array('width','height')),
            'default'=>'wide',
            'values'=>array(
              'standard'=>'4:3',
              'wide'=>'16:9',
              'custom'=>'Custom')),
            'width'=>array(
              'name'=>'Width',
              'type'=>'textfield',
              'default' =>'16'),
            'height'=>array(
              'name'=>'Height',
              'type'=>'textfield',
              'default' =>'9'),
          'size'=>array(
            'name'=>'Video Size',
            'type'=>'dropdown',
            'default'=>'standard',
            'values'=>array(
              'standard'=>'Standard Video',
            )
          )
        )
      )
    );


  }
}

?>
