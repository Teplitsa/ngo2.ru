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
<div class="<?php echo esc_attr($css);?>">
	
	<?php if(has_post_thumbnail()){ ?>
		<div class="mdl-card__media">
			<?php echo tst_get_post_thumbnail(null, 'thumbnail-extra'); ?>
		</div>			
	<?php } ?>
	
	<?php if(!empty($author)) { ?>
		<div class="entry-author mdl-card__supporting-text pictured-card-item">
		<?php $avatar = tst_get_author_avatar($author->term_id) ; ?>				
				
			<div class="author-avatar round-image pci-img"><?php echo $avatar;?></div>
				
			<div class="author-content card-footer-content pci-content">
				<h5 class="author-name mdl-typography--body-1"><?php echo apply_filters('tst_the_title', $author->name);?></h5>
				<p class="author-role mdl-typography--caption"><?php echo apply_filters('tst_the_title', $author->description);?></p>
			</div>
			
		</div>
	<?php } ?>
	
	<div class="mdl-card__title">
		<h4 class="mdl-card__title-text"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
	</div>
	
	<?php echo tst_card_summary(); ?>
	<div class="mdl-card--expand"></div>
	<div class="mdl-card__actions mdl-card--border">
		<a href="<?php the_permalink();?>" class="mdl-button mdl-js-button">Подробнее</a>
	</div>
	
	
</a>
</div>	
</article><!-- #post-## -->