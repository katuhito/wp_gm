<?php 
add_action( 'wp_enqueue_scripts', 'qualityblue_enqueue_styles' );
function qualityblue_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style('bootstrap', QUALITY_TEMPLATE_DIR_URI . '/css/bootstrap.css');
	wp_enqueue_style('default', QUALITY_TEMPLATE_DIR_URI . '/css/default.css');
	wp_enqueue_style('theme-menu', QUALITY_TEMPLATE_DIR_URI . '/css/theme-menu.css');
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );
}

add_action( 'after_setup_theme', 'qualityblue_setup' );
   	function qualityblue_setup()
   	{	
		load_theme_textdomain('quality-blue', get_stylesheet_directory() . '/languages' );
	}
?>