<?php
/**
 * @package bb
 */

$show_thumb = (function_exists('get_field')) ? (bool)get_field('show_thumbnail') : true;

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post-full'); ?>>

	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		
		<?php if(is_singular()) { ?>
			<h2 class="screen-reader-text wac-go-to-hell">Полный текст публикации</h2>
		<?php } ?>	
		
	</header>
		
	<div class="entry-summary"><?php the_excerpt();?></div>
	
	<?php if($show_thumb) { ?>
		<div class="entry-media">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'tst'), get_the_title()),
				);
				the_post_thumbnail('embed', $attr);
			?>
		</div>
	<?php } ?>
	
	<div class="entry-content"><?php the_content(); ?></div><!-- .entry-content -->
	
	<footer class="entry-footer">
		<div class="frame">
			<div class="bit sm-7 md-9">
				<span class="pubdate"><time><?php the_date();?></time></span>
				<?php echo get_the_term_list(get_the_ID(), 'category', ' <span class="category">', ', ', '</span>'); ?>
			</div>
			<div class="bit sm-5 md-3"><?php tst_post_nav(); ?></div>
		</div>
	</footer>
	
</article><!-- #post-## -->

