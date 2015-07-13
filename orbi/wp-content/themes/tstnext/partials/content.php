<?php
/**
 * @package bb
 */

$css = 'tpl-post mdl-card mdl-shadow--2dp invert';
if(has_term('news', 'category')) {
	$css = 'tpl-news mdl-card mdl-shadow--2dp';
}



?>

<article id="post-<?php the_ID(); ?>" <?php post_class('mdl-cell mdl-cell--4-col masonry-item'); ?>>
<div class="screen-reader-text"><?php _e('Article', 'tst');?></div>
<div class="<?php echo esc_attr($css);?>">
	
	<?php tst_post_card_content();?>


</div>	
</article><!-- #post-## -->