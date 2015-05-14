<?php
/**
 * Single event
 */

 
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('full-post'); ?>>
	
	<div class="frame">
		<div class="entry-preview bit-6"><?php echo the_post_thumbnail();?></div>
		
		
		<div class="entry-meta bit-6">
			<?php tst_event_undertitle_meta();?>
			<?php tst_event_contacts_block();?>
		</div>
	</div>
	
	<div class="entry-content">
		
		<?php the_content(); ?>		
		<?php tst_event_map_block();?>		
		
	</div>
			
	<footer class="post-footer">	

	<?php
		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '',', ');
		if ( $tags_list ) :
	?>
		<span class="tags-links">
			<?php printf(__('Tags: %1$s', 'tst'), $tags_list ); ?>
		</span>
	<?php endif; // End if $tags_list ?>
		
	</footer><!-- .entry-meta -->
	
	
</article><!-- #post-## -->
