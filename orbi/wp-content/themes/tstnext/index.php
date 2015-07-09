<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package bb
 */

get_header(); ?>
<div class="mdl-grid masonry-grid">
	
	<?php
		if(have_posts()){
			while(have_posts()){
				the_post();
				
				if(is_search())
					get_template_part('partials/content', 'search');
				else
					get_template_part('partials/content', get_post_type());
			}  		
		}
		else {
			get_template_part('partials/content', 'none');
			
		}
	?>
	<div class="mdl-cell mdl-cell--4-col masonry-item movable-widget"><?php get_sidebar(); ?></div>
</div>

<?php tst_paging_nav(); ?>

<?php get_footer(); ?>
