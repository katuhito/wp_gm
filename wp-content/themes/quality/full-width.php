<?php
/**
Template Name: Fullwidth Page
*/
 get_header();
?>
<div class="page-seperator"></div>
<div class="container">
  <div class="row">
    <div class="qua_page_heading">
      <h1><?php the_title(); ?></h1>
      <div class="qua-separator"></div>
    </div>
  </div>
</div>

	<div class="container">
		<div class="row qua_blog_wrapper">
			<!-- Blog Area -->
			<div class="col-md-12">
			<?php if(has_post_thumbnail()): ?>
			<div class="qua_blog_post_img">
					<?php $defalt_arg =array('class' => "img-responsive"); ?>
						
						<a  href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail('', $defalt_arg); ?>
						</a>
					</div>
			<?php endif; ?>	
			<?php if( $post->post_content != "" )
			{ ?>
			<div class="qua_blog_post_content">
			<?php if( have_posts()) :  the_post(); ?>		
			<?php the_content(); ?>
				<?php endif; ?>
			</div>
			<?php } ?>			
				<?php comments_template( '', true ); // show comments ?>
			</div>
			<!-- /Blog Area -->	
		</div>
	</div>

<!-- /Blog Section with Sidebar -->
<?php get_footer(); ?>