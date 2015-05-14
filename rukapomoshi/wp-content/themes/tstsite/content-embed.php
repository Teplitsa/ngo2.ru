<?php
/**
 * Post embeded through frl_query
 **/

if(!is_front_page()):
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('embed-item'); ?>>
		
	
	<div class="entry-meta"><?php tst_posted_on(); ?></div>
	<h4 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h4>		
		
	<div class="item-summary">			
		<?php the_excerpt(); ?>
	</div>
	
</article><!-- #post-## -->

<?php else: ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('grid-item bit-3'); ?>>
	
	<div class="item-preview"><?php the_post_thumbnail();?></div>
	<h4 class="item-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h4>	
	<div class="item-metadata"><?php tst_posted_on(); ?></div>	
</article><!-- #post-## -->


<?php endif; ?>