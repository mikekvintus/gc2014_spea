<?php
/**
 * The template used for displaying page content
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		// Page thumbnail and title.
		twentyfourteen_post_thumbnail();
		if (!is_front_page()) {
			the_title( '<header class="entry-header"><h1 class="entry-title">', '</h1></header><!-- .entry-header -->' );			
		}
	?>

	<div class="entry-content">
		<?php
		if (is_front_page()) { ?>
					<?php echo do_shortcode( '[adrotate banner="5"]' ) ?>
					<?php echo do_shortcode( '[adrotate banner="6"]' ) ?>	
					<hr style="margin:10px 0">
					<?php echo do_shortcode( '[add_posts show=1 full=true]' ) ?>
		<?php } ?>
		<?php
			the_content();
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );

			edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
		?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->
