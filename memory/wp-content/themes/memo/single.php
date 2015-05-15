<?php
/**
 * The template for displaying all single posts.
 *
 * @package bb
 */

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>	

<div class="complex-single">
	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part('partials/content_single', get_post_type()); ?>
		<?php memo_post_nav(); ?>

		<?php endwhile; // end of the loop. ?>
</div>

<?php get_footer(); ?>
