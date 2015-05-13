<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package bb
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-page'); ?>>
	<div class="entry-content">
		<?php the_content(); ?>		
	</div>
	
</article><!-- #post-## -->
