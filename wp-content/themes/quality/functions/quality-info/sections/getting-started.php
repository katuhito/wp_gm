<?php
/**
 * Getting started template
 */

$customizer_url = admin_url() . 'customize.php' ;
?>

<div id="getting_started" class="quality-tab-pane active">

	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<h1 class="quality-info-title text-center"><?php echo __('About Quality Theme','quality'); ?><?php if( !empty($quality['Version']) ): ?> <sup id="quality-theme-version"><?php echo esc_attr( $quality['Version'] ); ?> </sup><?php endif; ?></h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="quality-tab-pane-half quality-tab-pane-first-half">
				<p><?php esc_html_e( 'Business theme which is ideal for creating a corporate / business website.It boasts of 3 beautifully designed page template namely Business Page, Full-width and Blog. Each section in the Business Template is well designed according to the business requirements. Section like Banner, Services, Projects etc etc are there.','quality');?></p>
				<p>
				<?php esc_html_e( 'You can use this theme for any busniess type. Theme is compatible with contact form 7, so that you can create any type of  contact page. Theme also give clean and effective look to WordPress core gallery feature', 'quality' ); ?>
				</p>
				</div>
			</div>
			<div class="col-md-6">
				<div class="quality-tab-pane-half quality-tab-pane-first-half">
				<img src="<?php echo esc_url( get_template_directory_uri() ) . '/functions/quality-info/img/quality.png'; ?>" alt="<?php esc_html_e( 'Quality Blue Child Theme', 'quality' ); ?>" />
				</div>
			</div>	
		</div>
	
	
		 <div class="row">
			<div class="quality-tab-center">

				<h1><?php esc_html_e( "Useful Links", 'quality' ); ?></h1>

			</div>
			<div class="col-md-6"> 
				<div class="quality-tab-pane-half quality-tab-pane-first-half">

					<a href="<?php echo esc_url('http://webriti.com/quality-free/'); ?>" target="_blank"  class="info-block"><div class="dashicons dashicons-desktop info-icon"></div>
					<p class="info-text"><?php echo __('Lite Demo','quality'); ?></p></a>
					
					
					<a href="<?php echo esc_url('http://webriti.com/quality'); ?>" target="_blank"  class="info-block"><div class="dashicons dashicons-book-alt info-icon"></div>
					<p class="info-text"><?php echo __('View Pro','quality'); ?></p></a>
					
					
					
				</div>
			</div>
			<div class="col-md-6">	
				
				<div class="quality-tab-pane-half quality-tab-pane-first-half">
					
					<a href="<?php echo esc_url('https://wordpress.org/support/view/theme-reviews/quality'); ?>" target="_blank"  class="info-block"><div class="dashicons dashicons-smiley info-icon"></div>
					<p class="info-text"><?php echo __('Rate This Theme','quality'); ?></p></a>
					
					<a href="<?php echo esc_url('https://wordpress.org/support/theme/quality'); ?>" target="_blank"  class="info-block"><div class="dashicons dashicons-sos info-icon"></div>
					<p class="info-text"><?php echo __('Support','quality'); ?></p></a>
				</div>
			</div>
		</div>	
	</div>
</div>	