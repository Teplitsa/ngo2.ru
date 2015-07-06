<?php
/**
 * @package bb
 */

$show_thumb = (function_exists('get_field')) ? (bool)get_field('show_thumbnail') : true;

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post-full'); ?>>

	<div class="entry-meta">
		meta
	</div>
		
	<div class="entry-summary"><?php the_excerpt();?></div>
	<div class="sharing-on-top"><?php tst_social_share();?></div>
	
	<?php if($show_thumb && has_post_thumbnail()) { ?>
		<div class="entry-media">
			<?php echo tst_get_post_thumbnail(null, 'embed'); ?>
		</div>
	<?php } ?>
	
	<div class="entry-content"><?php the_content(); ?></div>
		
	
	<div class="entry-footer">
		<div class="sharing-on-bottom"><?php tst_social_share();?></div>
		
	<?php
		$author = tst_get_post_author();
		if(!empty($author)) {
			
			$avatar = tst_get_author_avatar($author->term_id) ;
	?>
		<div class="entry-author">
			<div class="author-avatar round-image card-img"><?php echo $avatar;?></div>
			
			<div class="author-content">
				<h5 class="author-name"><?php echo apply_filters('tst_the_title', $author->name);?></h5>
				<p class="author-role"><?php echo apply_filters('tst_the_title', $author->description);?></p>
			</div>
		</div>
	<?php } ?>	
		
		
	</div>
	
	
</article><!-- #post-## -->

