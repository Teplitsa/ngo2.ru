<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package Blank
 */

global $tst_nav_w, $tst_main_w, $tst_side_w, $wp_query;

$tst_nav_w = 0;
$tst_main_w = 8;
$tst_side_w = 4; 
 
get_header(); ?>

	<header class="page-header"><div class="inner">
	
		<div class="frame">
			<h1 class="page-title bit-6"><?php  _e('Search results', 'tst');?></h1>
			<div class="bit-6">&nbsp;</div>
		</div>
		
	</div></header>
	
	<div class="page-body"><div class="inner">
		
		<div class="frame">
	
		<?php
			if($tst_nav_w > 0)
				get_template_part('navbar');
		?>
			
		<div id="primary" class="content-area bit-<?php echo $tst_main_w;?>">
			<div id="main" class="site-main in-loop" role="main">
			
			<?php if(function_exists('la_rs_search_results_form')) la_rs_search_results_form($wp_query);?>
			
			<?php if ( have_posts() ) : ?>
				
				<?php while ( have_posts() ) : the_post(); ?>
					
					<?php
						if(function_exists('la_rs_loop_entry'))
							la_rs_loop_entry();
						else
							get_template_part('content');
					?>					
	
				<?php endwhile; ?>
	
				<?php tst_content_nav( 'nav-below' ); ?>
	
			<?php else : ?>
	
				<?php get_template_part( 'no-results', 'index' ); ?>
	
			<?php endif; ?>
	
			</div>
		</div>
		
		<?php
			if($tst_side_w > 0)
				get_sidebar();
		?>
	
		</div><!-- .frame -->
	
	</div></div><!-- .inner .page-body -->
	
<?php get_footer(); ?>