<?php
/*
Plugin Name: BCorp Shortcodes
Plugin URI: http://www.bcorp.com
Description: Advanced word press shortcodes for use with any wordpress theme.
Version: 0.23
Author: Tim Brattberg
Author URI: http://www.bcorp.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
*/

require_once(plugin_dir_path( __FILE__ ). 'bcorp_shortcodes_data.php' );

add_action('plugins_loaded', 'bcorp_plugins_loaded');
function bcorp_plugins_loaded() {
  add_action('init', 'bcorp_shortcodes_init');
}

function bcorp_shortcodes_init() {
  add_image_size('840 x 420 cropped', 840, 420, true);
  $GLOBALS['bcorp_shortcodes_data'] = new BCorp_SC_Data();
  $GLOBALS['bcorp_shortcodes'] = new BCorp_Short_Codes();
  do_action('bcorp_shortcodes_extra');
  do_action('bcorp_start_visual_editor');
}

class BCorp_Short_Codes {
  public function __construct ()
  {
    if (!is_admin()) $this->setup(); else $this->admin_setup();
  }

  private function setup () {
    add_action('wp_enqueue_scripts', array(&$this,'enqueue_scripts'));
    add_filter('the_content', array(&$this,'shortcode_empty_paragraph_fix' ));
  }

  private function admin_setup() {
    add_action('media_buttons', array(&$this,'add_media_button'));
    add_action('admin_enqueue_scripts', array(&$this,'admin_enqueue_scripts'));
    add_action('wp_ajax_bcorp_blog_more', array(&$this,'bcorp_blog_more' ));
    add_action('wp_ajax_nopriv_bcorp_blog_more', array(&$this,'bcorp_blog_more' ));
  }

  function enqueue_scripts() {
    global $bcorp_full_width_theme;
    wp_enqueue_style('bcsc',plugins_url( 'css/bcsc.css' , __FILE__ ));
    wp_enqueue_script('bcsc_js',plugins_url('js/bcsc.js', __FILE__ ),'','',true);
    if ($bcorp_full_width_theme) wp_enqueue_script('bcsc_full_js',plugins_url('js/bcsc-full.js', __FILE__ ),'','',true);
    wp_enqueue_script('imagesloaded', plugins_url('/js/imagesloaded.pkgd.min.js', __FILE__ ),'','',true);
    wp_enqueue_script('waypoints', plugins_url('/js/noframework.waypoints.min.js', __FILE__ ),'','',true);
    wp_enqueue_script('magnific_js',plugins_url('/js/magnific.min.js', __FILE__ ),'','',true);
    wp_enqueue_script('isotope', plugins_url('/js/isotope.pkgd.min.js', __FILE__ ),'','',true);
    wp_enqueue_style('animate_css',plugins_url('/css/animate.min.css', __FILE__ ));
    wp_localize_script("bcsc_js","bcorp_shortcodes",
      array("admin_ajax" => admin_url("admin-ajax.php"),
        "bcorp_blog_more" => wp_create_nonce( 'data-blog-more-nonce'),
      ));
  }

  function add_media_button() {
    echo '<a href="#" id="bcorp-shortcodes-button" class="button"><span class="bcsc-icon bcsc-bcorp" style="position:relative; top:-4px; margin-right:4px;"></span>Shortcodes</a>';
  }

  function admin_enqueue_scripts() {
    wp_enqueue_script('iris');
    wp_enqueue_style('bcorp_css_admin',plugins_url( 'css/bcsc-admin.css' , __FILE__ ));
    wp_enqueue_script('bcorp_js_admin',plugins_url('js/bcsc-admin.js', __FILE__ ),'','',true);
    wp_localize_script("bcorp_js_admin","bcsc",array('sc' => $GLOBALS['bcorp_shortcodes_data']->bcsc(),
                                                          'vars' => $GLOBALS['bcorp_shortcodes_data']->bcsc_vars()));
  }

  public function register_tinymce_javascript( $plugin_array ) {
     $plugin_array['bcorp_shortcodes'] = plugins_url( '/js/bcsc-tinymce-plugin.js',__FILE__ );
     return $plugin_array;
  }

	public function shortcode_empty_paragraph_fix($content) {
  	  $array = array (
  	      '<p>[' => '[',
  	      ']</p>' => ']',
  	      ']<br />' => ']'
  	  );
  	  return strtr($content, $array);
	}

  function register_button($buttons) {
		array_push($buttons, "bcorp_shortcodes");
		return $buttons;
	}

  function bcorp_accordion_shortcode($atts,$content=null,$tag) {
    /* [bcorp_accordion]
       multiple (true,false)
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    static $bcorp_id;
    $bcorp_id++;
    $output = '<div class="bcorp-accordion bcorp-accordion-multiple-'.$data['multiple'].'" id="bcorp-accordion-'.$bcorp_id.'">'.do_shortcode($content).'</div>';
    return $output;
  }

  function bcorp_accordion_panel_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_accord]
       open (true,false)
       title
       textblock
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    return '<p class="bcorp-heading bcorp-accordion-header bcorp-accordion-header-hidden bcorp-accordion-open-'.$data['open'].'">'.$data['title'].'</p><div class="bcorp-accordion-content bcorp-alt-background">'.do_shortcode($content).'</div>';
  }

  function bcorp_alert_box_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_alert_box]
     * message
     * type (info,success,warning,error,custom)
     * ->custom
     *     backgroundcolor
     *     textcolor
     *     bordercolor
     * border (none,solid,thick,dashed)
     * size (medium,large)
     * align (left,center,right)
     * showicon (true,false)
     * ->true
     *     icon
     * idname
     * class
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    if ($data['iconcolor']) $iconcolor = 'style="color:'.$data['iconcolor'].';"'; else $iconcolor = '';
    if ($data['idname']) $idname = 'id ="'.$data['idname'].'" '; else $idname = '';
    if ($data['type']=='custom') $custom_colors = 'background-color:'.$data['backgroundcolor'].'; border-color:'.$data['bordercolor'].'; color:'.$data['textcolor'].';'; else $custom_colors='';
    if (($data['icon']) && ($data['showicon'])) $icon= '<span class="bcorp-alert-box-icon" aria-hidden="true" data-icon="&#x'.$data['icon'].';" '.$iconcolor.'></span>'; else $icon="";
    return '<div '.$idname.'class="'.$data['class'].' bcorp-alert-box bcorp-alert-box-'.$data['type'].' bcorp-alert-box-padding-'.$data['size'].' bcorp-alert-box-border-'.$data['border'].'" style="'.$custom_colors.' text-align:'.$data['align'].';">'.$icon.do_shortcode($content).'</div>';
  }

  function get_setting($setting1,$setting2){
  	global $bcorp_settings;
  	$setting=json_decode($bcorp_settings[$setting1], true);
  	return $setting[$setting2];
  }

  function fullwidth_start($size) {
    global $bcorp_section_id;
    $bcorp_section_id++;
    return '</div></div></section><section id="bcorp-section-'.$bcorp_section_id.'" class="bcorp-'.$size.' bcorp-color-main">';
  }

  function fullwidth_end() {
    return '</section><section class="content-area bcorp-color-main"><div class="site-content"><div class="bcorp-row">';
  }

  function bcorp_blog_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_blog]
     * fullwidth (true,false)
     * filterby (category,tag,formats,portfolios)
     * ->category
     *     categories
     * ->tag
     *     tags
     * ->formats
     *     formats
     * ->portfolios
     *     portfolios
     *     portfoliolink (ajax,lightbox,portfolio)
     * excerpt (true, false)
     * format (full, excerpt, none)
     * columns (1-6)
     * gutter
     * bottommargin
     * wrappermargin
     * size (automatic,custom)
     * ->custom
     *     customsize
     * count
     * offset
     * filter
     * more (none,loadmore,paged)
     * border (default,show,none)
     * showheading
     * ->true
     *    heading (custom,h1,h2,h3,h4,h5,h6)
     *    ->custom
     *       headingsize
     *    headingcolor
     *    headingalign (left,center,right)
     *    headingmargintop
     *    headingmarginbottom
     * animation^
     * share (true,false)
     * metalocation (above,below)
     * metamargintop
     * metamarginbottom
     * metastyle (style1,style2)
     * metaalign (left,center,right)
     * metatype
     * metadate
     * metacategory
     * metaauthor
     * metatags
     * metacomments
     * metaedit
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data('bcorp_blog',$atts);
    global $post;
    static $bcorp_blog_instance = 0;
    $bcorp_blog_instance++;
    $ajax_extras='';
    $page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : (get_query_var( 'page' ) ? get_query_var( 'page' ) : 1);
    $paged_offset = ( $page - 1 ) * $data['count'] +$data['offset'];
    $args = array(
      'offset' => $paged_offset,
      'posts_per_page' => $data['count'],
      'cat' => $data['categories'],
      'paged' => $page
    );
    switch ($data['filterby']) {
      case "formats":
        $dataformats = explode(",", $data['formats']);
        if (in_array ( 'standard' , $dataformats )) {

          $formats =  array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-status', 'post-format-audio', 'post-format-chat', 'post-format-video');
          foreach ($dataformats as $format) {
            if(($key = array_search($format, $formats)) !== false) {
              unset($formats[$key]);
            }

          }
          $args['tax_query'] = array( array(
                      'taxonomy' => 'post_format',
                      'field' => 'slug',
                      'terms' => $formats,
                      'operator' => 'NOT IN'
                    ) );
        } else {
          $args['tax_query'] = array( array(
                      'taxonomy' => 'post_format',
                      'field' => 'slug',
                      'terms' => $dataformats,
                    ) );
        }
        break;
      case "portfolios":
        $args['post_type']  = 'portfolio';
        if (strlen($data['portfolios'])) {
          $args['tax_query'] = array(
            array(
              'taxonomy' => 'portfolio-category',
              'field'    => 'name',
              'terms'    => explode(",", $data['portfolios'])
            ),
          );
        }
        $terms = get_terms('portfolio-category');
        break;
      case "tag":
        if (!isset($data['tags'])) $tags=''; else $args['tag__in'] = explode(",",$data['tags']);
        break;
      default:
        $terms = get_terms('category');
    }
    $posts = new WP_Query( $args );
    if ($data['size'] == 'custom') $thumb_size = $data['customsize']; else $thumb_size = '840 x 420 cropped';
    ob_start();
    if ($data['fullwidth'] == 'true') echo $this->fullwidth_start('fullwidth');
    if ($data['portfoliolink'] == 'ajax') echo '<div id="bcorp-blog-ajax-'.$bcorp_blog_instance.'" class="bcorp-blog-ajax"></div>';
    echo '<div class="bcorp-blog bcorp-';
    if ($data['filterby']=='portfolios') echo 'portfolio'; else echo 'blog';
    if ($data['columns']>1) echo '-multi">'; else echo '-single">';
    if ($data['columns']>1) {
      if ($data['filter'] =='true') { ?>
        <div class="bcorp-blog-filter button-group">
          <a href="#" data-filter="*">All</a><?php
           if (isset($terms)) foreach ( $terms as $term ) {
              echo ' <a href="#" data-filter=".'.$term->slug.'-'.$bcorp_blog_instance.'">'. $term->name .'</a>';
           } ?>
        </div> <?php
      }
      $grid_sizer = (100-($data['columns']-1)*$data['gutter'])/$data['columns'];

      ?>
      <div id="bcorp-blog-content-<?php echo $bcorp_blog_instance; ?>" class="bcorp-blog-content bcorp-blog-masonry" style="margin: 0 <?php echo $data['wrappermargin']; ?>%;">
        <div class="grid-sizer" style="width:<?php echo $grid_sizer; ?>%; "></div>
        <div class="gutter-sizer" style="width:<?php echo $data['gutter']; ?>%;"></div><?php
    } else {
      ?>
      <div id="bcorp-blog-content-<?php echo $bcorp_blog_instance; ?>" class="bcorp-blog-content bcorp-blog-content-single"><?php
      $grid_sizer = 100;
    }
    if ($data['heading'] == 'custom') { $heading='h1'; $headingdetails=' font-size:'.$data['headingsize'].'; '; }
    else { $heading = $data['heading']; $headingdetails=''; }

    $headingcustomcolor = '';
    if ($data['headingcolor'] == 'custom') {
      $headingcolor = '';
      if ($data['headingcustomcolor']) $headingcustomcolor = ' style="color:'.$data['headingcustomcolor'].';"';
    } elseif ($data['headingcolor'] == 'altfont') $headingcolor = ' bcorp-color-font-alt';
    elseif ($data['headingcolor'] == 'font') $headingcolor = ' bcorp-color-font';
    elseif ($data['headingcolor'] == 'link') $headingcolor = ' bcorp-color-link';
    else $headingcolor =' bcorp-color-heading';


    if ($data['heading'] == 'default') {
      if ($data['filterby']=='portfolios') {
        if ($data['columns']>1) $heading = esc_attr($this->get_setting('multi_portfolio','heading_size'));
        else $heading = esc_attr($this->get_setting('single_portfolio','heading_size'));
      } else {
        if ($data['columns']>1) $heading = esc_attr($this->get_setting('multi_blog','heading_size'));
        else $heading = esc_attr($this->get_setting('single_blog','heading_size'));
      }
      if(!$heading) $heading = 'h3';
    }

    $headingdetails .= 'margin-top:'.$data['headingmargintop'].'; margin-bottom:'.$data['headingmarginbottom'].';  text-align:'.$data['headingalign'].';';

    $article_class = '';
    $article_styling = '';
    if ($data['bordercolor']) $article_styling.='border-color:'.$data['bordercolor'].';';
    $article_styling .= ' border-top-width:'.$data['bordertop'].'; border-bottom-width:'.$data['borderbottom'].'; border-left-width:'.$data['borderleft'].'; border-right-width:'.$data['borderright'].';';

    $padding = 'padding-left:'.$data['padding'].' !important; padding-right:'.$data['padding'].'!important;';

    $blog_start = ob_get_clean();
    ob_start();
    while ( $posts->have_posts() ) {
	    $posts->the_post();
      switch ($data['filterby']) {
        case "portfolios":
          $terms = get_the_terms( '', 'portfolio-category' );
          break;
        default:
          $terms = get_the_terms( '', 'category' );
      }
      $post_class = "bcorp-blog-item bcorp-animated ";
      if ($data['backgroundcolor'] == 'alt') $post_class .= ' bcorp-alt-background';
      if (is_array($terms)) foreach ( $terms as $term ) $post_class.= " ".$term->slug."-".$bcorp_blog_instance; ?>
      <div <?php post_class($post_class); ?> id="post-<?php the_ID(); ?>" data-animation="<?php echo $data['animation']; ?>" style="width:<?php echo $grid_sizer; ?>%; margin-bottom:<?php echo $data['bottommargin'].';'.$article_styling; ?>">
      <div class="bcorp-blog-item-inner">
        <article id="post-<?php echo the_ID(); ?>-<?php echo $bcorp_blog_instance; ?>" <?php post_class($article_class); ?>><?php

          $postcontent='';
          $blog_link = get_permalink();
          $bcorp_continue =  esc_html__( 'Read More', 'bcorp-shortcodes' );
          $full_ob_get_clean = get_the_content($bcorp_continue);
          $original_content = get_post( get_the_ID())->post_content;
          $blog_content = strip_shortcodes(wp_trim_words( $original_content, $num_words = 55, $more = null ));

          $showheading = $data['showheading'];
          $showmeta = 'true';
          switch (get_post_format()) {
            case "gallery":
              $full_blog_content = get_the_content($bcorp_continue);
              preg_match_all('/\[gallery(.*?)\]/s', $full_blog_content, $matches);
              if (isset($matches[0][0])) {
                preg_match_all('/ids\=\"(.*?)\"/s', $matches[0][0], $matches);
                $slides = explode(",",substr($matches[0][0],5,strlen($matches[0][0])-6));
/*                $transitions ='{$Duration:1200,$Opacity:2}';
                $first = true;
                foreach ($slides as $slide) {
                  if ($first){
                    $width = wp_get_attachment_image_src( $slide, $thumb_size )[1];
                    $height = wp_get_attachment_image_src( $slide, $thumb_size )[2];
                    $postcontent = '<div class="bcorp-slider bcorp-standard" data-transitions="'.$transitions.'" style="position: relative; top: 0px; left: 0px; width: '.$width.'px; height: '.$height.'px;">';
                    $postcontent .= '<div data-u="slides" style="cursor: move; position: absolute; overflow: hidden; left: 0px; top: 0px; width: '.$width.'px; height: '.$height.'px;">';
                    $first = false;
                  }
                  $postcontent .= '<div><img data-u="image" src="'.wp_get_attachment_image_src( $slide, $thumb_size )[0].'" alt="'.get_post_meta($slide, '_wp_attachment_image_alt', true).'" /></div>';
                }
                $postcontent .= '</div>';
                $arrow = 2;
                $arrowpadding = 8;
                $postcontent .= '<span data-u="arrowleft" class="jssora'.$arrow.'l" style="top: 123px; left: '.$arrowpadding.'px;"></span>
                      <span data-u="arrowright" class="jssora'.$arrow.'r" style="top: 123px; right: '.$arrowpadding.'px;"></span>
                      <div data-u="navigator" class="jssorb1" style="bottom: 16px; right: 10px;"><div data-u="prototype"></div></div>
                      </div>'; */

                $postcontent = do_shortcode('[bcorp_gallery '.$matches[0][0].' preview="true" caption="true" previewsize="840 x 420 cropped" columns="3" thumbsize="thumbnail" link="lightbox" largesize="large" animation="none"]');
                break;
              }
            case "quote":
              if($data['columns']>1) $linebreak = '<br />'; else $linebreak = '';
              $postcontent = '<div class="entry-content entry-quote" style="'.$padding.'">'.$linebreak.$full_ob_get_clean.'</div><br /><br />';
              $showheading = 'false';
              $showmeta = 'false';
              break;
            case "aside":
            case "status":
              if($data['columns']>1) $linebreak = '<br />'; else $linebreak = '';
              $postcontent = '<div class="entry-content bcorp-format-status" style="'.$padding.'">'.$linebreak.'<p>'.$blog_content.'</p></div>';
              $showheading = 'false';
              $showmeta = 'false';
              break;
            case "link":
              $blog_link = get_url_in_content($blog_content);
              break;
            case "audio":
            case "video":
              ob_start();
              the_content($bcorp_continue);
              $full_blog_content = ob_get_clean();
              preg_match_all('/\<iframe(.*?)\<\/iframe\>/', $full_blog_content, $matches);
              $videoratio=9/16*100;
              if (isset($matches[0][0])) {
                $remaining_content = str_replace($matches[0][0],'',$full_blog_content);
                $remaining_content = preg_replace('~<p>\s*<\/p>~i','',$remaining_content);
                $postcontent = '<div class="bcorp-video" style="padding-bottom: '.$videoratio.'%;">'.$matches[0][0].'</div>';
                break;
              } else $remaining_content = $full_blog_content;
            default:
              $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), $thumb_size)[0];
              if ($featured_image) {
                $postcontent = '<div class="bcorp-blog-thumbnail">';
                if ($data['portfoliolink'] == 'lightbox') $postcontent .= '<a href="'.wp_get_attachment_image_src( get_post_thumbnail_id(), 'large')[0].'" data-rel="prettyPhoto"';
                elseif ($data['portfoliolink'] == 'ajax') {
                  $ajax_extras = ' data-rel="bcorp-ajax-portfolio" data-id="'.get_the_id().'" data-ajax="'.$bcorp_blog_instance.'"';
                  $postcontent .= '<a href="'.get_the_permalink().'"'.$ajax_extras;
                } else  $postcontent .= '<a href="'.get_the_permalink().'"';
                $postcontent .=  ' title="'.get_the_title().'"><img src="'.$featured_image.'" alt="'.get_the_title().'"></a></div>';
              }
          }

          if ($showheading == 'true') {
            $posttitle = '<'.$heading.' class="entry-title" style="'.$headingdetails.'"><a href="' . esc_url( $blog_link ) . '" class="'.$headingcolor.'" rel="bookmark"'.$ajax_extras.' '.$headingcustomcolor.'>'.get_the_title().'</a></'.$heading.'>';
          } else $posttitle = '';

          if (($data['metatype'] == 'true') && get_post_format()) {
            $posttype = '<span class="post-format"><a class="entry-format" href="'. get_post_format_link( get_post_format() ) .'">'.get_post_format_string( get_post_format() ).'</a></span>';
          } else $posttype = '';

          if ($data['metadate'] == 'true') {
            if ( is_sticky() && is_home() && ! is_paged() ) {
              $postdate = '<span class="featured-post">' . esc_html__( 'Sticky', 'bcorp-shortcodes' ) . '</span>';
            } else {
              $postdate = sprintf( '<span class="entry-date"><time datetime="%1$s">%2$s</time></span>',esc_attr(get_the_date('c')),esc_html(get_the_date()));
            }
          } else $postdate = '';

          if ($data['metacategory'] == 'true'){
            if ($data['filterby'] == 'portfolios') {
              if (in_array('portfolio-category',get_object_taxonomies('portfolio')))
                  $postcategory = '<span class="cat-links">'. get_the_term_list('','portfolio-category','',_x( ', ', 'Used between list items, there is a space after the comma.', 'bcorp-shortcodes') ,'' ).'</span>';
            } elseif (in_array('category',get_object_taxonomies(get_post_type())))
                $postcategory = '<span class="cat-links">'.get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'bcorp-shortcodes' ) ).'</span>';
            else $postcategory = '';
          } else $postcategory = '';

          if ($data['metaauthor'] == 'true') {
            $postauthor = sprintf( '<span class="author vcard byline"> by <a class="url fn n" href="%1$s" rel="author">%2$s</a></span>',get_author_posts_url(get_the_author_meta('ID')),get_the_author());
          } else $postauthor = '';

          if ($data['metatags'] == 'true') {
            ob_start();
            the_tags( ' <span class="tag-links">', ', ', '</span>' );
            $posttags = ob_get_clean();
          } else $posttags = '';

          if ($data['metacomments'] == 'true') {
    				ob_start();
    				if (!post_password_required()) { ?>
    					<span class="comments-link"><?php comments_popup_link( esc_html__( 'Leave a comment', 'bcorp-shortcodes' ), esc_html__( '1 Comment', 'bcorp-shortcodes' ), esc_html__( '% Comments', 'bcorp-shortcodes' ),'', esc_html__('Comments Off','bcorp-shortcodes')); ?></span><?php
    				}
    				$postcomments = ob_get_clean();
    			} else $postcomments ='';

          if ($data['metaedit'] == 'true' && get_edit_post_link()) {
            $postedit = '<span class="edit-link"><a href="'.get_edit_post_link().'">'.esc_html__( 'Edit', 'bcorp-shortcodes' ).'</a></span>';
          } else $postedit ='';

          if ($data['metalocation'] == 'below') echo $postcontent;


          if ($showmeta == 'true') switch ($data['metastyle']) {
            case "style2":
              $catsize = (intval($data['metasize'])*1.3).preg_replace('/[0-9]+/', '', $data['metasize']);
              echo '<header class="entry-header bcorp-entry-style2" style="'.$padding.'"><div style="text-align:'.$data['metaalign'].'; font-size: '.$catsize.'; margin-top:'.$data['metamargintop'].';">'.$postcategory.'</div><div>'.$posttitle.'</div><div class="entry-meta" style="font-size: '.$data['metasize'].'; line-height: '.$data['metalineheight'].'; text-align:'.$data['metaalign'].'; margin-bottom:'.$data['metamarginbottom'].';">';
              echo $posttype.$postdate.$postauthor.$posttags.$postcomments.$postedit.'</div></header>';
              break;
            default:
              echo '<header class="entry-header bcorp-entry-style1" style="'.$padding.'">'.$posttitle.'<div class="entry-meta" style="font-size: '.$data['metasize'].'; line-height: '.$data['metalineheight'].';  text-align:'.$data['metaalign'].'; margin-top:'.$data['metamargintop'].';  margin-bottom:'.$data['metamarginbottom'].';">';
              if ($postcategory) $postcategory = ' in '.$postcategory;
              echo $posttype.$postdate.$postcategory.$postauthor.$posttags.$postcomments.$postedit.'</div></header>';
          }

          if ($data['metalocation'] == 'above') echo $postcontent;

          if (!get_post_format() || get_post_format()=='video' || get_post_format()=='audio' || get_post_format()=='gallery' || get_post_format()=='image') { if ($data['format'] !='none') { ?>
            <div class="entry-content" style="<?php echo $padding; ?>"><?php
              if ($data['format']=='full' && !get_post_format()) the_content(__('Read More','bcorp-shortcodes'));
              else if (get_post_format()=='video' || get_post_format()=='audio') {
                  echo '<p>'.$remaining_content;
              } else  if (get_post_format()=='gallery') {
                echo '<p>'.$blog_content; ?>
                  <br /><a href="<?php echo esc_url( $blog_link ); ?>" class="more-link"><?php esc_html_e( 'Read More', 'bcorp-shortcodes' );?></a><?php
              } else {
                echo '<p>'.$blog_content;
                if ($data['format']=='excerpt_more') { ?>
                  <br /><a href="<?php echo esc_url( $blog_link ); ?>" class="more-link"><?php esc_html_e( 'Read More', 'bcorp-shortcodes' );?></a><?php
                }
                echo "</p>";
              }
              if ($data['share'] == 'true') {
                global $bcorp_settings;
            		$blog=json_decode($bcorp_settings['blog'], true);
            		$bcorp_share ='';
            		if ($blog['share_facebook']=='true') $bcorp_share .= '<li class="bcorp-social-facebook"><a href="http://www.facebook.com/sharer.php?u='.get_permalink().'" class="icon-facebook bcorp-social-icon-share" target="_blank"></a></li>';
            		if ($blog['share_twitter']=='true') $bcorp_share .= '<li class="bcorp-social-twitter"><a href="https://twitter.com/share?text='.esc_url(get_the_title()).'&amp;url='.get_permalink().'" class="icon-twitter bcorp-social-icon-share" target="_blank"></a></li>';
            		if ($blog['share_google_plus']=='true') $bcorp_share .= '<li class="bcorp-social-google-plus"><a href="https://plus.google.com/share?url='.get_permalink().'" class="icon-google-plus bcorp-social-icon-share" target="_blank"></a></li>';
            		if ($blog['share_tumblr']=='true') $bcorp_share .= '<li class="bcorp-social-tumblr"><a href="	http://www.tumblr.com/share/link?name='.esc_url(get_the_title()).'&amp;description='.esc_url(get_the_excerpt()).'&amp;url='.get_permalink().'" class="icon-tumblr bcorp-social-icon-share" target="_blank"></a></li>';
            		if ($blog['share_reddit']=='true') $bcorp_share .= '<li class="bcorp-social-reddit"><a href="http://reddit.com/submit?title='.esc_url(get_the_title()).'&amp;url='.get_permalink().'" class="icon-reddit bcorp-social-icon-share" target="_blank"></a></li>';
            		if ($blog['share_linkedin']=='true') $bcorp_share .= '<li class="bcorp-social-linkedin"><a href="http://linkedin.com/shareArticle?mini=true&amp;title='.esc_url(get_the_title()).'&amp;url='.get_permalink().'" class="icon-linkedin bcorp-social-icon-share" target="_blank"></a></li>';
            		if ($blog['share_pinterest']=='true') $bcorp_share .= '<li class="bcorp-social-pinterest"><a href="http://pinterest.com/pin/create/button/?description='.esc_url(get_the_title()).'&amp;media=&amp;url='.get_permalink().'" class="icon-pinterest bcorp-social-icon-share" target="_blank"></a></li>';
            		if ($blog['share_vk']=='true') $bcorp_share .= '<li class="bcorp-social-vk"><a href="http://vk.com/share.php?url='.get_permalink().'" class="icon-vk bcorp-social-icon-share" target="_blank"></a></li>';
            		if ($blog['share_email']=='true') $bcorp_share .= '<li class="bcorp-social-email"><a href="mailto:?subject='.esc_url(get_the_title()).'&amp;body='.get_permalink().'" class="icon-email bcorp-social-icon-share" target="_blank"></a></li>';
            		if ($blog) echo '<div class="bcorp-share-box-wrap"><ul class="bcorp-share-box">'.$bcorp_share.'</ul></div>';
              }
            echo "</div>";
          }
        } ?>
      </article>
        </div>
      </div><?php
    }
    $blog_content = ob_get_clean();

    $args['offset']+=$data['count'];
    $args['posts_per_page']=1;
    $moreposts = new WP_Query( $args );

    if ($tag == 'bcorp_blog_ajax') return array($moreposts->have_posts(),$blog_content);

    ob_start();
    if (($data['more'] == 'paged') && ( $posts->max_num_pages > 1 )) {
      if ($data['columns'] >1) echo '</div><div class="bcorp-blog-paging">';
      $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : (get_query_var( 'page' ) ? get_query_var( 'page' ) : 1);

      $pagenum_link = rawurldecode( get_pagenum_link() );
      $query_args   = array();
      $url_parts    = explode( '?', $pagenum_link );
      if ( isset( $url_parts[1] ) ) {
        wp_parse_str( $url_parts[1], $query_args );
      }
      $pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
      $pagenum_link = trailingslashit( $pagenum_link ) . '%_%';
      $pageformat  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
      $pageformat .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';
      $links = paginate_links( array(
        'base'     => $pagenum_link,
        'format'   => $pageformat,
        'total'    => $posts->max_num_pages,
        'current'  => $paged,
        'mid_size' => 1,
        'add_args' => array_map( 'urlencode', $query_args ),
        'prev_text' => esc_html__( '&larr; Previous', 'bcorp-shortcodes' ),
        'next_text' => esc_html__( 'Next &rarr;', 'bcorp-shortcodes' ),
      ) );
      if ( $links ) { ?>
        <nav class="navigation paging-navigation" role="navigation">
          <div class="pagination loop-pagination">
            <?php echo $links; ?>
            </div>
          </nav><?php
      }
    } ?>
    </div><?php
    wp_reset_postdata();
    if (($data['more'] == 'loadmore') && $moreposts->have_posts()) echo '<button class="bcorp-blog-more load-more-link" data-info="'.base64_encode(serialize($atts)).'" data-blogID="bcorp-blog-content-'.$bcorp_blog_instance.'" data-clicks="0">'.esc_html__('Load More','bcorp-shortcodes').'</button>';

    echo "</div>";
    if ($data['fullwidth'] == 'true') echo $this->fullwidth_end();
    return $blog_start.$blog_content.ob_get_clean();
  }

  function bcorp_blog_more(){
    $nonce = $_POST['bcorp_nonce'];
    if (!wp_verify_nonce($nonce,'data-blog-more-nonce')) wp_die();
    $blogID = $_POST['blogID'];
    $data = $_POST['data'];
    $atts= unserialize(base64_decode($data));
    $atts['filter']='false';
    $atts['offset']+=$atts['count']*$_POST['clicks'];
    $html =  $this->bcorp_blog_shortcode( $atts,"","bcorp_blog_ajax");
    $response = json_encode(array('success'=>true,'blogID'=>$blogID,'html' => $html[1],'more'=>$html[0]));
    header( "Content-Type: application/json" );
    echo $response;
    exit;
  }

  function bcorp_button_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_button]
     * label
     * icon
     * link^
     * color
     * hovercolor
     * textcolor
     * hovertextcolor
     * transparent (true,false)
     * bordercolor
     * hoverbordercolor
     * thickness
     * radius
     * size (small,medium,large)
     * bold (true,false)
     * align (left,center,right)
     * animation^
     * margins^
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    $href=$this->bcorp_link($data['link'],$data['linkurl'],$data['linksection'],$data['linkpost'],$data['linkpage'],$data['linkportfolio'],$data['linkcategory'],$data['linktag'],$data['linkportfoliocategory'],$data['linkformat'],$data['linktarget']);
    if ($data['color']) $data['color'] = 'background-color:'.$data['color'].';';
    if ($data['transparent']=='true') $data['color'] = 'background-color:transparent;';
    if ($data['hovercolor']) $data['hovercolor'] = 'background-color:'.$data['hovercolor'].';';
    if ($data['textcolor']) $data['textcolor']  = 'color:'.$data['textcolor'].';';
    if ($data['hovertextcolor']) $data['hovertextcolor']  = 'color:'.$data['hovertextcolor'].';';
    if ($data['iconcolor']) $data['iconcolor']  = 'color:'.$data['iconcolor'].';';
    if ($data['thickness']) $data['thickness'] = ' border-width: '.$data['thickness'].';';
    if ($data['bordercolor']) $data['bordercolor'] = ' border-color: '.$data['bordercolor'].';';
    if ($data['radius']) $data['radius'] = ' border-radius: '.$data['radius'].';';
    if ($data['hoverbordercolor']) $data['hoverbordercolor'] = ' border-color: '.$data['hoverbordercolor'].';';
    if ($data['bold']=='true') $data['bold'] = ' font-weight:bold;'; else $data['bold'] = '';
    if ($data['margintop']) $data['margintop'] = 'margin-top:'.$data['margintop'].';';
    if ($data['marginright']) $data['marginright'] = 'margin-right:'.$data['marginright'].';';
    if ($data['marginbottom']) $data['marginbottom'] = 'margin-bottom:'.$data['marginbottom'].';';
    if ($data['marginleft']) $data['marginleft'] = 'margin-left:'.$data['marginleft'].';';
    if ($data['align'] != 'center') {
      $data['align'] = 'float:'.$data['align'].';';
      $centered = '';
    } else {
      $centered = ' bcorp-button-centered';
      $data['align'] = '';
    }
    $iconleft = '';
    $iconright = '';
    if ($data['showicon'] != 'none' && $data['icon']) {
      $icon = '<span class="bcorp-button-icon bcorp-button-icon-'.$data['showicon'].'" aria-hidden="true" data-icon="&#x'.$data['icon'].';" style="'.$data['iconcolor'].'"></span>';
      if ($data['showicon'] == 'left') $iconleft = $icon; else $iconright = $icon;
    }
    return '<div class="bcorp-button bcorp-button-'.$data['colors'].$centered.' bcorp-animated"  data-animation="'.$data['animation'].'" style="'.$data['align']
    .$data['margintop'].$data['marginright'].$data['marginbottom'].$data['marginleft'].'"><a href="'.$href['link'].'"'.$href['target']
      .' style ="'.$data['hovercolor'].$data['hovertextcolor'].$data['hoverbordercolor'].'"><div class="bcorp-button-hover bcorp-button-'.$data['size'].'" style="'.$data['color'].$data['textcolor'].$data['bold']
      .$data['thickness'].$data['radius'].$data['bordercolor'].'">'.$iconleft.$data['label'].$iconright.'</div></a></div>';
  }

  function bcorp_cell_classes_reset($tag) {
    global $bcorp_cell_position, $bcorp_cell_position_small;
    $bcorp_cell_position[$tag] = 0;
    $bcorp_cell_position_small[$tag] = 0;
  }

  function bcorp_cell_classes( $tag,$width,$mobilewidth) {
    $gutter_counter = array("1-8"=>1/8,"1-7"=>1/7,"1-6"=>1/6,"1-6"=>1/6,"1-5"=>0.2,"1-4"=>0.25,"1-3"=>1/3,"2-5"=>2/5,
                            "1-2"=>1/2,"3-5"=>3/5,"2-3"=>2/3,"3-4"=>3/4,"4-5"=>4/5,"5-6"=>5/6,"1-1"=>1);

    global $bcorp_cell_position, $bcorp_cell_position_small;
    if (!isset($bcorp_cell_position[$tag])) $this->bcorp_cell_classes_reset($tag);
    if ($bcorp_cell_position[$tag]==0) $bcorp_no_gutter=' bcorp-first-cell bcorp-no-gutter'; else $bcorp_no_gutter = ' bcorp-gutter';
    if (($bcorp_cell_position_small[$tag]==0) || ($gutter_counter[$mobilewidth]==1)) $bcorp_no_gutter_small=' bcorp-no-gutter-small'; else $bcorp_no_gutter_small = '';
    $bcorp_cell_position[$tag] += $gutter_counter[$width];
    if ($bcorp_cell_position[$tag]>=1) $bcorp_cell_position[$tag]=0;
    $bcorp_cell_position_small[$tag] += $gutter_counter[$mobilewidth];
    if ($bcorp_cell_position_small[$tag]>=1) $bcorp_cell_position_small[$tag]=0;
    return 'bcorp-cell bcorp-'.$width.$bcorp_no_gutter.$bcorp_no_gutter_small.' bcorp-mobile-'.$mobilewidth.' bcorp-no-gutter-mobile';
  }

  function bcorp_cell_shortcode($atts,$content=null,$tag) {
    /* [bcorp_cell]
     * width
     * mobilewidth
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    return '<div class="'.$this->bcorp_cell_classes($tag,$data['width'],$data['mobilewidth']).'">'.do_shortcode($content).'</div>';
  }

  function bcorp_gallery_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_gallery]
     * ids
     * type (preview,nopreview)
     * -->preview
     *      previewsize (image sizes)
     * caption (true,false)
     * columns (1-12)
     * thumbsize (images sizes)
     * link (lightbox,same,new,none)
     * largesize
     * animation (none)
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    static $bcorp_gallery_instance = 0;
    $bcorp_gallery_instance++;
    $gallery_selector = "bcorp-gallery-{$bcorp_gallery_instance}";
    $preview_selector = "bcorp-gallery-preview-{$bcorp_gallery_instance}";
    $ids_list = explode(",", $data['ids']);
    $output='';
    $image_selector = 0;
    foreach ($ids_list as $id) {
      if (get_post_status($id)) {
        $bcorp_gallery_caption = get_post($id)->post_excerpt;
        $bcorp_gallery_thumbnail = wp_get_attachment_image($id,$data['thumbsize']);
        $image_url = wp_get_attachment_image_src($id,$data['largesize'])[0];

        $preview_url = wp_get_attachment_image_src($id,$data['previewsize'])[0];
        $bcorp_gallery_preview_image = wp_get_attachment_image($id,$data['previewsize']);
        if (!$output)
          {
          if ($data['preview']=='true') {
            $output.='<div class="bcorp-gallery"><div class="bcorp-gallery-preview" id="'.$preview_selector.'" data-rel="prettyPhoto['.$gallery_selector.']"><div class="bcorp-image-preview" data-image-count="0">'.$bcorp_gallery_preview_image;
            if ($data['caption'] == 'true') $output.='<br /><div class="bcorp-gallery-preview-caption">'.$bcorp_gallery_caption.'</div>';
            $output.='</div></div>';
          }
          else $output.='<div class="bcorp-gallery-no-preview">';
          $output .= '<div class="bcorp-gallery-thumbs">';
          }
        $output.= '<div class="bcorp-thumb bcorp-thumb-'.$data['columns'].'"><a href="'.$image_url.'" data-preview-area="'.$preview_selector.'" data-title="'.$data['caption'].'" data-rel="prettyPhoto['.$gallery_selector.']" data-preview-url="'.$preview_url.'" title="'.$bcorp_gallery_caption.'" data-image-count="'.$image_selector.'">'.$bcorp_gallery_thumbnail.'</a></div>';
        $image_selector++;
      }
    }
    if ($output) return $output.'</div></div><!--[/bcorp_gallery]-->';
  }

  function bcorp_heading_shortcode($atts,$content=null,$tag) {
    /* [bcorp_heading]
     * text
     * heading^
     * bold (true,false)
     * italic (true,false)
     * fontcolor (heading,font,altfont,link,custom)
     * -->custom
     *      color
     * align (left,center,right)
     * animation
     * separator (none,inline-single,inline-double,underline)
     * -->inline-single,inline-double,underline
     *      linestyle
     *      linecolor
     * -->inline-single
     *       linethickness
     * margins^
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    if (!$data['text']) return '';
    if ($data['heading'] == 'default') $data['heading'] = 'h1';
    if ($data['heading'] == 'custom') {$data['heading']='h1'; $customfontsize=' font-size:'.$data['headingsize'].';'; } else {
      $customfontsize='';
    }
    $textcolor = '';
    if ($data['fontcolor'] == 'custom') {
      $fontcolor = '';
      if ($data['color']) $textcolor = ' color:'.$data['color'].';';
    } elseif ($data['fontcolor'] == 'altfont') $fontcolor = 'bcorp-color-font-alt';
      elseif ($data['fontcolor'] == 'font') $fontcolor = 'bcorp-color-font';
      elseif ($data['fontcolor'] == 'link') $fontcolor = 'bcorp-color-link';
    else $fontcolor ='bcorp-color-heading';
    if ($data['italic'] == 'true') $italic = ' bcorp-heading-italic'; else $italic = '';
    if ($data['bold'] != 'true') $bold = ' font-weight:normal;'; else $bold = '';
    if ($data['linecolor']) $data['linecolor'] = 'border-color:'.$data['linecolor'].';';
    if ($data['margintop']) $data['margintop'] = 'margin-top:'.$data['margintop'].';';
    if ($data['marginbottom']) $data['marginbottom'] = 'margin-bottom:'.$data['marginbottom'].';';

    $sepclass = '';
    if ($data['separator'] == 'underline') $underline = ' bcorp-heading-title-underline'; else $underline = '';
    if ($data['linethickness'] == 'thick') $thickness = ' bcorp-heading-separator-inline-thick'; else $thickness = '';
    if ($data['separator'] == 'inline-single' || $data['separator'] == 'inline-double' ) $sepclass = ' bcorp-heading-separator-'.$data['separator'];
    return '<div class="bcorp-heading bcorp-heading-align-'.$data['align'].$italic.' bcorp-animated" data-animation="'.$data['animation'].'" style="'.$textcolor.';"><'.$data['heading'].' class="bcorp-heading-title '.$underline.$thickness.$sepclass.' '.$fontcolor.'" style="'.$bold.$textcolor.$customfontsize.$data['margintop'].$data['marginbottom'].$data['linecolor'].'border-top-style:'.$data['linestyle'].';border-bottom-style:'.$data['linestyle'].';">'.$data['text'].'</'.$data['heading'].'></div>';
  }

  function bcorp_divider_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_divider]
     * line (default,spaced,blank,small,custom)
     * -->small
     *      align (left,right,center)
     * -->blank
     *      height
     * -->custom
     *      align (left,right,center)
     *      thickness (none,thin,thick)
     *      -->thick&thin
     *           linewidth
     *           linecolor
     *    margintop
     *    marginbottom
     *    showicon (true,false)
     *    -->true
     *         iconcolor
     *         icon
     * hide (never,mobile,desktop)
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    switch ($data['type']) {
      case "blank":
        return '<div class="bcorp_divider bcorp-cell bcorp-1-1 bcorp-hide-'.$data['hide'].'" style="height:'.$data['height'].';"></div>';
      case "small":
        switch ($data['align']) {
          case "center":
            return '<div class="bcorp_divider bcorp-cell bcorp-1-1" style="padding-top:'.$data['margintop'].'; padding-bottom:'.$data['marginbottom'].'; border-color:inherit;">
                      <div style="width:33%; margin:0 auto; border-color:inherit;">
                        <div style="overflow:hidden;border-color:inherit;">
                          <div style="margin-left:-9px; width:50%; padding:2px; border-bottom: 1px solid; border-color:inherit; float:left;"></div>
                          <div style="width:5px; height:5px; border: 2px solid; border-color:inherit; border-radius: 5px; float:left;"></div>
                          <div style="margin-right:-9px; width:50%; padding:2px; border-bottom: 1px solid; border-color:inherit; float:left;"></div>
                        </div>
                      </div>
                    </div>';
          default:
            return '<div class="bcorp_divider bcorp-cell bcorp-1-1" style="padding-top:'.$data['margintop'].'; padding-bottom:'.$data['marginbottom'].'; border-color:inherit;">
                      <div style="width:20%; min-width:100px; float:'.$data['align'].'; border-color:inherit;">
                        <div style="width:5px; height:5px; border: 2px solid; border-color:inherit; border-radius: 5px; float:'.$data['align'].';"></div>
                        <div style="border-bottom: 1px solid; border-color:inherit; padding:2px; width:80%; float:'.$data['align'].';"></div>
                      </div>
                    </div>';
        }
      case "custom":
        if ($data['thickness'] == 'thin') $linethickness = 1; else if ($data['thickness'] == 'thick') $linethickness=2; else $linethickness=0;
        if (!$data['linecolor']) $data['linecolor']='inherit';
        if (!$data['linewidth']) $data['linewidth']='33%';
        if (($data['icon']) &&($data['showicon'] == 'true')) $icon = '<span aria-hidden="true" data-icon="&#x'.$data['icon'].';" style="font-size:20px; line-height:20px; color:'.$data['iconcolor'].';"></span>'; else $icon="";
        if ($data['showicon'] == 'true') $iconhtml = '<div style="width:28px; height:22px; float:left; text-align:center;">'.$icon.'</div>'; else $iconhtml='';
        return '<div class="bcorp_divider bcorp-cell bcorp-1-1" style="padding-top:'.$data['margintop'].'; padding-bottom:'.$data['marginbottom'].'; border-color:inherit;">
                  <div style="width:'.$data['linewidth'].'; float:'.$data['align'].'; margin:0 auto; border-color:inherit;">
                    <div style="overflow:hidden;border-color:inherit;">
                      <div style="margin-left:-24px; width:50%; padding:5px; border-bottom:'.$linethickness.'px solid; border-color:'.$data['linecolor'].'; float:left;"></div>
                      '.$iconhtml.'
                      <div style="margin-right:-24px; width:50%; padding:5px; border-bottom:'.$linethickness.'px solid; border-color:'.$data['linecolor'].'; float:left;"></div>
                    </div>
                  </div>
                </div>';
      default:
        return '<div class="bcorp_divider bcorp-cell bcorp-1-1" style="padding-top:'.$data['margintop'].'; padding-bottom:'.$data['marginbottom'].'; border-color:inherit;"><div style="border-top-width:1px; border-top-style: solid; border-top-color:inherit;"></div></div>';
      }
  }

  function bcorp_icon_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_icon]
     * icon
     * size
     * iconcolor
     * border (true,false)
     * ->bordersize
     * ->title
     * ->heading^
     * ->headingcolor
     * ->headingmargintop
     * ->bordercoler
     * ->backgroundcolor
     * ->borderthickness
     * align (left,center,right)
     * link^
     * textblock (tooltip)
     * margins^
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    if (!$data['icon']) return;
    $circle_size = ($data['bordersize']/100). 'em';
    if ($data['heading'] == 'default') $data['heading']='h3';
    if ($data['heading'] == 'custom') {$data['heading']='h1'; $data['headingsize']=' font-size:'.$data['headingsize'].';'; } else $data['headingsize']='';
    $href=$this->bcorp_link($data['link'],$data['linkurl'],$data['linksection'],$data['linkpost'],$data['linkpage'],$data['linkportfolio'],$data['linkcategory'],$data['linktag'],$data['linkportfoliocategory'],$data['linkformat'],$data['linktarget']);
    if ($data['border']=="true") {
      if ($data['bordercolor']) $data['bordercolor'] = 'border-color:'.$data['bordercolor'].';';
      if ($data['backgroundcolor']) $data['backgroundcolor'] = 'background-color:'.$data['backgroundcolor'].';';
      if ($data['headingcolor']) $data['headingcolor'] = 'color:'.$data['headingcolor'].';';
      $border = 'width:'.($circle_size).'; height:'.($circle_size).';  line-height:'.($circle_size).'; '.$data['backgroundcolor'].$data['bordercolor'].' border-width:'.$data['borderthickness'].';';
    } else $border = '';
    if ($data['iconcolor']) $data['iconcolor'] = 'color:'.$data['iconcolor'].';';
    if ($data['margintop']) $data['margintop'] = 'margin-top:'.$data['margintop'].';';
    if ($data['marginright']) $data['marginright'] = 'margin-right:'.$data['marginright'].';';
    if ($data['marginbottom']) $data['marginbottom'] = 'margin-bottom:'.$data['marginbottom'].';';
    if ($data['marginleft']) $data['marginleft'] = 'margin-left:'.$data['marginleft'].';';
    if ($data['title']) $icon_title = '<'.$data['heading'].' class="bcorp-icon-title" style="'.$data['headingcolor'].' margin-top:'.$data['headingmargintop'].'; '.$data['headingsize'].'  color:'.$data['iconcolor'].';">'.$data['title'].'</'.$data['heading'].'>'; else $icon_title = '';
    if ($data['animation'] !='none') { $animation = ' bcorp-animated'; $data['animation'] = ' data-animation="'.$data['animation'].'" '; } else {
      $animation = ''; $data['animation'] = '';
    }
    return '<div class="bcorp-icon bcorp-align-'.$data['align'].$animation.'"'.$data['animation'].' title="'.$data['tooltip'].'" style="'
            .$data['margintop'].$data['marginright'].$data['marginbottom'].$data['marginleft'].'">'.$href['start']
            .'<span class="bcorp-icon-border-'.$data['border'].'" aria-hidden="true" data-icon="&#x'.$data['icon'].';" style="font-size:'
            .$data['size'].'; '.$border.$data['iconcolor'].'"></span>'.$icon_title.$href['end'].'</div>';
  }

  function bcorp_icon_box_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_icon_box]
     * title
     * heading^
     * headingcolor (heading,font,altfont,link,custom)
     * ->custom
     *     headingcustomcolor
     * textcolor (heading,font,altfont,link,custom)
     * ->custom
     *     textcustomcolor
     * showicon (true,false)
     * ->true
     *     icon
     * align (left, center)
     * textblock
     * background (none,alt,custom)
     * ->custom
     *     backgroundcolor
     */
   $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
   if ($data['heading'] == 'default') $data['heading'] = 'h1';
   if ($data['heading'] == 'custom') {$data['heading']='h1'; $customfontsize=' font-size:'.$data['headingsize'].';'; } else {
     $customfontsize='';
   }
   if ($data['background']=='alt') $alt = ' bcorp-alt-background'; else $alt='';
   if ($data['background']=='custom') $bgcolor = ' background-color:'.$data['backgroundcolor'].';'; else $bgcolor ='';
   if ($data['padding'] !='') $padding = ' padding:'.$data['padding'].';'; else $padding ='';
   if ($data['showicon'] == 'true' && $data['icon']) {
     if ($data['iconcolor']) $iconcolor = ' style="color:'.$data['iconcolor'].';"'; else $iconcolor = '';
     $icon = '<span class="bcorp-icon-box-icon" aria-hidden="true" data-icon="&#x'.$data['icon'].';"'.$iconcolor.'></span>';
   } else $icon = '';
   if ($data['align']=='center') $align = ' style="text-align:center;"'; else $align ='';
   $headingcustomcolor = '';
   if ($data['headingcolor'] == 'custom') {
     $headingcolor = '';
     if ($data['headingcustomcolor']) $headingcustomcolor = ' style="color:'.$data['headingcustomcolor'].';"';
   } elseif ($data['headingcolor'] == 'altfont') $headingcolor = ' bcorp-color-font-alt';
   elseif ($data['headingcolor'] == 'font') $headingcolor = ' bcorp-color-font';
   elseif ($data['headingcolor'] == 'link') $headingcolor = ' bcorp-color-link';
   else $headingcolor =' bcorp-color-heading';

   $textcustomcolor = '';
   if ($data['textcolor'] == 'custom') {
     $textcolor = '';
     if ($data['textcustomcolor']) $textcustomcolor = ' style="color:'.$data['textcustomcolor'].';"';
   } elseif ($data['textcolor'] == 'altfont') $textcolor = ' bcorp-color-font-alt';
   elseif ($data['textcolor'] == 'font') $textcolor = ' bcorp-color-font';
   elseif ($data['textcolor'] == 'link') $textcolor = ' bcorp-color-link';
   else $textcolor =' bcorp-color-heading';

   return '<div class="bcorp-icon-box'.$alt.'" style="'.$bgcolor.$padding.'">
              <div class="bcorp-icon-box-title'.$headingcolor.'"'.$headingcustomcolor.'><'.$data['heading'].' class="bcorp-heading bcorp-heading"'.$align.' style="'.$customfontsize.'">'.$icon.$data['title'].'</'.$data['heading'].'></div>
              <div class="bcorp-icon-box-content'.$textcolor.'"'.$textcustomcolor.'>'.do_shortcode($content).'</div>
           </div>';
  }

  function bcorp_icon_list_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_icon_list]
     * align (left,right)
     * iconsize
     * headingsize
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    global $bcorp_icon_list_align, $bcorp_icon_list_icon_size,$bcorp_icon_list_heading_size,$bcorp_icon_list_circle_size,$bcorp_icon_list_margin;
    $bcorp_icon_list_align = $data['align'];
    $bcorp_icon_list_icon_size = $data['iconsize'];
    $bcorp_icon_list_heading_size = $data['headingsize'];
    $bcorp_icon_list_circle_size= intval(5/3*$bcorp_icon_list_icon_size).preg_replace('/[0-9]+/', '', $bcorp_icon_list_icon_size);
    $bcorp_icon_list_margin = intval($bcorp_icon_list_circle_size/2).preg_replace('/[0-9]+/', '', $bcorp_icon_list_circle_size);
    $output = '<div class="' . $tag . '">'.do_shortcode($content).'</div>';
    return $output;
  }

  function bcorp_icon_list_icon_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_icon_list_icon]
     * title
     * icon
     * link^
     * textblock
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    global $bcorp_icon_list_align, $bcorp_icon_list_icon_size,$bcorp_icon_list_heading_size,$bcorp_icon_list_circle_size,$bcorp_icon_list_margin;
    if ($bcorp_icon_list_align == 'left') $margin='right'; else $margin='left';
    if ($data['icon']) $icon = 'data-icon="&#x'.$data['icon'].';"'; else $icon = '';
    $href=$this->bcorp_link($data['link'],$data['linkurl'],$data['linksection'],$data['linkpost'],$data['linkpage'],$data['linkportfolio'],$data['linkcategory'],$data['linktag'],$data['linkportfoliocategory'],$data['linkformat'],$data['linktarget']);
    $bcorp_icon = $href['start'].'<div class="bcorp-border-background bcorp-icon-list-icon bcorp-icon-list-'.$bcorp_icon_list_align.'" aria-hidden="true" '.$icon.'
                  style="font-size:'.$bcorp_icon_list_icon_size.'; height:'.$bcorp_icon_list_circle_size.'; width:'.$bcorp_icon_list_circle_size.'; line-height:'.$bcorp_icon_list_circle_size.';
                  float:'.$bcorp_icon_list_align.'; margin-'.$margin.':'.$bcorp_icon_list_margin.';"></div>'.$href['end'];
    return '<div class="bcorp-icon-list">'.$bcorp_icon.'<div class="bcorp-icon-list-title" style="text-align:'.$bcorp_icon_list_align.';"><'.$bcorp_icon_list_heading_size.'>'.$data['title'].'</'.$bcorp_icon_list_heading_size.'>'.do_shortcode(rawurldecode($content)).'</div></div>';
  }

  function bcorp_image_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_image]
     * id
     * size
     * align
     * link^
     * animation
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    $href=$this->bcorp_link($data['link'],$data['linkurl'],$data['linksection'],$data['linkpost'],$data['linkpage'],$data['linkportfolio'],$data['linkcategory'],$data['linktag'],$data['linkportfoliocategory'],$data['linkformat'],$data['linktarget']);
    $image = wp_get_attachment_image_src($data['id'],$data['size']);
    return '<div class="bcorp-image bcorp-image-align-'.$data['align'].' bcorp-animated" data-animation="'.$data['animation'].'">'.$href['start'].'<img src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'">'.$href['end'].'</div>';
  }

  function bcorp_link($link,$linkurl,$linksection,$linkpost,$linkpage,$linkportfolio,$linkcategory,$linktag,$linkportfoliocategory,$linkformat,$linktarget) {
    /* [bcorp_link]
     * link (none,manual,section,post,page,portfolio,category,tag,portfoliocategory,format)
     * ->manual
     *     url
     * ->section
     *     linksection
     * ->post
     *     linkpost
     * ->page
     *     linkpage
     * ->portfolio
     *     linkportfolio
     * ->category
     *     linkcategory
     * ->tag
     *     linktag
     * ->portfoliocatgory
     *     linkportfoliocategory
     * ->format
     *     linkformat
     * target (true,false)
     */
    switch ($link) {
      case "none":
        $linkurl='';
      break;
      case "manual":
      break;
      case "section":
        $linkurl="#bcsc-section-".$linksection;
        break;
      case "post":
        $linkurl=get_permalink($linkpost);
        break;
      case "page":
        $linkurl=get_permalink($linkpage);
        break;
      case "portfolio":
        $linkurl=get_permalink($linkportfolio);
        break;
      case "category":
        $linkurl=get_category_link($linkcategory);
        break;
      case "tag":
        $linkurl=get_tag_link($linktag);
        break;
      case "portfoliocategory":
        $linkurl= get_term_link($linkportfoliocategory, 'portfolio-category');
        break;
      case "format":
        $linkurl=get_post_format_link($linkformat);
     }
    if ($linkurl) {
      if ($linktarget=='true') $href['target']=' target="_blank"'; else $href['target']='';
      $href['link']=esc_url($linkurl);
      $href['start']='<a href="'.$href['link'].'"'.$href['target'].'>';
      $href['end']='</a>';
    } else {
      $href['start']='';
      $href['end']='';
      $href['link']='';
      $href['target']='';
    }
    return $href;
  }

  function bcorp_row_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_row]
     * colors (header,main,alt,footer,base)
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    $this->bcorp_cell_classes_reset('bcorp-cell');  // Reset Gutter at Start of Row
    $bcsc_do_content = do_shortcode($content);
    $this->bcorp_cell_classes_reset('bcorp-cell'); // Reset Gutter at End of Row
    return '<div class="bcorp-cell bcorp-1-1 bcorp-color-'.$data['colors'].'">'.$bcsc_do_content.'</div>';
  }

  function bcorp_section_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_section]
     * background
     * size
     * location (none,youtube,vimeo)
     * ->youtube,vimeo
     *     video
     * colors (header,main,alt,footer,base)
     * height (none,25,50,75,100,custom)
     * ->pixels
     * effect (normal,parallax,fixed)
     * topborder (none,shadow,darkshadow,plain)
     * class
     * idname
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    global $bcorp_section_id;
    $this->bcorp_cell_classes_reset('bcorp_cell');  // Reset Gutter at Start of Section
    $bcorp_section_id++;
    if ( wp_is_mobile() ) { $data['effect']='normal'; $data['type'] = 'cover'; }
    $bcorp_do_content = do_shortcode($content);
    $this->bcorp_cell_classes_reset('bcorp_cell');  // Reset Gutter at End of Section
    $cell_style = 'style="';
    if ($data['background'] && (!$data['video'] || wp_is_mobile())) {
      if (wp_is_mobile()) $cell_style .= ' background-size: cover;';
      if ($data['effect']=='fixed') $cell_style .= ' background-attachment:fixed;';
      if ($data['effect']=='parallax') $cell_style .= ' background-size:0;';
      $bcorp_background_info = ' data-background-width="'.wp_get_attachment_image_src( $data['background'],"full")[1].'"'.
                              ' data-background-height="'.wp_get_attachment_image_src( $data['background'],"full")[2].'"';
    } else $bcorp_background_info = '';
    if ($data['speed']) {
      $bcorp_background_info .= ' data-parallax-speed="'.$data['speed'].'"';
    }
    if (($data['height'] == '25') || ($data['height'] == '50') || ($data['height'] == '75') || ($data['height'] == '100')) $cell_style .= ' min-height:'.$data['height'].'vh;';
    else if ($data['height'] == 'custom') $cell_style .= ' min-height:'.$data['pixels'].'px;';
    if ($data['topborder']== 'shadow') $cell_style .= ' box-shadow:inset 0 1px 3px rgba(0,0,0,0.1); padding-top:1px;';
    if ($data['topborder']== 'darkshadow') $cell_style .= ' box-shadow:inset 0 1px 3px rgba(0,0,0,0.4); padding-top:1px;';
    if ($data['topborder']== 'plain') $cell_style .= ' border-top-width:1px; border-top-style:solid;';
    $cell_style .= '"';
    if ($data['video'] && !wp_is_mobile()) {
      global $bcorp_background_video_id;
      $bcorp_background_video_id++;
      if ($data['location'] == 'vimeo') $videourl = 'http://player.vimeo.com/video/'.$data['video'].'?title=0&amp;byline=0&amp;portrait=0&amp;autoplay=1&amp;loop=1&amp;autopause=0&amp;player_id=bcorp-'.$data['location'].'-'.$bcorp_background_video_id;
      else if ($data['location'] == 'youtube') { $videourl = 'http://www.youtube.com/embed/'.$data['video'].'?&amp;rel=0&amp;controls=0&amp;showinfo=0&amp;enablejsapi=1&amp;origin='.get_home_url(); }
      $videoratio = 56.25;
      $background_video = '<div class="bcorp-video-slide-wrap"><iframe class="bcorp-background-video bcorp-'.$data['location'].'-autoplay-onload bcorp-video-mute bcorp-video-loop bcorp-video-stretch bcorp-video-'.$data['location'].' bcorp-video-'.$data['location'].'-background" id="bcorp-'.$data['location'].'-'.$bcorp_background_video_id.'"  data-starttime="'.$data['starttime'].'" src="'.$videourl.'" allowfullscreen data-video-ratio="'.$videoratio.'"></iframe></div>';
    } else $background_video = '';

    if (!$data['idname']) $idname = 'bcorp-section-'.$bcorp_section_id; else $idname = $data['idname'];
    if ($data['background'] && !$data['video']) $newbackground = '<div class="bcorp-background-image">'.wp_get_attachment_image($data['background'],$data['size']).'</div>'; else $newbackground ='';
    return '</div></div></section>
      <section id="'.$idname.'" class="bcorp-cell bcorp-section'.$data['class'].' bcorp-scroll-'.$data['effect'].' content-area bcorp-color-'.$data['colors'].'" '.$cell_style.$bcorp_background_info.'>
        '.$newbackground.$background_video.'
        <div class="site-content">'.$bcorp_do_content.'</div>
      </section>
      <section class="content-area bcorp-color-main"><div class="site-content"><div class="bcorp-row">';
  }

  function bcorp_tabs_shortcode($atts,$content=null,$tag ) {
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    $regex = '/ title\s*=\s*"(.*?)"/';
    preg_match_all($regex, $content, $titles);
    $regex = '/ showicon\s*=\s*"(.*?)"/';
    preg_match_all($regex, $content, $showicons);
    $regex = '/ icon\s*=\s*"(.*?)"/';
    preg_match_all($regex, $content, $icons);
    static $bcorp_id;
    $vertical ='';
    $width='';
    $style = '';
    if ($data['position'] == 'left') {
      $vertical = " bcorp-vertical-tab";
      $style = ' style="padding-left:'.$data['width'].';"';
      $width = ' style="width:'.(absint($data['width'])+1).'px;"';
    } else if ($data['position'] == 'right') {
      $vertical = " bcorp-vertical-tab bcorp-vertical-tab-right";
      $style = ' style="padding-right:'.$data['width'].';"';
      $width = ' style="width:'.(absint($data['width'])+1).'px;"';
    }
    if ($data['position'] == 'top') $vertical = " bcorp-tab-top";

    $output = '<div class="bcorp-tabs'.$vertical.'"'.$style.'><ul'.$width.'>';
    $icon_count=0;
    for ($i = 0; $i < count($titles[1]); $i++) {
      $bcorp_id++;
      if ($showicons[1][$i]=='true') {
        if ($icons[1][$icon_count]) $bcorp_icon= '<span class="bcorp-tab-icon" aria-hidden="true" data-icon="&#x'.esc_attr($icons[1][$icon_count]).';"></span>'; else $bcorp_icon="";
          $icon_count++;
      } else $bcorp_icon="";
      $output .= '<li><a href="#bcorp-tab-'.$bcorp_id.'">'.$bcorp_icon.esc_html($titles[1][$i]).'</a></li>';
    }
    $output .= '</ul>'.do_shortcode($content).'</div>';
    return $output;
  }

  function bcorp_tab_panel_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_tab_panel]
     * title
     * showicon (true,false)
     * icon
     * textblock
     */
    static $bcorp_id;
    $bcorp_id++;
   return '<div class="bcorp-tab-panel bcorp-alt-background" id="bcorp-tab-'.$bcorp_id.'"><div class="bcorp-tab-panel-inner">'.do_shortcode($content).'</div></div>';
  }


  function bcorp_text_shortcode($atts,$content=null,$tag ) {
    /* [bcorp_text]
     *  animation
     *
     */
    $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
    return '<div class="bcorp-text bcorp-cell bcorp-1-1 bcorp-animated" data-animation="'.$data['animation'].'">'.do_shortcode($content).'</div>';
  }

  function bcorp_wp_widget($atts,$content=null,$tag,$widget) {
      ob_start();
      $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
      if ($data['idname']) $id = 'id="'.$data['idname'].'" '; else $id = '';
      $titlestyle = '';
      if ($data['heading'] == 'default') $data['heading'] = 'h5';
      if ($data['heading'] == 'custom') {
        $data['heading']='h1';
        $titlestyle = ' style="font-size:'.$data['headingsize'].';"';
      }
      if (!$data['title']) $titlestyle = ' style="display:none;"';
      $args = array(
        'before_widget' => '<div '.$id.'class="bcorp-wp-widget bcorp-cell '.$data['class'].'">',
        'before_title' => '<'.$data['heading'].' class="widgettitle"'.$titlestyle.'>',
        'after_title' => '</'.$data['heading'].'>',
      );
      if (isset($data['count'])) if ($data['count']=='true') $data['count']=true; else $data['count']=false;
      if (isset($data['dropdown'])) if ($data['dropdown']=='true') $data['dropdown']=true; else $data['dropdown']=false;
      if (isset($data['show_summary'])) if ($data['show_summary']=='true') $data['show_summary']=true; else $data['show_summary']=false;
      if (isset($data['show_author'])) if ($data['show_author']=='true') $data['show_author']=true; else $data['show_author']=false;
      if (isset($data['show_date'])) if ($data['show_date']=='true') $data['show_date']=true; else $data['show_date']=false;
      if (isset($data['hierarchical'])) if ($data['hierarchical']=='true') $data['hierarchical']=true; else $data['hierarchical']=false;
      the_widget($widget,$data,$args );
      return ob_get_clean();
    }

    function bcorp_wp_archives_shortcode($atts,$content=null,$tag) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_Archives');
    }

    function bcorp_wp_calendar_shortcode($atts,$content=null,$tag) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_Calendar');
    }

    function bcorp_wp_categories_shortcode($atts,$content=null,$tag) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_Categories');
    }

    function bcorp_wp_meta_shortcode($atts,$content=null,$tag) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_Meta');
    }

    function bcorp_wp_pages_shortcode($atts,$content=null,$tag) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_Pages');
    }

    function bcorp_wp_recent_comments_shortcode($atts,$content=null,$tag) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_Recent_Comments');
    }

    function bcorp_wp_recent_posts_shortcode($atts,$content=null,$tag) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_Recent_Posts');
    }

    function bcorp_wp_rss_shortcode($atts,$content=null,$tag) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_RSS');
    }

    function bcorp_wp_search_shortcode($atts,$content=null,$tag ) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_Search');
    }

    function bcorp_wp_tag_cloud_shortcode($atts,$content=null,$tag) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_Tag_Cloud');
    }

    function bcorp_wp_text_shortcode($atts,$content=null,$tag) {
      return $this->bcorp_wp_widget($atts,$content,$tag,'WP_Widget_Text');
    }

    function bcorp_video_shortcode($atts,$content=null,$tag ) {
      /* [bcorp_video]
       * location (local,youtube,vimeo)
       * ->(youtube,vimeo)
       *     video
       * ->local
       *     videourl
       * ratio (standard,wide,custom)
       * ->custom
       *     width
       *     height
       * size (standard,fullwidth,fullscreen)
       */
      $data=$GLOBALS['bcorp_shortcodes_data']->bcorp_sanitize_data($tag,$atts);
      if ($data['location'] == 'vimeo') $videourl = 'http://player.vimeo.com/video/'.$data['video'].'?title=0&byline=0&portrait=0';
      else if ($data['location'] == 'youtube') $videourl = 'http://www.youtube.com/embed/'.$data['video'].'?rel=0&controls=1&showinfo=0&origin='.get_home_url();
      else $videourl=$data['videourl'];
      if ($data['ratio'] == 'wide') $videoratio = 56.25;
      else if (($data['ratio'] == 'custom') && ($data['width'] != 0) && ($data['height'] != 0))  $videoratio = $data['height'] / $data['width'] * 100;
      else $videoratio = 75;
      $video = '<div class="bcorp-cell bcorp-video bcorp-video-'.$data['location'].'" data-video-ratio="'.$videoratio.'" style="padding-bottom: '.$videoratio.'%;"><iframe id="bcorp-'.$data['location'].'" src="'.$videourl.'" allowfullscreen></iframe></div>';
      if ($data['location'] == 'local') return '<div class="bcorp-cell bcorp-video bcorp-video-'.$data['location'].'">'.do_shortcode('[video src="'.$videourl.'"]').'</div>';
      if ($data['size'] == 'fullwidth')
        return $this->fullwidth_start($data['size']).'<div class="bcorp-cell bcorp-video"><iframe class="bcorp-fullwidth-video bcorp-video-'.$data['location'].'" id="bcorp-'.$data['location'].'" src="'.$videourl.'" allowfullscreen data-video-ratio="'.$videoratio.'"></iframe></div>'.$this->fullwidth_end();
      else if ($data['size'] =='fullscreen')
        return $this->fullwidth_start($data['size']).'<div class="bcorp-cell bcorp-video"><iframe class="bcorp-video-stretch bcorp-video-'.$data['location'].'" id="bcorp-'.$data['location'].'" src="'.$videourl.'" allowfullscreen data-video-ratio="'.$videoratio.'"></iframe></div>'.$this->fullwidth_end();
       else return $video;
  }
}
?>
