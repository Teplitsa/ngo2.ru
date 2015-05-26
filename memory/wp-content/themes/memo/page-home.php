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
			$img = wp_get_attachment_image($img_id, 'post-thumbnail');			
	?>
		<div class="bit md-4 home-block">
			<div class="post-inner">
				<a href="<?php echo $url;?>">
					<div class="hb-image"><?php echo $img;?></div>
					<div class="nb-header">
						<h3><?php echo $title;?></h3>
						<div class="desc"><?php echo $desc;?></div>
					</div>
				</a>				
			</div>
		</div>
	<?php } ?>
	</div>
	<?php  }} //endif ?>
</section>
<?php get_footer(); ?>
