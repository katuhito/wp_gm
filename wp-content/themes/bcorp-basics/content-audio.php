<?php
/**
 * Audio Content
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
    $audio_content = ob_get_clean();
    preg_match_all('/\<iframe(.*?)\<\/iframe\>/', $audio_content, $matches);
    if (isset($matches[0][0])){
      echo '<div class="bcorp-audio">'.$matches[0][0].'</div>';
      $audio_content = str_replace($matches[0][0],'',$audio_content);
    } ?>
<div class="entry-content"><?php
    echo $audio_content;
    bcorp_tags();
    bcorp_link_pages(); ?>
</div>
</article><?php
if (!is_single()) { ?>
  </div>
</div><?php
}
