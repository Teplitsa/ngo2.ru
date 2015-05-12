<?php
/**
 * Template Name: Leyka Home
 **/

global $post; 

get_header();



?>
<div class="content thin">
	<?php while (have_posts()){ the_post(); ?>
	
	<article id="post-<?php the_ID(); ?>" <?php post_class('leyka-home'); ?>>			
		
		<div class="content-inner">
	  
			<?php the_content(); ?>
	
		</div><!-- /content-inner -->
	
	</article>
	<?php	} //endwhile ?>
	
	<?php do_action('grt_content_bottom');?>
	
</div> <!-- /content -->
		
<?php get_footer(); ?>