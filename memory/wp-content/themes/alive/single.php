<?php
/**
 * The template for displaying all single posts.
 *
 * @package alv
 */

get_header(); ?>
<header class="section-header">
	<?php get_template_part('partials/title', 'section');?>	
</header>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part('partials/content_single', get_post_type()); ?>
			<?php alv_post_nav(); ?>			

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
