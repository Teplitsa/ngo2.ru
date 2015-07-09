<?php
/**
 * @package bb
 */

$css = 'tpl-post mdl-card mdl-shadow--2dp invert';
if(has_term('news', 'category')) {
	$css = 'tpl-news mdl-card mdl-shadow--2dp';
}

$author = tst_get_post_author();

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('mdl-cell mdl-cell--4-col masonry-item'); ?>>
<div class="screen-reader-text"><?php _e('Article', 'tst');?></div>
<div class="<?php echo esc_attr($css);?>"><a href="<?php the_permalink();?>" class="card-link">
	
	<?php if(!has_term('news', 'category') && has_post_thumbnail()){ ?>
		<div class="mdl-card__media">
			<?php echo tst_get_post_thumbnail(null, 'thumbnail-extra'); ?>
		</div>			
	<?php } ?>
	
	<div class="mdl-card__title">
		<h4 class="mdl-card__title-text"><?php the_title();?></h4>
	</div>
	<div class="mdl-card__supporting-text entry-summary">
		<?php echo tst_get_post_excerpt(); ?>
	</div>
	
	<?php if(!has_term('news', 'category') && !empty($author)) { ?>
		<footer class="entry-author mdl-card__actions pictured-card-item">
		<?php $avatar = tst_get_author_avatar($author->term_id) ; ?>				
				
			<div class="author-avatar round-image pci-img"><?php echo $avatar;?></div>
				
			<div class="author-content card-footer-content pci-content">
				<h5 class="author-name pci-title"><?php echo apply_filters('tst_the_title', $author->name);?></h5>
				<p class="author-role pci-caption"><?php echo apply_filters('tst_the_title', $author->description);?></p>
			</div>
			
		</footer>
	<?php } ?>
</a>
</div>	
</article><!-- #post-## -->