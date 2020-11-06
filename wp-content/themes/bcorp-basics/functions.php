<?php
/**
 * Functions
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 */

if (!isset($content_width)) { $content_width = 1310; }

add_action( 'after_setup_theme', 'bcorp_theme_setup' );
if (!function_exists('bcorp_theme_setup')):
	function bcorp_theme_setup() {
		register_nav_menus( array('floating_nav' => esc_html__('Floating Menu','bcorp-basics')));
		add_theme_support('post-formats',array('aside','gallery','link','image','quote','video','audio','status'));
		add_theme_support('html5',array('search-form','comment-form','comment-list','gallery','caption'));
		add_theme_support('title-tag');
		add_theme_support('post-thumbnails');
		add_theme_support('automatic-feed-links');
		global $bcorp_full_width_theme;
		$bcorp_full_width_theme = true;
		set_post_thumbnail_size( 840, 560, true );
		add_image_size('300 x 200 cropped', 300, 200, true);
		add_image_size('350 x 300 cropped', 350, 300, true);
		add_image_size('375 x 250 cropped', 375, 250, true);
		add_image_size('400 x 350 cropped', 400, 350, true);
		add_image_size('600 x 300',600,300,false);
		add_image_size('600 x 300 cropped',600,300,true);
	  add_image_size('600 x 400 cropped', 600, 400, true);
		add_image_size('600 x 600',600,600,false);
		add_image_size('675 x 600 cropped', 675, 600, true);
		add_image_size('768 x 512 cropped', 768, 512, true);
		add_image_size('900 x 300 cropped', 900, 300, true);
		add_image_size('900 x 600 cropped', 900, 600, true);
		add_image_size('1280 x 720 cropped', 1280, 720, true);
		add_image_size('1310 x 450 cropped', 1310, 450, true);
		add_image_size('1310 x 655 cropped', 1310, 655, true);
		add_image_size('1920 x 1080 cropped', 1920, 1080, true);
	}
endif;

add_filter( 'image_size_names_choose', 'bcorp_custom_sizes' );
if (!function_exists('bcorp_custom_sizes')):
	function bcorp_custom_sizes($sizes) {
	  return array_merge($sizes,array(
			'300 x 200 cropped' => esc_html__('300 x 200 cropped','bcorp-basics'),
			'350 x 300 cropped' => esc_html__('350 x 300 cropped','bcorp-basics'),
			'375 x 250 cropped' => esc_html__('375 x 250 cropped','bcorp-basics'),
			'400 x 350 cropped' => esc_html__('400 x 350 cropped','bcorp-basics'),
			'600 x 300' => esc_html__('600 x 300','bcorp-basics'),
			'600 x 300 cropped' => esc_html__('600 x 300 cropped','bcorp-basics'),
			'600 x 400 cropped' => esc_html__('600 x 400 cropped','bcorp-basics'),
			'600 x 600' => esc_html__('600 x 600','bcorp-basics'),
			'675 x 600 cropped' => esc_html__('675 x 600 cropped','bcorp-basics'),
			'768 x 512 cropped' => esc_html__('768 x 512 cropped','bcorp-basics'),
	    '900 x 300 cropped' => esc_html__('900 x 300 cropped','bcorp-basics'),
			'900 x 600 cropped' => esc_html__('900 x 600 cropped','bcorp-basics'),
			'1310 x 450 cropped' => esc_html__('1310 x 450 cropped','bcorp-basics'),
	  ));
	}
endif;

add_filter('the_content', 'bcorp_shortcode_empty_paragraph_fix');
if (!function_exists('bcorp_shortcode_empty_paragraph_fix')):
	function bcorp_shortcode_empty_paragraph_fix($content) {
	  $array = array (
	      '<p>[' => '[',
	      ']</p>' => ']',
	      ']<br />' => ']'
	  );
	  return strtr($content, $array);
	}
endif;

add_action( 'wp_head', 'bcorp_js_class', 1 );
if (!function_exists('bcorp_js_class')):
	function bcorp_js_class () {
		echo '<script>document.documentElement.className = document.documentElement.className.replace("no-js","js");</script>'. "\n";
	}
endif;

add_filter('get_search_form', 'bcorp_search_form');
if (!function_exists('bcorp_search_form')):
	function bcorp_search_form($text) {
		$text='<form role="search" method="get" class="search-form" action="'.esc_url(home_url()).'"><input type="submit" class="search-submit" value="&#xf179;">
			<input type="search" class="search-field" placeholder="Search..." value="" name="s" title="Search for:"></form>';
	  return $text;
	}
endif;

add_action( 'widgets_init', 'bcorp_register_sidebars' );
if (!function_exists('bcorp_register_sidebars')):
	function bcorp_register_sidebars () {
		$args = array(
			'name'          => esc_html__( 'Everywhere', 'bcorp-basics' ),
			'id'            => 'everywhere',
			'description'   => '',
			'class'         => '',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h5 class="widget-title">',
			'after_title'   => '</h5>' );
		register_sidebar( $args );
		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 1', 'bcorp-basics' ),
			'id'            => 'bcorp_footer_1',
			'description'   => esc_html__( 'Appears in the footer section of the site.', 'bcorp-basics' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h5 class="widget-title">',
			'after_title'   => '</h5>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 2', 'bcorp-basics' ),
			'id'            => 'bcorp_footer_2',
			'description'   => esc_html__( 'Appears in the footer section of the site.', 'bcorp-basics' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h5 class="widget-title">',
			'after_title'   => '</h5>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Footer Column 3', 'bcorp-basics' ),
			'id'            => 'bcorp_footer_3',
			'description'   => esc_html__( 'Appears in the footer section of the site.', 'bcorp-basics' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h5 class="widget-title">',
			'after_title'   => '</h5>',
		) );
	}
endif;

add_action('wp_enqueue_scripts', 'bcorp_enqueue_scripts' );
if (!function_exists('bcorp_enqueue_scripts')):
	function bcorp_enqueue_scripts() {
		wp_enqueue_script('jquery');
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );
		wp_enqueue_style( 'bcorp-dashicons-style', get_stylesheet_uri(), array('dashicons'));
		wp_enqueue_script('sticky_js', get_template_directory_uri().'/js/jquery.sticky.js','','',true);
		wp_enqueue_script('bcorp_js',get_template_directory_uri().'/js/bcorp.js','','',true);
		wp_enqueue_style( 'wpb-google-fonts', 'http://fonts.googleapis.com/css?family=Noto+Serif:regular|Open+Sans:600|Quantico:italic', false );
	}
endif;

if (!function_exists('bcorp_sidebar_position')):
	function bcorp_sidebar_position($id) {
		global $bcorp_sidebar_position;
		if ($bcorp_sidebar_position) return $bcorp_sidebar_position;
		if (is_page()) $bcorp_sidebar_position = "bcorp_no_sidebar"; else $bcorp_sidebar_position = "bcorp-right-sidebar";
		return $bcorp_sidebar_position;
	}
endif;

if (!function_exists('bcorp_get_breadcrumbs')):
	function bcorp_get_breadcrumbs() {
    global $wp_query;
    if ( !is_front_page() ){
        echo '<ul class="breadcrumbs bcorp-color-alt">';
        echo '<li><a href="'.esc_url(get_home_url()).'">'.esc_html__('Home','bcorp-basics').'</a></li>';
        if ( is_category() )
        {
            $catTitle = single_cat_title( "", false );
            $cat = get_cat_ID($catTitle);
            echo "<li>/ ". get_category_parents($cat, TRUE, "  " ) ."</li>";
        }
				elseif ( is_author()) echo "<li>/ ".esc_html__('Posts by','bcorp-basics')." ".get_the_author()."</li>";
				elseif ( is_day() ) echo '<li>/ '. get_the_date().'</li>';
				elseif ( is_month() ) echo '<li>/ '. get_the_date( _x( 'F Y', 'monthly archives date format', 'bcorp-basics' ) ).'</li>';
				elseif ( is_year() ) echo '<li>/ '. get_the_date( _x( 'Y', 'yearly archives date format', 'bcorp-basics' ) ) .'</li>';
				elseif (is_tag()) echo  '<li>/ '.single_tag_title( '', false ).'</li>' ;
        elseif ( is_search() ) {
            echo "<li>/ ".esc_html__('Search Results','bcorp-basics')."</li>";
        }
				elseif ( wp_attachment_is_image())
				{
					echo "<li>/ ".the_title('','', FALSE)."</li>";
				}
				elseif (get_post_type() == 'portfolio') {
					$terms = get_the_terms( '', 'portfolio-category' );
					echo '<li>/ ';
					if (isset($terms[0]->slug)) echo '<a href="'.esc_url(get_term_link( $terms[0]->slug, 'portfolio-category' )).'">'.$terms[0]->name.'</a></li><li>/ ';
					echo substr(the_title('','', FALSE),0,35) ."</li>";
				}
        elseif ( is_single() )
        {
            $category = get_the_category();
            $category_id = get_cat_ID( $category[0]->cat_name );
						echo '<li>/ ';
          	if ($category_id) echo get_category_parents( $category_id, TRUE, " / " );
            echo substr(the_title('','', FALSE),0,35) ."</li>";
        }
        elseif ( is_page() )
        {
            $post = $wp_query->get_queried_object();
            if ( $post->post_parent == 0 ){
                echo "<li>/ ".the_title('','', FALSE)."</li>";
            } else {
                $title = the_title('','', FALSE);
                $ancestors = array_reverse( get_post_ancestors( $post->ID ) );
                array_push($ancestors, $post->ID);
                foreach ( $ancestors as $ancestor ){
                    if( $ancestor != end($ancestors) ){
                        echo '<li>/  <a href="'. esc_url(get_permalink($ancestor)) .'">'. strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ) .'</a></li>';
                    } else {
                        echo '<li>/  '. strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ) .'</li>';
                    }
                }
            }
        }
				elseif (is_archive() && get_post_format()) return '<li>/ '.ucfirst(get_post_format()).' '.esc_html__('Archives','bcorp-basics').'</li>';
        echo "</ul>";
    }
	}
endif;

if (!function_exists('bcorp_get_link_url')):
function bcorp_get_link_url() {
	$has_url = get_url_in_content(get_the_content());
	return $has_url ? $has_url : apply_filters( 'the_permalink', esc_url(get_permalink()) );
}
endif;

if (!function_exists('bcorp_full_width')):
	function bcorp_full_width() {
		global $bcorp_sidebar_position;
		if ($bcorp_sidebar_position === 'bcorp_no_sidebar') return '1310 x 450 cropped';
		return 'post-thumbnail';
	}
endif;

if (!function_exists('bcorp_post_thumbnail')):
	function bcorp_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		} ?>
		<div class="bcorp-blog-thumbnail-wrap"><?php
			if ( is_singular() ) {
				?>
				<div class="bcorp-blog-thumbnail"><?php
					the_post_thumbnail( bcorp_full_width() ); ?>
				</div><?php
			} else {
				?><a class="bcorp-blog-thumbnail" href="<?php echo the_permalink(); ?>">
				<?php
				the_post_thumbnail( bcorp_full_width() ); ?>
			</a><?php
			} ?>
		</div><?php
	}
endif;

add_filter( 'excerpt_more', 'bcorp_excerpt_more' );
if (!function_exists('bcorp_excerpt_more')):
	function bcorp_excerpt_more( $more ) { return ' ...'; }
endif;

if (!function_exists('bcorp_link_pages')):
	function bcorp_link_pages() {
		wp_link_pages( array(
			'before'      => '<div class="page-links">',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
		) );
	}
endif;

if (!function_exists('bcorp_post_meta')):
	function bcorp_post_meta() {
		if (get_post_format()) {
			$posttype = '<span class="post-format"><a class="entry-format" href="'.get_post_format_link( get_post_format()).'">'.get_post_format_string( get_post_format()).'</a></span>';
		} else $posttype = '';
		if ( is_sticky() && is_home() && ! is_paged() ) {
			$postdate = '<span class="featured-post">' . esc_html__( 'Sticky', 'bcorp-basics' ) . '</span>';
		} else {
			$postdate = sprintf( '<span class="entry-date"><time datetime="%1$s">%2$s</time></span>',esc_attr(get_the_date('c')),esc_html(get_the_date()));
		}
		if (in_array('category',get_object_taxonomies(get_post_type()))) {
			$postcategory = '<span class="cat-links">'.get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'bcorp-basics' ) ).'</span>';
		} else $postcategory = '';
		$postauthor = sprintf( '<span class="author vcard byline"> by <a class="url fn n" href="%1$s" rel="author">%2$s</a></span>',get_author_posts_url(get_the_author_meta('ID')),get_the_author());
		ob_start();
		echo the_tags( ' <span class="tag-links">', ', ', '</span>' );
		$posttags = ob_get_clean();
		ob_start();
		if (!post_password_required()) { ?>
			<span class="comments-link"><?php comments_popup_link( esc_html__( 'Leave a comment', 'bcorp-basics' ), esc_html__( '1 Comment', 'bcorp-basics' ), esc_html__( '% Comments', 'bcorp-basics' ),'', esc_html__('Comments Off','bcorp-basics')); ?></span><?php
		}
		$postcomments = ob_get_clean();
		if (get_edit_post_link()) {
			$postedit = '<span class="edit-link"><a href="'.esc_url(get_edit_post_link()).'">'.esc_html__( 'Edit', 'bcorp-basics' ).'</a></span>';
		} else $postedit ='';
		if (!is_single()) $posttitle = '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">'.get_the_title().'</a></h2>';
		else 	$posttitle = '<h2 class="entry-title">'.get_the_title().'</h2>';
		echo '<header class="entry-header bcorp-entry-style2">'.$postcategory.$posttitle.'<div class="entry-meta">';
		echo $posttype.$postdate.$postauthor.$posttags.$postcomments.$postedit.'</div></header>';
	}
endif;

if (!function_exists('bcorp_tags')):
	function bcorp_tags() {
		if (is_single()) the_tags( '<span class="tagcloud">', ' ', '</span>' );
	}
endif;

if (!function_exists('bcorp_categorized_blog')):
	function bcorp_categorized_blog() {
		if (false===($all_cats=get_transient('bcorp_category_count')))
			set_transient('bcorp_category_count', count(get_categories(array( 'hide_empty' => 1))));
		if (1!==(int)$all_cats ) return true; else false;
	}
endif;

function bcorp_category_transient_flusher() { delete_transient( 'bcorp_category_count' ); }
add_action( 'edit_category', 'bcorp_category_transient_flusher' );
add_action( 'save_post',     'bcorp_category_transient_flusher' );

if (!function_exists('bcorp_the_attached_image')):
	function bcorp_the_attached_image() {
		$post                = get_post();
		$next_attachment_url = wp_get_attachment_url();
		$attachment_ids = get_posts( array(
			'post_parent'    => $post->post_parent,
			'fields'         => 'ids',
			'numberposts'    => -1,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'menu_order ID',
		) );
		if ( count( $attachment_ids ) > 1 ) {
			foreach ( $attachment_ids as $attachment_id ) {
				if ( $attachment_id == $post->ID ) {
					$next_id = current( $attachment_ids );
					break;
				}
			}
			if ( $next_id ) $next_attachment_url = get_attachment_link( $next_id );
			else $next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
		}
		printf( '<a href="%1$s" rel="attachment">%2$s</a>',esc_url( $next_attachment_url ),wp_get_attachment_image( $post->ID, '900 x 600 cropped' )
		);
	}
endif;

require_once dirname( __FILE__ ).'/includes/plugin-activation.php';
add_action( 'tgmpa_register', 'bcorp_register_required_plugins' );
if (!function_exists('bcorp_register_required_plugins')):
	function bcorp_register_required_plugins() {
    $plugins = array(
			array(
				'name'      => 'BCorp Shortcodes',
				'slug'      => 'bcorp-shortcodes',
				'required'  => false,
			),
			array(
				'name'      => 'BCorp Visual Editor',
				'slug'      => 'bcorp-visual-editor',
				'required'  => false,
			),

	  );
	    $config = array(
	        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
	        'menu'         => 'tgmpa-install-plugins', // Menu slug.
	        'has_notices'  => true,                    // Show admin notices or not.
	        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
	        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
	        'is_automatic' => true,                   // Automatically activate plugins after installation or not.
	        'message'      => '',                      // Message to output right before the plugins table.
	        'strings'      => array(
	            'page_title'                      => esc_html__( 'Install Required Plugins', 'bcorp-basics' ),
	            'menu_title'                      => esc_html__( 'Install Plugins', 'bcorp-basics' ),
	            'installing'                      => esc_html__( 'Installing Plugin: %s', 'bcorp-basics' ), // %s = plugin name.
	            'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'bcorp-basics' ),
	            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' , 'bcorp-basics'), // %1$s = plugin name(s).
	            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' , 'bcorp-basics'), // %1$s = plugin name(s).
	            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' , 'bcorp-basics'), // %1$s = plugin name(s).
	            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' , 'bcorp-basics'), // %1$s = plugin name(s).
	            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'bcorp-basics' ), // %1$s = plugin name(s).
	            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' , 'bcorp-basics'), // %1$s = plugin name(s).
	            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' , 'bcorp-basics'), // %1$s = plugin name(s).
	            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' , 'bcorp-basics'), // %1$s = plugin name(s).
	            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' , 'bcorp-basics'),
	            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' , 'bcorp-basics'),
	            'return'                          => esc_html__( 'Return to Required Plugins Installer', 'bcorp-basics' ),
	            'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'bcorp-basics' ),
	            'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'bcorp-basics' ), // %s = dashboard link.
	            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
	        )
	    );
	    tgmpa( $plugins, $config );
	}
endif;
