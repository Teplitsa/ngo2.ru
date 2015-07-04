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
<div class="row">
	
	<?php
		if(have_posts()){
			while(have_posts()){
				the_post();
				
				if(is_search())
					get_template_part('partials/content', 'search');
				else
					get_template_part('partials/content', get_post_type());
			}  

			tst_paging_nav();
		}
		else {
			get_template_part('partials/content', 'none');
			
		}
	?>
	<div class="col md-6 lg-4"><?php get_sidebar(); ?></div>
</div>

<?php get_footer(); ?>
