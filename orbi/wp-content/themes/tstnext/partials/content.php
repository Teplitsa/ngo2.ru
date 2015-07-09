<?php
/**
 * @package bb
 */

$css = 'tpl-post card invert';
if(has_term('news', 'category')) {
	$css = 'tpl-news card';
}

$author = tst_get_post_author();

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('mdl-cell mdl-cell--4-col masonry-item'); ?>>
<div class="screen-reader-text"><?php _e('Article', 'tst');?></div>
<div class="<?php echo esc_attr($css);?>"><a href="<?php the_permalink();?>" class="card-link">
	
	<?php if(!has_term('news', 'category') && has_post_thumbnail()){ ?>
		<div class="card-image">
			<?php echo tst_get_post_thumbnail(null, 'thumbnail-extra'); ?>
		</div>			
	<?php } ?>
	
	<div class="card-content">
		<h4 class="entry-title"><?php the_title();?></h4>
		<div class="entry-summary"><?php echo tst_get_post_excerpt(); ?></div>
	</div>
	
	<?php if(!has_term('news', 'category') && !empty($author)) { ?>
		<footer class="entry-author card-footer pictured-card-item">
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