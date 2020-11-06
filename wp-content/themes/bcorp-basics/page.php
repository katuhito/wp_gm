<?php
/**
 * Page
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 *
 */

get_header();
$bcorp_sidebar = bcorp_sidebar_position($post->ID); ?>
<div id="main-content" class="main-content <?php echo esc_attr($bcorp_sidebar); ?>">
	<section id="primary" class="content-area bcorp-color-main-bg-main">
		<div id="content" class="site-content bcorp-color-main" role="main">
			<div class="bcorp-row bcorp-color-main"><?php
				while ( have_posts() ) {
					the_post();
					the_content();
					if (comments_open() || get_comments_number())	comments_template();
				} ?>
			</div><!-- #bcorp-row --><?php
			if ($bcorp_sidebar!='bcorp_no_sidebar') {
				get_sidebar( 'content' );
				get_sidebar();
			} ?>
		</div><!-- #content -->
	</section><!-- #primary -->
</div><!-- #main-content -->
<?php get_footer();
