<?php

/** Document template **/

$css = ' bit md-4';

$author = (function_exists('get_field')) ? get_field('auctor') : '';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-book card'.$css); ?>>
<div class="screen-reader-text"><?php _e('Book', 'memo');?></div>

<div class="post-inner">
	
	<div class="card-content">
		<div class="frame">
			<div class="bit mf-4">
				<a href="<?php the_permalink();?>" class="thumbnail-link">
		<?php
					$attr = array(
						'alt' => sprintf(__('Cover for - %s', 'memo'), get_the_title()),
					);
					the_post_thumbnail('portrait', $attr);
				?>
				</a>
			</div>
			<div class="bit mf-8">
				<header class="entry-header">
					<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
					<div class="entry-meta"><?php echo apply_filters('memo_the_title', $author); ?></div>
				</header>
				<div class="entry-summary"><?php the_excerpt(); ?></div>
			</div>
		</div>
	</div>
	<?php echo get_the_term_list(get_the_ID(), 'post_tag', '<div class="tags"><i class="fa fa-tags"></i>', ', ', '</div>'); ?>

</div>

</article><!-- #post-## -->