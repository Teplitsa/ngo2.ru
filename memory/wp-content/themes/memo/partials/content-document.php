<?php

/** Document template **/

$css = (is_singular()) ? ' bit sm-6 md-4' : '';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-document'.$css); ?>>
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
	<?php echo get_the_term_list(get_the_ID(), 'post_tag', '<div class="tags"><i class="fa fa-tags"></i>', ', ', '</div>'); ?>

</div>

</article><!-- #post-## -->