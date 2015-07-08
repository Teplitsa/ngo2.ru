<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package bb
 */

get_header(); ?>
<div class="row">

	<div class="col md-8 lg-6 lg-offset-3">
		<?php		
			while(have_posts()){
				the_post();
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-page'); ?>>
				<div class="entry-content">
					<?php the_content(); ?>		
				</div>				
			</article>
		<?php } ?>		
	</div>
	
	<div class="col md-4 lg-3"><?php get_sidebar(); ?></div>
	
</div><!-- .row -->

<?php get_footer(); ?>
