<?php
/**
 * @package alv
 */

$show_thumb = (function_exists('get_field')) ? (bool)get_field('show_thumbnail') : false;
$show_thumb = true;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post-full'); ?>>

	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		
		<?php if(is_singular()) { ?>
			<h2 class="screen-reader-text wac-go-to-hell">Полный текст публикации</h2>
		<?php } ?>
		
		<div class="entry-meta"><?php alv_posted_on(); ?></div>
	</header>

	<div class="entry-content">
		<div class="frame">
			<div class="bit md-6">
				<?php if($show_thumb) { ?>
					<div class="entry-media"><?php echo alv_get_post_thumbnail(null, 'embed');?></div>
				<?php  } ?>
				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div>
			</div>
			
			<div class="bit md-6">
				<div class="entry-content">
					<?php the_content(); ?>
				</div>			
			</div>
		</div>
	</div><!-- .entry-content -->
	
</article><!-- #post-## -->
?>
