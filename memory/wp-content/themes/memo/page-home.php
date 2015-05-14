<?php
/**
 * Template Name: Homepage
 *
 * Customize this for home
 */

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>

<div class="frame">
	
	<div id="primary" class="content-area bit md-8">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>				
				<div class="entry-content"><?php the_content(); ?></div>
			<?php endwhile; // end of the loop. ?>

		</main>
	</div>

<?php get_sidebar(); ?>

</div>
<?php get_footer(); ?>
