<?php
/**
 * Template Name: Homepage
 *
 * Customize this for home
 */

global $post;

$home_id = $post->ID;
get_header(); ?>

<section id="home_intro" class="home-section intro">
	<?php
		$slider = (function_exists('get_field')) ? get_field('home_slider', $home_id) : '';
		if(!empty($slider)){
			echo do_shortcode($slider);
		}
		
		while(have_posts()){ the_post();
	?>
		<div class="intro-text"><span class="wrap"><?php the_content(); ?></span></div>
	<?php } ?>
</section>

<section class="home-section home-posts">
<?php
	$query = new WP_Query(array(
		'post_type' => 'post',
		'orderby' => 'rand',
		'posts_per_page' => 6
	));
?>

	<div class="loop-holder card-board">
		<div class="frame">
		<?php	
			while ($query->have_posts() ) {
				$query->the_post();				
				get_template_part( 'partials/content', 'story');
			}
			wp_reset_postdata();
		?>
		</div>
		
		<div class="loop-nav">
			<?php $archive = get_post(get_option('page_for_posts')); ?>
			<a href="<?php echo get_permalink($archive);?>">Всего историй <b><?php echo intval($query->found_posts); ?></b> &raquo;</a>
		</div>
	</div>

</section>

<section class="home-section home-blocks">
	<?php if(function_exists('have_rows')) { if(have_rows('home_blocks', $home_id)) { ?>
	<div class="frame">
	<?php
		while(have_rows('home_blocks', $home_id)){
			the_row();
			
			$title = get_sub_field('hb_title');
			$desc = get_sub_field('hb_desc');  
			$url = get_sub_field('hb_link');
			$url = (!empty($url)) ? esc_url($url) : $url;
			
			$img_id = get_sub_field('hb_img');
			$img = wp_get_attachment_url($img_id);			
	?>
		<div class="bit md-4 home-block"><a href="<?php echo $url;?>">		
			<div class="post-inner" style="background-image: url(<?php echo $img;?>);">	</div>
			<div class="nb-header">
				<h3><span><?php echo $title;?></span></h3>
				<div class="desc"><span><?php echo $desc;?></span></div>
			</div>
		</a></div>
	<?php } ?>
	</div>
	<?php  }} //endif ?>
</section>
<?php get_footer(); ?>
