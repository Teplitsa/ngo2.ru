<?php
/**
 * @package bb
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post'); ?>>
<div class="screen-reader-text"><?php _e('Article', 'memo');?></div>

<div class="post-inner">
	<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>	
	<div class="entry-summary"><?php the_excerpt(); ?></div>
</div>
	
</article><!-- #post-## -->