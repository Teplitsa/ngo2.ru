<?php
/**
 * The template for displaying all single posts.
 *
 * @package bb
 */

global $post;

get_header();
?>
<div class="page-content-grid">
<div class="mdl-grid">
	
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--5-col-tablet ">
		<?php		
			while(have_posts()){
				the_post();
				get_template_part('partials/content_single', get_post_type());				
			}			
		?>		
	</div>
	
	<div class="mdl-cell mdl-cell--3-col mdl-cell--6-col-phone"><?php get_sidebar(); ?></div>
	
</div><!-- .row -->
</div>

<div class="page-footer"><div class="mdl-grid">
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--8-col-tablet">
		<?php get_template_part('partials/related', get_post_type());?>
	</div>
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
</div></div>

<?php get_footer(); ?>
