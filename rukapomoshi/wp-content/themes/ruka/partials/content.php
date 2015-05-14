<?php
/**
 * @package bb
 */


?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post bit md-6'); ?>>
<div class="screen-reader-text"><?php _e('Article', 'ruka');?></div>
<div class="post-frame">
<?php if(has_category('digests')) { ?>

	<div class="frame">
		<div class="bit mf-8">
			<a href="<?php the_permalink();?>" class="thumbnail-link">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'ruka'), get_the_title()),
				);
				the_post_thumbnail('long', $attr);
			?>
			</a>				
		</div>
		<div class="bit mf-4">
			<div class="entry-meta">
				<span class="issue">Дайджест</span>
				<time><?php echo get_the_date();?></time>
			</div>
		</div>
	</div>

<?php } else { ?>

	<div class="entry-meta"><time><?php echo get_the_date();?></time></div>
	<a href="<?php the_permalink();?>" class="thumbnail-link">
	<?php
		$attr = array(
			'alt' => sprintf(__('Thumbnail for - %s', 'ruka'), get_the_title()),
		);
		the_post_thumbnail('long', $attr);
	?>
	</a>	

<?php } ?>
	
	<header class="entry-header">
		<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>			
	</header>	
	
	<div class="entry-summary"><?php the_excerpt(); ?></div>	
	
</div>	
</article><!-- #post-## -->