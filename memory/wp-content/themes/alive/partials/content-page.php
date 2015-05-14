<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package alv
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-page'); ?>>

	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>		
	</div>
	
</article><!-- #post-## -->
