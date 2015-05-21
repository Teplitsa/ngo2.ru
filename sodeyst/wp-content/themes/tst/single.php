<?php
/**
 * The template for displaying all single posts.
 *
 * @package bb
 */

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>	

<div class="frame">

	<div id="primary" class="content-area bit md-9">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part('partials/content_single', get_post_type()); ?>
			<?php //tst_post_nav(); ?>

		<?php endwhile; // end of the loop. ?>

		</main>
	</div>

<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
