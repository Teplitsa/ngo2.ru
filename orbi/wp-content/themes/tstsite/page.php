<?php
/**
 * The Template for displaying single page
 * 
 */

global $tst_nav_w, $tst_main_w, $tst_side_w;

$tst_nav_w = 2;
$tst_main_w = 7;
$tst_side_w = 3;

	
get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
	
	<?php get_template_part('content', 'page');?>

<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>