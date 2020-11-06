<?php
  /**Theme Name	: Quality
   * Theme Core Functions and Codes
  */	
  	/**Includes reqired resources here**/
  	define('QUALITY_TEMPLATE_DIR_URI',get_template_directory_uri());	
  	define('QUALITY_TEMPLATE_DIR',get_template_directory());
  	define('QUALITY_THEME_FUNCTIONS_PATH',QUALITY_TEMPLATE_DIR.'/functions');	
  	define('QUALITY_THEME_OPTIONS_PATH',QUALITY_TEMPLATE_DIR_URI.'/functions/theme_options');
  
	require( QUALITY_THEME_FUNCTIONS_PATH . '/menu/new_Walker.php'); //NEW Walker Class Added.  		
  	require_once( QUALITY_THEME_FUNCTIONS_PATH . '/scripts/scripts.php');     //Theme Scripts And Styles	
  	require( QUALITY_THEME_FUNCTIONS_PATH . '/resize_image/resize_image.php'); //Image Resizing 	
  	require( QUALITY_THEME_FUNCTIONS_PATH . '/commentbox/comment-function.php'); //Comment Handling
  	require( QUALITY_THEME_FUNCTIONS_PATH . '/widget/custom-sidebar.php'); //Sidebar Registration
	
	
	//Customizer 
	require( QUALITY_THEME_FUNCTIONS_PATH . '/customizer/customizer-service.php');
	require( QUALITY_THEME_FUNCTIONS_PATH . '/customizer/customizer-slider.php');
	require( QUALITY_THEME_FUNCTIONS_PATH . '/customizer/customizer-copyright.php');
	require( QUALITY_THEME_FUNCTIONS_PATH . '/customizer/customizer-home.php');
	require( QUALITY_THEME_FUNCTIONS_PATH . '/customizer/customizer-project.php');
	require( QUALITY_THEME_FUNCTIONS_PATH . '/customizer/customizer-blog.php');
	require( QUALITY_THEME_FUNCTIONS_PATH . '/customizer/customizer-pro.php');
	require( QUALITY_THEME_FUNCTIONS_PATH . '/customizer/customizer_import_data.php');
	require( QUALITY_THEME_FUNCTIONS_PATH . '/font/font.php');
	
	//wp title tag starts here
  	function quality_head( $title, $sep )
  	{	global $paged, $page;		
  		if ( is_feed() )
  			return $title;
  		// Add the site name.
  		$title .= get_bloginfo( 'name' );
  		// Add the site description for the home/front page.
  		$site_description = get_bloginfo( 'description' );
  		if ( $site_description && ( is_home() || is_front_page() ) )
  			$title = "$title $sep $site_description";
  		// Add a page number if necessary.
  		if ( $paged >= 2 || $page >= 2 )
  			$title = "$title $sep " . sprintf( _e( 'Page', 'quality' ), max( $paged, $page ) );
  		return $title;
  	}	
  	add_filter( 'wp_title', 'quality_head', 10, 2);
  	
  	add_action( 'after_setup_theme', 'quality_setup' ); 	
  	function quality_setup()
  	{	
		//content width
		if ( ! isset( $content_width ) ) $content_width = 700;//In PX
		// Load text domain for translation-ready
  		load_theme_textdomain( 'quality', QUALITY_THEME_FUNCTIONS_PATH . '/lang' );
  		add_theme_support( 'post-thumbnails' ); //supports featured image
  		// This theme uses wp_nav_menu() in one location.
  		register_nav_menu( 'primary', __( 'Primary Menu', 'quality' ) ); //Navigation
  		// theme support 	
  		add_theme_support( 'automatic-feed-links');
		
		//Title tag
		add_theme_support( "title-tag" );
  		
  		require_once('theme_setup_data.php');
  		// setup admin pannel defual data for index page		
  		$quality_pro_options=theme_data_setup();

	if ( is_admin() ) {

        global $quality_required_actions;

        /*
         * id - unique id; required
         * title
         * description
         * check - check for plugins (if installed)
         * plugin_slug - the plugin's slug (used for installing the plugin)
         *
         */
        $quality_required_actions = array(
			array(
                "id" => 'quality-req-ac-frontpage-latest-news',
                "title" => esc_html__( 'Get the one page template' ,'quality' ),
            ),
            array(
                "id" => 'quality-req-ac-install-pirate-forms',
                "title" => esc_html__( 'Install Pirate Forms' ,'quality' ),
                "check" => defined("PIRATE_FORMS_VERSION"),
                "plugin_slug" => 'pirate-forms'
            ),
            array(
                "id" => 'quality-req-ac-check-pirate-forms',
                "title" => esc_html__( 'Check the contact form after installing Pirate Forms' ,'quality' ),
            )
        );
		require( QUALITY_THEME_FUNCTIONS_PATH . '/quality-info/welcome-screen.php');
	}

		
  	}
  	// Read more tag to formatting in blog page 
  	function quality_new_content_more($more)
	{  global $post;
		return '<div class="blog-btn-col"><a href="' . get_permalink() . "#more-{$post->ID}\" class=\"qua_blog_btn\">Read More<i class='fa fa-long-arrow-right'></i></a></div>";
	}   
	add_filter( 'the_content_more_link', 'quality_new_content_more' );
	
	
	
	add_filter( "the_excerpt", "quality_add_class_to_excerpt" );
	function quality_add_class_to_excerpt( $excerpt ) {
    return str_replace('<p', '<p class="qua-blog-post-description"', $excerpt);
	}
	
	add_action( 'admin_init', 'quality_detect_button' );
	function quality_detect_button() {
	wp_enqueue_style( 'quality-info-button', get_template_directory_uri() . '/css/import-button.css' );
	}
	
  ?>