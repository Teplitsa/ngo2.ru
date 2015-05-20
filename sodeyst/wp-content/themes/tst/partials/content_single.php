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
	
	<div class="entry-meta"><?php tst_event_meta(); ?></div>
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
		
	<?php
		$pt = get_post_type();
		if($pt == 'post') {
	?>
		<footer class="entry-footer">
		<time class="date"><?php echo esc_html(get_the_date());?></time>
		<?php
			$sep = tst_get_sep();
			echo get_the_term_list(get_the_ID(), 'category', $sep.' <span class="category">', ', ', '</span>');
		?>
		</footer>
	<?php } ?>
	
</article><!-- #post-## -->

