<?php
/** Related posts **/

global $post;

$h3 = (has_category('digests', $post)) ? 'Еще выпуски' : "Еще новости";
$more = (has_category('digests', $post)) ? 'Все выпуски' : "Все новости";
$more_url = get_permalink(get_option('page_for_posts'));
$cats = wp_get_object_terms($post->ID, 'category', array('fields' => 'ids'));

$args = array(
	'post_type'      => 'post',
	'posts_per_page' => 3,
	'post__not_in'   => array($post->ID),
	'tax_query'      => array(
		array(
			'taxonomy' => 'category',
			'field' => 'id',
			'terms' => $cats
		)
	)
);

$query = new WP_Query($args);
if(!$query->have_posts())
	return;

?>
<footer class="related-items">
	<h3><?php echo $h3;?></h3>
	
	<div class="related-list">
	<div class="frame">
	<?php
		while($query->have_posts()){
			$query->the_post();
		?>
		<article class="related-item bit sm-6 md-4">
			<a href="<?php the_permalink();?>" class="thumbnail-link">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'ruka'), get_the_title()),
				);
				the_post_thumbnail('long', $attr);
			?>
			</a>
			<h4 class="item-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>		
		</article>
		<?php	
		}
		wp_reset_postdata();
	?>	
	</div>
	</div>
	<div class="more"><a href="<?php echo esc_url($more_url);?>"><?php echo $more;?></a></div>
</footer>