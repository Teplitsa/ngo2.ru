<?php
/**
 * The template for displaying all single posts.
 *
 * @package bb
 */

global $post;

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>	

<div class="complex-single content-area">
	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part('partials/content_single', get_post_type()); ?>

	<?php endwhile; // end of the loop. ?>
		
<?php
	//related
	$num = 6;
	$tax = (is_singular('post')) ? 'place' : 'post_tag';
	$r_query = frl_get_related_query($post, $tax, $num);
		
	if($r_query && $r_query->have_posts()){
?>
	<aside class="related-posts entry-relations">
		<h3 class="aside-title"><?php echo (is_singular('post')) ? 'Еще истории' : 'Еще материалы';?></h3>
	<?php
		echo "<div class='frame'>";
			
		while($r_query->have_posts()){
			$r_query->the_post();
			
			if(is_singular('post')){
				get_template_part('partials/content', 'story');
			}
			else {
				get_template_part('partials/content', get_post_type());
			}
			
		}
		wp_reset_postdata();
		
		echo "</div>";
	?>
	</aside>
<?php } ?>
</div>

<?php get_footer(); ?>
