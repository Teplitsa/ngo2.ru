<?php
/**
 * Template Name: Homepage
 *
 * Customize this for home
 */

get_header(); ?>

<section class="home-section intro">
<?php
	while(have_posts()){
		the_post();
		the_content();
	}
?>
</section>

</div><!-- .site-wrap -->

<section class="home-section home-blocks">
<div class="site-wrap">
	<?php if(function_exists('home_blocks')) { if(have_rows('home_blocks')) { ?>
	<div class="frame">	
		
		
		<?php
			while(have_rows('home_blocks')){
				the_row();
				
				$title = get_sub_field('home_block_title');  
				$url = get_sub_field('home_block_url');
				$url = (!empty($url)) ? esc_url($url) : $url;
				$logo_id = get_sub_field('home_block_img');
				$logo_url = wp_get_attachment_url($logo_id);				
			?>	
			<div class="logo bit mf-6 md-3"><div class="logo-frame">			
				<a class="logo-link" title="<?php echo esc_attr($title);?>" href="<?php echo $url;?>"><?php echo $logo ;?></a>
			</div></div>
		<?php } ?>
		
	</div>
	<?php  }} //endif ?>

</div>
</section>
	
<div class="site-wrap"><!-- again -->



<?php get_footer(); ?>
