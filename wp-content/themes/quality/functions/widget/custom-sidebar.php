<?php	
add_action( 'widgets_init', 'quality_widgets_init');
function quality_widgets_init() {
	/*sidebar*/
	register_sidebar( array(
		'name' => __('Sidebar widget area', 'quality' ),
		'id' => 'sidebar-primary',
		'description' => __('Sidebar widget area','quality'),
		'before_widget' => '<div class="qua_sidebar_widget" >',
		'after_widget' => '</div>',
		'before_title' => '<div class="qua_sidebar_widget_title"><h2>',
		'after_title' => '</h2><div class="qua-separator-small spacer"></div></div>',
	) );
	
	register_sidebar( array(
		'name' => __('Footer widget area', 'quality' ),
		'id' => 'footer-widget-area',
		'description' => __('Footer widget area','quality'),
		'before_widget' => '<div class="col-md-3 qua_footer_widget_column">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="qua_footer_widget_title">',
		'after_title' => '<div class="qua-separator-small"></div></h2>',
	) );
}	                     
?>