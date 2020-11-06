<?php
/**
 * Index
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 *
 */

get_header();
$bcorp_sidebar = bcorp_sidebar_position("index"); ?>
<div id="main-content" class="main-content <?php echo esc_attr($bcorp_sidebar); ?>">
	<section id="primary" class="content-area bcorp-color-main">
		<div id="content" class="site-content" role="main">
			<div class="bcorp-row bcorp-content bcorp-blog-single bcorp-blog-main"><?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						get_template_part( 'content', get_post_format() );
					}
					the_posts_pagination( array(
    				'prev_text' => '&larr; '.__( 'PREVIOUS', 'bcorp-basics' ),
    				'next_text' => __( 'NEXT', 'bcorp-basics' ).' &rarr;',
					) );
				} else get_template_part( 'content', 'none' ); ?>
			</div><?php
			if ($bcorp_sidebar!='bcorp_no_sidebar') {
				get_sidebar( 'content' );
				get_sidebar();
			} ?>
		</div>
	</section>
</div>
<?php
get_footer();
