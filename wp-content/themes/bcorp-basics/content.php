<?php
/**
 * Content
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 *
 */

if (!is_single()) { ?>
	<div class="bcorp-blog-item bcorp-animated" data-animation="fadeIn">
		<div class="bcorp-blog-item-inner">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>><?php
} else { ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class("bcorp-animated"); ?> data-animation="fadeIn"><?php
}
				bcorp_post_meta();
				bcorp_post_thumbnail();
				if ( is_search() ) { ?>
					<div class="entry-summary"><?php
						the_excerpt(); ?>
					</div><?php
				} elseif (get_post_type() == 'portfolio') { ?>
					<div class="entry-summary"><?php
						the_excerpt(); ?>
					</div><?php
				} else { ?>
					<div class="entry-content"><?php
						the_content( esc_html__( 'Read More', 'bcorp-basics' ) );
						bcorp_tags();
			      bcorp_link_pages();
						?>
					</div><?php
				} ?>
			</article><?php
if (!is_single()) { ?>
		</div>
	</div><?php
}
