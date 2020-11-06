<?php
/**
 * Video Content
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 */

 if (!is_single()) { ?>
 	<div class="bcorp-blog-item bcorp-animated" data-animation="fadeIn">
 		<div class="bcorp-blog-item-inner">
 			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>><?php
 } else { ?>
 			<article id="post-<?php the_ID(); ?>" <?php post_class("bcorp-animated"); ?> data-animation="fadeIn"><?php
 }
      bcorp_post_meta();
			ob_start();
			the_content( esc_html__( 'Read More', 'bcorp-basics' ) );
			$video_content = ob_get_clean();
			preg_match_all('/\<iframe(.*?)\<\/iframe\>/', $video_content, $matches);
			if (isset($matches[0][0])){
				$videoratio=9/16*100;
				echo '<div class="bcorp-video" style="padding-bottom: '.$videoratio.'%;">'.$matches[0][0].'</div>';
				$video_content = str_replace($matches[0][0],'',$video_content);
			} ?>
	<div class="entry-content"><?php
			echo $video_content;
      bcorp_tags();
			bcorp_link_pages(); ?>
	</div>
</article><?php
if (!is_single()) { ?>
		</div>
	</div><?php
}
