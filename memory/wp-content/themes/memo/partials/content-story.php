<?php
/** Story template **/

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-story card'.(is_singular() ? ' bit md-4' : '')); ?>>
<div class="screen-reader-text"><?php _e('Article', 'memo');?></div>

<div class="post-inner">
	<div class="card-content">
		<div class="frame">
			<div class="bit mf-4 md-5">
				<a href="<?php the_permalink();?>" class="thumbnail-link">
				<?php the_post_thumbnail('portrait', array('alt' => sprintf(__('Thumbnail for - %s', 'memo'), get_the_title()),));?>
				</a>
			</div>

			<div class="bit mf-8 md-7">
				<header class="entry-header">
					<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>	
				</header>
				<div class="entry-summary"><?php the_excerpt();?></div>
			</div>
		</div><!-- .frame -->
	</div>
	<?php echo get_the_term_list(get_the_ID(), 'place', '<div class="tags"><i class="fa fa-map-marker"></i>', ', ', '</div>'); ?>
</div>

</article>