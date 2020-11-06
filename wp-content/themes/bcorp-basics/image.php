<?php
/**
 * Single
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 *
 */

get_header();
$bcorp_sidebar = bcorp_sidebar_position($post->ID); ?>
<div id="main-content" class="main-content <?php echo esc_attr($bcorp_sidebar); ?>">
	<section id="primary" class="content-area bcorp-color-main">
		<div id="content" class="site-content" role="main">
			<div class="bcorp-row bcorp-content"><?php
				while ( have_posts() ) {
					the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header"><?php
								if ( is_single() ) the_title( '<h3 class="entry-title">', '</h3>' );
								else the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
								bcorp_post_meta(); ?>
						</header>
						<br />
						<div class="entry-content">
							<div class="entry-attachment">
								<div class="attachment">
									<?php bcorp_the_attached_image(); ?>
								</div>
								<?php if ( has_excerpt() ) : ?>
								<div class="entry-caption">
									<?php the_excerpt(); ?>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</article>
					<nav id="image-navigation" class="navigation image-navigation">
						<div class="nav-links">
						<?php previous_image_link( false, '<div class="previous-image">' . esc_html__( 'Previous Image', 'bcorp-basics' ) . '</div>' ); ?>
						<?php next_image_link( false, '<div class="next-image">' . esc_html__( 'Next Image', 'bcorp-basics' ) . '</div>' ); ?>
						</div>
					</nav>
					<br /><?php
					if ( comments_open() || get_comments_number() ) comments_template();
				}
			?></div><?php
			if ($bcorp_sidebar!='bcorp_no_sidebar') {
				get_sidebar( 'content' );
				get_sidebar();
			} ?>
		</div>
	</section>
</div>
<?php
get_footer();
