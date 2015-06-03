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
		$slider = (function_exists('get_field')) ? get_field('home_slider', $home_id) : '';
		if(!empty($slider)){
			echo do_shortcode($slider);
		}
		
		while(have_posts()){ the_post();
	?>		
		<div id="home_intro" class="intro-text"><span class="wrap"><?php the_content(); ?></span></div>
	<?php } ?>
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
				<div class="frame">
					<div class="bit sm-5 md-12">
						<a href="<?php echo $url;?>" class="block-link"><span></span><img src="<?php echo $img;?>"></a>
					</div>
					<div class="bit sm-7 md-12">
						<h4><a href="<?php echo $url;?>"><?php echo $title;?></a></h4>
						<p><?php echo apply_filters('tst_the_title', $desc);?></p>
					</div>
				</div>
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
			$news = new WP_Query(array('posts_per_page' => 4, 'post_type' => 'post'));
			$words = 20;
			if($news->have_posts()) {
				$items = $news->posts;
		?>
		<div class="frame">
			<div class="bit sm-5 md-6">
				<div class="h-item">
					<a href="<?php echo get_permalink($items[0])?>" class="thumbnail-link">
						<?php echo get_the_post_thumbnail($items[0]->ID, 'post-thumbnail');?>
					</a>
					<h4 class="h-item-title">
						<a href="<?php echo get_permalink($items[0])?>"><?php echo get_the_title($items[0]);?></a>
					</h4>
					<div class="h-item-meta"><?php tst_posted_on($items[0]); ?></div>
					<p><?php echo apply_filters('tst_the_title', wp_trim_words($items[0]->post_excerpt, $words));?></p>
				</div>	
			</div>
			<div class="bit sm-7 md-6">
			<?php if(isset($items[1])) { ?>	
				<div class="h-item">						
					<h4 class="h-item-title">
						<a href="<?php echo get_permalink($items[1])?>"><?php echo get_the_title($items[1]);?></a>
					</h4>
					<div class="h-item-meta"><?php tst_posted_on($items[1]); ?></div>
					<p><?php echo apply_filters('tst_the_title', wp_trim_words($items[1]->post_excerpt, $words));?></p>
				</div>
			<?php } ?>
			
			<?php if(isset($items[2])) { ?>	
				<div class="h-item">						
					<h4 class="h-item-title">
						<a href="<?php echo get_permalink($items[2])?>"><?php echo get_the_title($items[2]);?></a>
					</h4>
					<div class="h-item-meta"><?php tst_posted_on($items[2]); ?></div>
					<p><?php echo apply_filters('tst_the_title', wp_trim_words($items[2]->post_excerpt, $words));?></p>
				</div>
			<?php } ?>
			
			<?php if(isset($items[3])) { ?>	
				<div class="h-item">						
					<h4 class="h-item-title">
						<a href="<?php echo get_permalink($items[3])?>"><?php echo get_the_title($items[3]);?></a>
					</h4>
					<div class="h-item-meta"><?php tst_posted_on($items[3]); ?></div>
					<p><?php echo apply_filters('tst_the_title', wp_trim_words($items[3]->post_excerpt, $words));?></p>
				</div>
			<?php } ?>
			</div>
		</div>
		<?php } ?>	
		</div><!-- .bit -->
		
		<div class="bit md-4"><div class="widgets-wrap"><?php dynamic_sidebar( 'home-sidebar' ); ?></div></div>
		
	</div>
	
</section>
<?php get_footer(); ?>
