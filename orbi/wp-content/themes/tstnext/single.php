<?php
/**
 * The template for displaying all single posts.
 *
 * @package bb
 */

get_header(); ?>


<div class="row">

	<div class="col md-8 lg-6 lg-offset-3">
		<?php		
			while(have_posts()){
				the_post();
				get_template_part('partials/content_single', get_post_type());
				
				//if('post' == get_post_type())
					//get_template_part('partials/related', get_post_type());
			}			
		?>		
	</div>
	
	<div class="col md-4 lg-3"><?php get_sidebar(); ?></div>
	
</div><!-- .row -->
<?php get_footer(); ?>
