<?php
/**
 * Template Name: Homepage
 * The Template for homepage
 * 
 */

get_header(); ?>
	
<section class="home-section slider"><div class="inner">

	<?php frl_cycloneslider('homeslider');?>

</div></section>

<section class="home-section home-blocks"><div class="inner">
		
	<div class="frame">
	<?php echo do_shortcode('[la_banner type="homeblock" num="3" format="home_textblock" format_args="bit-4" orderby="menu_order" order="ASC"]');?>
	</div><!-- .frame -->
	

</div></section><!-- .home-section -->

<section class="home-section callout"><div class="inner">

	<?php the_content();?>

</div></section>

<section class="home-section recent-widgets"><div class="inner">
	
	<div class="frame">
		<div class="bit-3">
		<?php dynamic_sidebar('home_one-sidebar'); ?>
		</div>
		
		<div class="bit-6">
		<?php dynamic_sidebar('home_two-sidebar'); ?>
		</div>
		
		<div class="bit-3">
		<?php dynamic_sidebar('home_three-sidebar'); ?>
		</div>
		
	</div><!-- .frame -->
	
</div></section>

<section class="home-section news-grid"><div class="inner">
	<h3 class="grid-title">Наши новости</h3>
	
	<div class="frame">
		<?php echo do_shortcode('[frl_query q="post_type=post+posts_per_page=8" format="content-embed"]'); ?>
	</div>
	
</div></section><!-- .frame .page-body -->

<?php get_footer(); ?>