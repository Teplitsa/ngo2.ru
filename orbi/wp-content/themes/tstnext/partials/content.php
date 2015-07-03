<?php
/**
 * @package bb
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post'); ?>>
<div class="screen-reader-text"><?php _e('Article', 'tst');?></div>
	
	<div class="row">
		
		<div class="col s12 m7 l6">
			<a href="<?php the_permalink();?>" class="thumbnail-link">
				<?php echo tst_get_post_thumbnail(); ?>
			</a>	
		</div>
		
		<div class="col s12 m5 l6">
			<header class="entry-header">
				<h4 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>	
				<div class="entry-meta"><?php echo tst_posted_on(); ?></div>
			</header>
			
		</div>
		
	</div>
	
	<div class="row">
		<div class="entry-summary col sm12 m10 offset-m1"><?php the_excerpt(); ?></div>	
	</div>
	
</article><!-- #post-## -->