<?php
/**
 **/

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-leyka-full'); ?>>

	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		
		<?php if(is_singular()) { ?>
			<h2 class="screen-reader-text wac-go-to-hell">Полный текст публикации</h2>
		<?php } ?>	
		
	</header>
	
	
	
	<div class="frame content-section">
		<div class="bit md-8">
			<div class="entry-content"><?php the_content();?></div>
		</div>
		
		<div class="bit md-4">
			<div class="column-inner side">
				
				<div class="widget">
					<h3 class="widget-title">Мы благодарим</h3>
					<div class="widget-content"><?php echo do_shortcode('[leyka_donors_list num="25"]')?></div>
				</div>
				
			</div>
		</div>
	</div>
	

	
</article><!-- #post-## -->

