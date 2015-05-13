<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Blank
 */

global $tst_nav_w, $tst_main_w, $tst_side_w;

$tst_nav_w = 0;
$tst_main_w = 8;
$tst_side_w = 4;

get_header(); ?>
	
	<header class="page-header"><div class="inner">
		
		<div class="frame">
			<h1 class="page-title bit-6">&nbsp;</h1>
			<div class="bit-6"><?php frl_breadcrumbs();?></div>
		</div>
		
	</div></header>
	
	<div class="page-body"><div class="inner">
		
		<div class="frame">
	
			<?php
				if($tst_nav_w > 0)
					get_template_part('navbar');
			?>
			
			<div id="primary" class="content-area bit-<?php echo $tst_main_w;?>">
				<div id="main" class="site-main in-single" role="main">
		
				<?php while ( have_posts() ) : the_post(); ?>
		
					<?php
						$pt = get_post_type();
						$template = 'single';
						if($pt == 'event')
							$template = 'single_event';
							
						get_template_part('content', $template);
					?>				
					
					<?php tst_content_nav( 'nav-below' ); ?>							
					
				<?php endwhile; // end of the loop. ?>
		
				</div><!-- #main -->
			</div><!-- #primary -->
		
			<?php
				if($tst_side_w > 0)
					get_sidebar();
			?>
		
		</div><!-- .frame -->
	
	</div></div><!-- .inner .page-body -->

<?php get_footer(); ?>