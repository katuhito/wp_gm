<?php
/**
 * Sidebar
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 *
 */

?><ul class="sidebar">
	<?php
		if ( is_active_sidebar( 'everywhere' ) ) : ?>
			<?php dynamic_sidebar( 'everywhere' ); ?>
	<?php else : ?>
		<!-- This content shows up if there are no widgets defined in the backend. -->
		<div class="alert alert-message">
			<p><?php esc_html_e('Please activate some Widgets','bcorp-basics'); ?>.</p>
		</div>
	<?php endif; ?>
</ul>
