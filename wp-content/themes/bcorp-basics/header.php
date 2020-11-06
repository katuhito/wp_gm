<?php
/**
 * Header
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 */

?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
	<head>
    <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<?php wp_head(); ?>
	  </head>
	  <body <?php body_class("bcorp-color-head-bg-main"); ?>>
			<div class="bcorp-wrapper">
				<header>
  						<div class="bcorp-fixed-navbar-wrapper">
  						<div id="bcorp-fixed-navbar" class="bcorp-fixed-navbar bcorp-color-header">
  						<div class="site-content">
                <div class="bcorp-header-wrap">
                  <div id="bcorp-logo">
                    <a href="<?php echo esc_url(home_url()); ?>">
                        <span id="bcorp-logo-font" class="bcorp-logo bcorp-logo-font"><?php bloginfo('name');?></span>
                    </a>
                  </div><?php
									$bcorp_menu_search ='<li class="bcorp-search">'.get_search_form(false).'</li>';?>
                  <label class="bcorp-menu-toggle" for="mobile-menu-button" onclick><span class="menu-link"></span></label></div>
                  <input class="bcorp-menu-toggle" type="checkbox" id="mobile-menu-button" checked><?php
                  $bcorp_main_menu_args = array(
                    'theme_location'  => 'floating_nav',
                    'menu'            => '',
                    'container'       => '',
                    'container_class' => '',
                    'container_id'    => 'menu-wrap',
                    'menu_class'      => 'menu',
                    'menu_id'         => 'menu',
                    'echo'            => true,
                    'fallback_cb'     => false,
                    'before'          => '',
                    'after'           => '',
                    'link_before'     => '',
                    'link_after'      => '',
                    'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s'.$bcorp_menu_search.'</ul>',
                    'depth'           => 0,
                    'walker'          => ''
                  ); ?>
                  <nav class="bcorp-main-nav bcorp-main-nav-hidden"><?php
                     $bcorp_main_menu_args['items_wrap'] = '<ul id="%1$s" class="%2$s">%3$s</ul>';
                     wp_nav_menu($bcorp_main_menu_args); ?>
                  </nav>
                </div><?php
                  $bcorp_main_menu_args['items_wrap'] = '<ul id="%1$s" class="%2$s">%3$s'.$bcorp_menu_search.'</ul>';
                  $bcorp_main_menu_args['menu_id'] = 'menu_floating'; ?>
                  <div id="bcorp-floating-nav" class="bcorp-fullwidth  bcorp-color-header-bg-alt bcorp-color-header-border">
                    <div class="site-content" style="center">
                      <nav class="bcorp-main-nav"><?php echo wp_nav_menu($bcorp_main_menu_args); ?></nav>
                    </div>
                  </div>
  						</div>
  					</div>
				</header><?php
				if ( !is_home() ) { ?>
					<section id="bcorp-title-bar" class="bcorp-title-bar bcorp-color-alt">
						<div id="title" class="site-content" role="main"><?php
            get_the_archive_title();
            bcorp_get_breadcrumbs(); ?>
						</div>
					</section>
<?php }
