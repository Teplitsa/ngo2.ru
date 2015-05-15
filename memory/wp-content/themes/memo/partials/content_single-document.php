<?php
/**
 *
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-document-full'); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		
		<?php if(is_singular()) { ?>
			<h2 class="screen-reader-text wac-go-to-hell">Полный текст публикации</h2>
		<?php } ?>	
		
	</header>
	
	<div class="frame">
		<div class="bit md-5">
			<!-- img / gallery -->
			<?php the_post_thumbnail();?>
		</div>
		
		<div class="bit md-7">
			<?php the_content();?>
		</div>
	</div>
	
	<footer class="document-footer">
		
	</footer>
	
</article>