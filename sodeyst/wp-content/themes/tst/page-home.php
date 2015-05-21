<?php
/**
 * Template Name: Homepage
 *
 * Customize this for home
 */

global $post;

$home_id = $post->ID;
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
	<?php if(function_exists('have_rows')) { if(have_rows('home_blocks', $home_id)) { ?>
	<div class="frame">	
		<?php
			while(have_rows('home_blocks', $home_id)){
				the_row();
				
				$title = get_sub_field('home_block_title');
				$desc = get_sub_field('home_block_desc');  
				$url = get_sub_field('home_block_url');
				$url = (!empty($url)) ? esc_url($url) : $url;
				
				$logo_id = get_sub_field('home_block_img');
				$logo_url = wp_get_attachment_url($logo_id);
				$img = aq_resize(wp_get_attachment_url($logo_id), 306, 152, true);
			?>
			
			<div class="block bit md-4">
				<a href="<?php echo $url;?>" class="block-link"><span><img src="<?php echo $img;?>"></span></a>
				<h4><a href="<?php echo $url;?>"><?php echo $title;?></a></h4>
				<p><?php echo apply_filters('tst_the_title', $title);?></p>
			</div>			
		<?php } ?>
		
	</div>
	<?php  }} //endif ?>

</div>
</section>
	
<div class="site-wrap"><!-- again -->

<section class="home-section content">
	<div class="frame">
		<div class="bit md-8">
			<h3 class="section-title">Новое в блоге <a href="<?php echo home_url('blog');?>">&gt;&gt;</a></h3>
		<?php
			$news = new WP_Query(array('posts_per_page' => 3));
			if($news->have_posts()) {
				$items = $news->posts;
		?>
			<div class="frame">
				<div class="bit sm-6">
				<div class="h-item">
					<a href="<?php echo get_permalink($items[0])?>" class="thumbnail-link"></a>
					<h4 class="h-item-title"><a href="<?php echo get_permalink($items[0])?>"><?php echo get_the_title($items[0]);?></a></h4>
					
				</div>	
				</div>
				<div class="bit sm-6">
					
				</div>
			</div>
		<?php } ?>
		</div>
		<div class="bit md-4">
			<?php dynamic_sidebar( 'home-sidebar' ); ?>
		</div>
	</div>
	
</section>
<?php get_footer(); ?>
