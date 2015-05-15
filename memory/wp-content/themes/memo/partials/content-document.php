<?php

/** Document template **/
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-document'); ?>>
<div class="screen-reader-text"><?php _e('Document', 'memo');?></div>

<div class="post-inner">
	<a href="<?php the_permalink();?>" class="thumbnail-link">
	<?php
		$attr = array(
			'alt' => sprintf(__('Thumbnail for - %s', 'memo'), get_the_title()),
		);
		the_post_thumbnail('post-thumbnail', $attr);
	?>
	</a>
	
	<header class="entry-header">
		<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>	
	</header>
	
	<div class="entry-summary"><?php the_excerpt(); ?></div>	
</div>

</article><!-- #post-## -->