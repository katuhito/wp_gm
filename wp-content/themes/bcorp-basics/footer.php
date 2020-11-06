<?php
/**
 * Footer
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 */

	$footer_columns=3
?>

<footer id="footer" class="site-footer">
		<div class="bcorp-color-footer">
			<div class="site-content"><?php
				$footer_columns = 3;
				for( $i=1; $i<=$footer_columns; $i++ ) { ?>
					<div class="bcorp-footer-elements-<?php echo esc_attr($footer_columns); ?> bcorp-footer-element-<?php echo $i; ?>">
						<?php dynamic_sidebar( 'bcorp_footer_'.$i); ?>
					</div><?php
				} ?>
			</div>
		</div>
		<div class="bcorp_base_line bcorp-color-base">
			<div class="site-content">
				<div class="bcorp-base-html">
					<?php esc_html_e( 'Basics Theme by BCorp.com', 'bcorp-basics' ); ?>
				</div>
			</div>
		</div>
</footer><?php
	wp_footer(); ?>
</div>
</body>
</html>
