<?php
/**
 * The template used for displaying page content 
 */

global $tst_nav_w, $tst_main_w, $tst_side_w;
?>

<header class="page-header"><div class="inner">

	<div class="frame">
		<h1 class="page-title bit-6"><?php the_title(); ?></h1>
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
		<div id="main" class="site-main in-page" role="main">		
	
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				
				<?php
					$thumb = get_the_post_thumbnail(null, 'long');
					if(!empty($thumb)):
				?>
					<div class="entry-preview"><?php echo $thumb;?></div>
				<?php endif;?>
				
				<div class="entry-content"><?php the_content(); ?></div>
				
			</article>
			
			<?php frl_page_actions();?>	
	
		</div>
	</div><!-- #primary -->
	
	<?php
		if($tst_side_w > 0)
			get_sidebar();
	?>
	
	</div><!-- .frame -->

</div></div><!-- .inner .page-body -->
