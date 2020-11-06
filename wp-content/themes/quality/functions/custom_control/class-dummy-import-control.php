<?php
class quality_dummy_import extends WP_Customize_Section {
	
	public $type = 'quality_import_section';
	protected function render_template() {
	?>
			<li>
				<h3 class="accordion-section-title">
					<?php _e('Activate Homepage','quality'); ?>
				</h3>
				<div class="updated notice notice-success notice-alt is-dismissible">
				<p><?php printf( esc_html__( 'To show the Home Page, click on the buttons given below %s', 'quality' ), '</br></br><a target="_blank" class="button button-blue-secondary" href="'.esc_url( add_query_arg( array( 'page' => 'quality-info#actions_required' ), admin_url( 'themes.php' ) ) ).'">'.esc_html__( 'How to activate homepage', 'quality' ).'</a>'  ); ?></p>
				</div>
			</li>
	<?php }
}
