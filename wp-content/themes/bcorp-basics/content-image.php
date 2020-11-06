<?php
/**
 * Image Content
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
 }
 	bcorp_post_thumbnail(); ?>
	<header class="entry-header"><?php
		if ( is_single() ) the_title( '<h3 class="entry-title">', '</h3>' );
		else the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
		bcorp_post_meta();  ?>
	</header>
	<div class="entry-content">
		<?php
			the_content( esc_html__( 'Read More', 'bcorp-basics' ) );
      bcorp_tags();
			bcorp_link_pages(); ?>
	</div>
</article><?php
if (!is_single()) { ?>
		</div>
	</div><?php
}
