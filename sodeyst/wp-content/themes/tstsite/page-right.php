<?php
/**
 * Template Name: RightSide
 * 
 */

global $tst_nav_w, $tst_main_w, $tst_side_w;


$tst_nav_w = 0;
$tst_main_w = 8;
$tst_side_w = 4; 
	
get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
	
	<?php get_template_part('content', 'page');?>

<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>