<?php
/**
 * Template Name: Calendar
 */

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>

<div class="row">
	
	<div id="primary" class="content-area col s12 l8">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-page'); ?>>
					<div class="entry-content">
						<?php the_content(); ?>		
					</div>
					
				</article>

			<?php endwhile; // end of the loop. ?>

		</main>
	</div>

<?php get_sidebar(); ?>

</div>
<?php get_footer(); ?>
