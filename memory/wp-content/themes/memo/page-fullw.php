<?php
/**
 * Template Name: FullWidth
 **/

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>
<div class="complex-page content-area fullwidth">
	
	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'partials/content', 'page' ); ?>

	<?php endwhile; // end of the loop. ?>

</div>
<?php get_footer(); ?>
