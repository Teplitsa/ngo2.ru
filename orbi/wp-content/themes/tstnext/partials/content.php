<?php
/**
 * @package bb
 */

$css = 'tpl-post card invert';
if(has_term('news', 'category')) {
	$css = 'tpl-news card';
}

$author = wp_get_object_terms(get_the_ID(), 'auctor');
if(!empty($author))
	$author = $author[0];

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('col sm-6 lg-4'); ?>>
<div class="screen-reader-text"><?php _e('Article', 'tst');?></div>
<div class="<?php echo esc_attr($css);?>">
	
	<?php if(!has_term('news', 'category') && has_post_thumbnail()){ ?>
		<div class="card-image">
			<a href="<?php the_permalink();?>" class="thumbnail-link"><?php echo tst_get_post_thumbnail(null, 'thumbnail-extra'); ?></a>
		</div>			
	<?php } ?>
	
	<div class="card-content">
		<h4 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
		<div class="entry-summary"><?php echo tst_get_post_excerpt(); ?></div>
	</div>
	
	<?php if(!has_term('news', 'category') && !empty($author)) { ?>
		<footer class="entry-author card-footer">
		<?php
			$avatar = (function_exists('get_field')) ? get_field('auctor_photo', 'auctor_'.$author->term_id) : '';
			if(empty($avatar)){
				$avatar = tst_get_default_author_avatar();
			}
			else {
				$avatar = wp_get_attachment_image($avatar);
			}
		?>
			<div class="row">				
				<div class="col sm-2">
					<a href="<?php echo get_term_link($author);?>" class="author-avatar round-image"><?php echo $avatar;?></a>
				</div>
				<div class="col sm-10">
					<h5 class="author-name"><a href="<?php echo get_term_link($author);?>"><?php echo apply_filters('tst_the_title', $author->name);?></a></h5>
					<p class="author-role"><?php echo apply_filters('tst_the_title', $author->description);?></p>
				</div>
			</div>
		</footer>
	<?php } ?>

</div>	
</article><!-- #post-## -->