<?php
/**
 * @package alv
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post'); ?>>
<div class="screen-reader-text"><?php _e('Article', 'alv');?></div>
	
	<div class="frame">
		<div class="bit md-6">
			<a href="<?php the_permalink();?>" class="thumbnail-link">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'alv'), get_the_title()),				
				);
				the_post_thumbnail('post-thumbnail', $attr);
			?>
			</a>	
		</div>
		
		<div class="bit md-6">
			<div class="entry-summary">
				<a href="<?php the_permalink();?>" class="excerpt-link"><?php the_excerpt(); ?></a>
			</div>
			<div class="entry-meta">
				<?php alv_posted_on(); ?>
			</div>
		</div>
	</div>
	
	<header class="entry-header">
		<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>			
	</header>
	
</article><!-- #post-## -->