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
			<?php the_post_thumbnail('full');?>
		</div>
		
		<div class="bit md-7">
			<div class="entry-content"><?php the_content();?></div>
		</div>
	</div>
	
	<footer class="document-footer">
		<div class="frame">
			<div class="bit sm-7 md-9">
				<span class="pubdate"><i class="fa fa-calendar"></i> <time><?php the_date();?></time></span>
				<?php echo get_the_term_list(get_the_ID(), 'post_tag', ' <span class="tags"><i class="fa fa-tags"></i>', ', ', '</span>'); ?>
			</div>
			<div class="bit sm-5 md-3"><?php memo_post_nav(); ?></div>
		</div>
		
	</footer>
	
</article>


