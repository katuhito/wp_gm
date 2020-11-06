<?php
/**
 * Link Content
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 */

 if (!is_single()) { ?>
 	<div class="bcorp-blog-item bcorp-animated" data-animation="fadeIn">
 		<div class="bcorp-blog-item-inner">
 			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>><?php
 } else { ?>
 			<article id="post-<?php the_ID(); ?>" <?php post_class("bcorp-animated"); ?> data-animation="fadeIn"><?php
 } ?>
	<header class="entry-header"><?php
		the_title( '<h3 class="entry-title"><a href="' . esc_url(bcorp_get_link_url()) . '" rel="bookmark">', '</a></h3>' );
?>
	</header>
</article><?php
if (!is_single()) { ?>
		</div>
	</div><?php
}
