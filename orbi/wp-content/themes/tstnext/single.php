<?php
/**
 * The template for displaying all single posts.
 *
 * @package bb
 */

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>	

<div class="row">

	<div id="primary" class="content-area col s12 l8">
		<main id="main" class="site-main" role="main">

		<?php		
			while(have_posts()){
				the_post();
				get_template_part('partials/content_single', get_post_type());
				
				if('post' == get_post_type())
					get_template_part('partials/related', get_post_type());
			}			
		?>

		</main>
	</div>

<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
