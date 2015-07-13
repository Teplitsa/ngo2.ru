<?php
/**
 * Related products template
 **/

global $post;

$today = strtotime(sprintf('now %s hours', get_option('gmt_offset'))); 
$r_query = new WP_Query(
	array(
		'post_type' => 'event',
		'posts_per_page' => 4,
		'orderby' => 'rand',
		'meta_query' => array(
			array(
				'key' => 'event_date',
				'value' => date('Y', $today).date('m', $today).date('d', $today),
				'compare' => '>=',
			),
		)
	)
);
?>
<aside class="related-posts ev-future section">	
	<h5>Еще товары</h5>
	
	<?php
		while($r_query->have_posts()){
			$r_query->the_post();		
			tst_compact_event_item();		
		}
		wp_reset_postdata();	
	?>
</aside>
