<?php
/**
 * Template Name: Stories
 **/

global $post, $wp_query;


$query = new WP_Query(array(
	'post_type' => 'post',
	'orderby' => 'rand'		
));

$archive = get_post(get_option('page_for_posts'));



get_header(); ?>

<header class="section-header">
	<h1 class="section-title"><?php echo get_the_title($post);?></h1>
	<div class="all-link">
		<a href="<?php echo get_permalink($archive);?>">Всего историй <b><?php echo intval($query->found_posts); ?></b> &raquo;</a>
	</div>
</header>

<div class="loop-holder card-board">
	<div class="frame">
	<?php
		memo_tags_widget();

		while($query->have_posts() ) {
			$query->the_post();
			
			//to-do add tags widget
			get_template_part('partials/content', 'story');
		}
		wp_reset_postdata();
	?>
	</div>
	
	<div class="loop-nav">
		<a href="<?php echo get_permalink($archive);?>">Всего историй <b><?php echo intval($query->found_posts); ?></b> &raquo;</a>
	</div>
</div>

<?php get_footer(); ?>