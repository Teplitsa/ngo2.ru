<?php
/**
 * Related products template
 **/

global $post;


$r_query = new WP_Query(
	array(
		'post_type' => 'product',
		'posts_per_page' => 4,
		'orderby' => 'rand',
		'post__not_in' => array($post->ID)
	)
);
?>
<aside class="related-posts ev-future section">	
	<h5>Еще товары</h5>
	
	<?php
		while($r_query->have_posts()){
			$r_query->the_post();		
			tst_compact_product_item();		
		}
		wp_reset_postdata();	
	?>
</aside>
