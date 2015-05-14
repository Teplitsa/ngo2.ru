<?php
/**
 * The template part for displaying results in search pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package bb
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-search'); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h4 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' ); ?>
		<cite><?php the_permalink();?></cite>		
	</header>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>

	
</article>
