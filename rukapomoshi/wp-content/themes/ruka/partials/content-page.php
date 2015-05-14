<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package bb
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-page'); ?>>
	
	<?php if(is_help()) { ?>
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>
	<?php } ?>
	<div class="entry-content">
		<?php the_content(); ?>		
	</div>
	
</article><!-- #post-## -->
