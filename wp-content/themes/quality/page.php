<?php 
  get_header(); ?>
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
    <div class="<?php if( is_active_sidebar('sidebar-primary')) { echo "col-md-8"; } else { echo "col-md-12"; } ?>">
      <?php the_post(); ?>
	  <div class="qua_blog_section" >
	   <?php if(has_post_thumbnail()): ?>
        <div class="qua_blog_post_img">
          <?php $defalt_arg =array('class' => "img-responsive"); ?>
         <a  href="<?php the_permalink(); ?>">
          <?php the_post_thumbnail('quality_blog_img', $defalt_arg); ?>
          </a>
        </div>
		 <?php endif; 	
		if (!is_page()) { ?>
        <div class="qua_post_date">
          <span class="date"><?php echo get_the_date('j'); ?></span>
          <h6><?php echo the_time('M'); ?></h6>
        </div>
		<?php } ?>
        <div class="clear"></div>
		<?php if ( get_the_content()!="" ) {?>
        <div class="qua_blog_post_content">
          <?php the_content(); ?>
		</div>
		<?php }?>
      </div>
	  <?php comments_template('',true); ?>
    </div>
    <?php get_sidebar(); ?>		
  </div>
</div>
<?php get_footer(); ?>