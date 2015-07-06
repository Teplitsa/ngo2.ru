<?php
/**
 * @package bb
 */

$show_thumb = (function_exists('get_field')) ? (bool)get_field('show_thumb') : true;

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post-full'); ?>>

	<div class="entry-meta">
		<div class="row">
			<div class="col sm-4">
				<div class="captioned-text">
					<div class="caption"><?php _e('Author', 'tst');?></div>
					<div class="text"><?php echo get_the_term_list(get_the_ID(), 'auctor', '', ', ', '');?></div>
				</div>
			</div>
			<div class="col sm-8">
				<div class="captioned-text">
					<div class="caption"><?php _e('Published', 'tst');?></div>
					<div class="text"><?php echo tst_posted_on();?></div>
				</div>
			</div>
		</div>
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
		<div class="row">
			
			<div class="col sm-8">
				<div class="entry-author pictured-card-item">
					<div class="author-avatar round-image pci-img"><?php echo $avatar;?></div>
					
					<div class="author-content pci-content">
						<h5 class="author-name pci-title"><?php echo apply_filters('tst_the_title', $author->name);?></h5>
						<p class="author-role pci-caption"><?php echo apply_filters('tst_the_title', $author->description);?></p>
					</div>
				</div>
			</div>
			
			<div class="col sm-4">
				<a href="<?php echo get_term_link($author);?>" class="author-link"><?php _e('All author\'s articles', 'tst');?></a>
			</div>
			
		</div>
		
	<?php } ?>		
		
	</div>
	
	
</article><!-- #post-## -->

