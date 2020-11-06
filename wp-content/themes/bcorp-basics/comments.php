<?php
/**
 * Comments
 *
 * @package BCorp Basics
 * @author Tim Brattberg
 * @link http://www.bcorp.com
 */

if ( post_password_required() ) return; ?>
<div id="comments" class="comments-area bcorp-color-main">
	<?php if ( have_comments() ) { ?>
	<h5 class="comments-title">
		<?php
			printf( _n( 'One Comment', '%1$s Comments', get_comments_number(), 'bcorp-basics' ),
				number_format_i18n( get_comments_number() ) );
		?>
	</h5>
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { ?>
	<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
		<h5 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'bcorp-basics' ); ?></h5>
		<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'bcorp-basics' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'bcorp-basics' ) ); ?></div>
	</nav><!-- #comment-nav-above -->
	<?php } // Check for comment navigation. ?>
	<ol class="comment-list">
		<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'=> 74,
			) );
		?>
	</ol><!-- .comment-list -->
	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { ?>
	<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
		<h5 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'bcorp-basics' ); ?></h5>
		<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'bcorp-basics' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'bcorp-basics' ) ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php } // Check for comment navigation. ?>

	<?php if ( ! comments_open() ) { ?>
	<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'bcorp-basics' ); ?></p>
	<?php } ?>
	<?php } // have_comments() ?>
	<?php
				$commenter = wp_get_current_commenter();
				$req = get_option( 'require_name_email' );
				$aria_req = ( $req ? " aria-required='true'" : '' );
				comment_form(array('title_reply' => ''.esc_html__('Leave a Comment','bcorp-basics').'',
													 'comment_field' =>'<p class="comment-form-comment bcorp-cell bcorp-1-1"><textarea id="comment" class="bcorp-1-1" name="comment" cols="45" rows="8" aria-required="true"
													 placeholder="'. esc_html__( 'Comment...', 'bcorp-basics' ) .'"></textarea></p>',
													 'comment_notes_before' => '',
													 'fields' =>  array('author' => '<div class="comment-form-author bcorp-cell bcorp-1-3"><input id="author" name="author" type="text" placeholder="'.
													 																esc_html__( 'Name', 'bcorp-basics' ) .( $req ? ' (required)' : '' ).'" value="' .
																				 									esc_attr( $commenter['comment_author'] ).'" ' . $aria_req . ' /></div>',
						   											 					'email' =>  '<div class="comment-form-email bcorp-cell bcorp-1-3 bcorp-gutter bcorp-no-gutter-mobile"><input id="email" name="email" type="text" placeholder="'.
																													esc_html__( 'Email', 'bcorp-basics' ) .( $req ? ' (required)' : '' ).'" value="' .
																													esc_attr(  $commenter['comment_author_email'] ) .'" ' . $aria_req . ' /></div>',
						   																'url' => '<div class="comment-form-url bcorp-cell bcorp-1-3 bcorp-gutter bcorp-no-gutter-mobile"><input id="url" name="url" type="text" placeholder="'.
																											 esc_html__( 'Website', 'bcorp-basics' ) .'" value="' . esc_attr( $commenter['comment_author_url'] ).
						     																 			 '"  /></div>',
						 																	)
												 )); ?>
</div><!-- #comments -->
