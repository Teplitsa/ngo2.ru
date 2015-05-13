<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Blank
 */

global $tst_nav_w, $tst_main_w, $tst_side_w;

$tst_nav_w = 0;
$tst_main_w = 12;
$tst_side_w = 0;

get_header(); ?>


<header class="page-header"><div class="inner">

	<div class="frame">
		<h1 class="page-title bit-6"><?php echo __('Page is not found', 'tst');?></h1>
		<div class="bit-6">&nbsp;</div>
	</div>
		
</div></header>
	
<div class="page-body"><div class="inner">
		
	<div class="frame">
	
	<div id="primary" class="content-area bit-<?php echo $tst_main_w;?>">
		<div id="main" class="site-main in-page" role="main">		

			<section class="not-found-404">
				

				<div class="page-content">
					<?php  _e("<p>We're sorry, but there is no such page on our website.</p>
					<p>Let's try to find an information you needed.</p>", 'tst');?>

					<?php get_search_form(); ?>


				</div><!-- .page-content -->
			</section><!-- .error-404 -->
			
			

		</div>
	</div><!-- #primary -->

	</div><!-- .frame -->

</div></div><!-- .inner .page-body -->


<?php get_footer(); ?>