<?php
/**
 * Event in Loop
 **/

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-event'); ?>>
<div class="screen-reader-text"><?php _e('Event', 'tst');?></div>
	
<div class="frame">
	<div class="bit sm-4">
		<a href="<?php the_permalink();?>" class="thumbnail-link">
		<?php
			$attr = array('alt' => sprintf(__('Thumbnail for - %s', 'tst'), get_the_title()));
			the_post_thumbnail('thumbnail', $attr);
		?>
		</a>	
	</div>
	
	<div class="bit sm-8">
		<header class="entry-header">
			<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>	
			<div class="entry-meta"><?php tst_event_meta(); ?></div>
		</header>
		
		<div class="entry-summary"><?php the_excerpt(); ?></div>	
	</div>
</div>	
	
</article><!-- #post-## -->