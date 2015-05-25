<?php
/**
 * @package bb
 */

$show_thumb = (function_exists('get_field')) ? (bool)get_field('show_thumbnail') : true;
if(is_singular('leyka_campaign')){
	$show_thumb = false;
}
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
					'alt' => sprintf(__('Thumbnail for - %s', 'ruka'), get_the_title()),
				);
				the_post_thumbnail('embed', $attr);
			?>
		</div>
	<?php } ?>
	
	<div class="entry-content"><?php the_content(); ?></div><!-- .entry-content -->
	
	<?php
		if(get_post_type() == 'post') {
			get_template_part( 'partials/footer', 'related' );
		}
	?>
	
	
</article><!-- #post-## -->

