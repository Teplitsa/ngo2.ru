<?php
/**
 * Related posts template
 **/

global $post;

$r_query = frl_get_related_query($post, 'category', 3); 
if($r_query->have_posts()){
	
	$cat = wp_get_object_terms($post->ID, 'category');
	
	$aside_title = ''; 
	if(function_exists('get_field') && isset($cat[0])){
		$aside_title = get_field('related_post_title', 'category_'.$cat[0]->term_id);
	}
	
	if(empty($aside_title) && isset($cat[0])) {
		$aside_title = 'Еще '.$cat[0]->name;
	}
	elseif(empty($aside_title)) {
		$aside_title = __('More posts', 'tst'); 
	}
?>
<aside class="related-posts section">
	
	<div class="mdl-grid">
		<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
		<div class="mdl-cell mdl-cell--6-col mdl-cell--5-col-tablet">
			<h5><?php echo apply_filters('tst_the_title', $aside_title);?></h5>
	
		<?php
			while($r_query->have_posts()){
				$r_query->the_post();
				
				if(has_term('news', 'category')){
					tst_compact_news_item();
				}
				else {
					tst_compact_post_item();
				}
				
			}
			wp_reset_postdata();	
		?>	
		</div>
		<div class="mdl-cell mdl-cell--3-col "></div>
		
	</div><!-- .row -->
	
</aside>
<?php  } ?>