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
	
	<div class="frame">
		<div class="bit md-4 home-block">
			<div class="post-inner">
				
			</div>
		</div>
	</div>
</section>
<?php get_footer(); ?>
