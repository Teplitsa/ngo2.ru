<?php
/**
 * The main template file.
 **/

global $tst_nav_w, $tst_main_w, $tst_side_w;

$tst_nav_w = 0;
$tst_main_w = 8;
$tst_side_w = 4;

get_header(); ?>

	<header class="page-header"><div class="inner">
	
		<div class="frame">
			<h1 class="page-title bit-6"><?php get_template_part( 'title', 'archive');?></h1>
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
			<div id="main" class="site-main in-loop" role="main">
	
			<?php if ( have_posts() ) : ?>
				
				<?php while ( have_posts() ) : the_post(); ?>
	
					<?php get_template_part( 'content', get_post_format() ); ?>
	
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