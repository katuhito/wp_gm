<?php
/**
 * 404 Not Found
 *
 * @package bcorp-basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 *
 */

get_header();
$bcorp_sidebar = bcorp_sidebar_position("404"); ?>
<div id="main-content" class="main-content <?php echo esc_attr($bcorp_sidebar); ?>">
	<section id="primary" class="content-area bcorp-color-main">
		<div id="content" class="site-content" role="main">
			<div class="bcorp-row">
				<div class="page-content">
					<br /><br /><p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'bcorp-basics' ); ?></p>
					<?php get_search_form(); ?>
				</div>
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
