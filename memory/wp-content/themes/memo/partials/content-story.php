<?php
/** Story template **/

$css = (is_singular()) ? ' bit sm-6 md-4' : '';
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-story'.$css); ?>>
<div class="screen-reader-text"><?php _e('Article', 'memo');?></div>

<div class="post-inner">
<div class="frame">
	<div class="bit sm-5">
		<a href="<?php the_permalink();?>" class="thumbnail-link">
		<?php
			$attr = array(
				'alt' => sprintf(__('Thumbnail for - %s', 'memo'), get_the_title()),
			);
			the_post_thumbnail('portrait', $attr);
		?>
		</a>	
	</div>
	
	<div class="bit sm-7">
		<header class="entry-header">
			<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>	
		</header>
		<div class="entry-summary"><?php the_excerpt(); ?></div>	
	</div>
</div>
	
<?php echo get_the_term_list(get_the_ID(), 'place', '<div class="tags"><i class="fa fa-map-marker"></i>', ', ', '</div>'); ?>
</div>

</article>