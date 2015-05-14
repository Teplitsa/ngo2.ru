<?php
/**
 * Event in Loop
 **/

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-event'); ?>>
<div class="screen-reader-text"><?php _e('Event', 'step');?></div>
	
	<div class="frame">
		<div class="bit md-7">
			<a href="<?php the_permalink();?>" class="thumbnail-link">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'step'), get_the_title()),
				);
				the_post_thumbnail('long', $attr);
			?>
			</a>	
		</div>
		
		<div class="bit md-5">
			<header class="entry-header">
				<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>	
				<div class="entry-meta"><?php step_event_meta(); ?></div>
			</header>
		</div>
	</div>
	
	<div class="entry-summary"><?php the_excerpt(); ?></div>	
	
	
</article><!-- #post-## -->