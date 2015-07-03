<?php
/**
 * Related posts template
 **/

global $post;

$r_query = frl_get_related_query($post, 'post_tag', 3); 
if($r_query->have_posts()){  
?>
<aside class="related-posts section">
	<h5><?php _e('Related posts', 'tst');?></h5>
	
	<div class="row">
<?php
	while($r_query->have_posts()){
		$r_query->the_post();
	?>
		<div class="col s12 m4">
			<div class="tpl-related-post">
				<div class="row">
					<div class="col s6 m12"><a href="<?php the_permalink();?>" class="thumbnail-link"><?php echo tst_get_post_thumbnail(); ?></a></div>
					<div class="col s6 m12">
						<h6 class="rp-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h6>
						<div class="rp-meta"><?php echo tst_posted_on(); ?></div>
					</div>
				</div>
				
			</div>
		</div>
	<?php
	}
	wp_reset_postdata();	
?>	
	</div>	
</aside>
<?php  } ?>