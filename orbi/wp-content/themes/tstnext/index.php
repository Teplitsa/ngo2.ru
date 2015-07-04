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
	<div id="primary" class="content-area col s12 l8">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) { ?>
			
		<?php
			while(have_posts()){
				the_post();
				
				if(is_search())
					get_template_part('partials/content', 'search');
				else
					get_template_part('partials/content', get_post_type());
			}  

			tst_paging_nav();
		?>

		<?php } else { ?>

			<?php get_template_part('partials/content', 'none'); ?>

		<?php } ?>

		</main>
	</div>
	
	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
