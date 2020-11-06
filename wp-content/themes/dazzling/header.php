<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package dazzling
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

<!-- AMP Analytics --><script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>

<meta="<?php bloginfo( 'charset' ); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<link rel="canonical" href="http://katuhito.info/">
<link rel="alternate" hreflang="ja" href="http://katuhito.info/">
<link rel="alternate" hreflang="en" href="http://katuhito.info/en/">

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KJX2C55');</script>
<!-- End Google Tag Manager -->

<?php wp_head(); ?>


<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({
          google_ad_client: "ca-pub-5974217711969010",
          enable_page_level_ads: true
     });
</script>

</head>

<body <?php body_class(); ?>>

<!-- Google Tag Manager -->
<amp-analytics config="https://www.googletagmanager.com/amp.json?id=GTM-KQKJ2LT&gtm.url=SOURCE_URL" data-credentials="include"></amp-analytics>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KJX2C55"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<div id="page" class="hfeed site">

	<nav class="navbar navbar-default" role="navigation">
		<div class="container">
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
			    <span class="sr-only"><?php _e( 'Toggle navigation', 'dazzling' ); ?></span>
			    <span class="icon-bar"></span>
			    <span class="icon-bar"></span>
			    <span class="icon-bar"></span>
			  </button>

				<div id="logo">

					<?php echo is_home() ?  '<h1 class="site-title">' : '<span class="site-title">'; ?>

						<?php if( get_header_image() != '' ) : ?>

							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php header_image(); ?>"  height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="<?php bloginfo( 'name' ); ?>"/></a>


						<?php endif; // header image was removed ?>

						<?php if( !get_header_image() ) : ?>

							<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>

						<?php endif; // header image was removed (again) ?>

					<?php echo is_home() ?  '</h1>' : '</span>'; ?><!-- end of .site-name -->

				</div><!-- end of #logo -->

				<?php if( !get_header_image() ) : ?>

					<?php $description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) : ?>
						<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
					<?php
					endif; ?>

				<?php endif; ?>
						
			</div>
				<?php dazzling_header_menu(); ?>
		</div>
	</nav><!-- .site-navigation -->

        <div class="top-section">
		<?php dazzling_featured_slider(); ?>
		<?php dazzling_call_for_action(); ?>
        </div>
        <div id="content" class="site-content container">

            <div class="container main-content-area"><?php

                global $post;
                if( get_post_meta($post->ID, 'site_layout', true) ){
                        $layout_class = get_post_meta($post->ID, 'site_layout', true);
                }
                else{
                        $layout_class = of_get_option( 'site_layout' );
                }
                if( is_home() && is_sticky( $post->ID ) ){
                        $layout_class = of_get_option( 'site_layout' );
                }
                ?>
                <div class="row <?php echo $layout_class; ?>">


