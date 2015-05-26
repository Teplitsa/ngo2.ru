<?php
/**
 * Book
 */
$author = (function_exists('get_field')) ? get_field('auctor') : '';

//@to-do: make it file of exernal link 
$read_link = '';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-book-full'); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		
		<?php if(is_singular()) { ?>
			<h2 class="screen-reader-text wac-go-to-hell">Полный текст публикации</h2>
		<?php } ?>	
		<div class="entry-meta"><?php echo apply_filters('memo_the_title', $author); ?></div>
	</header>
	
	<div class="frame">
		<div class="bit sm-6 md-3">
			<!-- img / gallery -->
			<?php the_post_thumbnail('portrait');?>
		</div>
		
		<div class="bit sm-6 md-9">
			<div class="entry-summary"><?php the_excerpt();?></div>
			<div class="entry-content"><?php the_content();?></div>
			
			<div class="download-link"><?php echo memo_book_read_link(get_the_ID());?></div>
		</div>
	</div>
	
	<footer class="document-footer single-footer">
		<div class="frame">
			<div class="bit sm-7 md-9">
				<span class="pubdate"><i class="fa fa-calendar"></i> <time><?php the_date();?></time></span>
				<?php echo get_the_term_list(get_the_ID(), 'post_tag', ' <span class="tags"><i class="fa fa-tags"></i>', ', ', '</span>'); ?>
			</div>
			<div class="bit sm-5 md-3"><?php memo_post_nav(); ?></div>
		</div>
		
	</footer>
	
</article>


