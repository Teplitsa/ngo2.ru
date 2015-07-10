<?php
/**
 * The template for displaying all single posts.
 *
 * @package bb
 */

global $post;

get_header();
?>
<div class="mdl-grid">
	
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--5-col-tablet">
		<?php		
			while(have_posts()){
				the_post();
				get_template_part('partials/content_single', get_post_type());				
			}			
		?>		
	</div>
	
	<div class="mdl-cell mdl-cell--3-col "><?php get_sidebar(); ?></div>
	
</div><!-- .row -->

<?php get_footer(); ?>
