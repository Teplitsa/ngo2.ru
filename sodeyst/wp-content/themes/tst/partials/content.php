<?php
/**
 * @package bb
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post'); ?>>
<div class="screen-reader-text"><?php _e('Article', 'tst');?></div>
	
	<div class="frame">
		<div class="bit md-4">
			<a href="<?php the_permalink();?>" class="thumbnail-link">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'tst'), get_the_title()),
				);
				the_post_thumbnail('thumbnail', $attr);
			?>
			</a>	
		</div>
		
		<div class="bit md-8">
			<header class="entry-header">
				<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>	
				<div class="entry-meta"><?php tst_posted_on(); ?></div>
			</header>
			
			<div class="entry-summary"><?php the_excerpt(); ?></div>	
		</div>
	</div>
	
</article><!-- #post-## -->