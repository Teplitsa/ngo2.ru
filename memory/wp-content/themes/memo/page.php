<?php
/**
 * 
 */

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>
<div class="complex-page content-area columns">
<div class="frame">
	
	<div id="primary" class="bit md-8"
		
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'partials/content', 'page' ); ?>
		<?php endwhile; // end of the loop. ?>
		
	</div><!-- #primary -->

	<?php get_sidebar(); ?>
</div>
</div>
<?php get_footer(); ?>
