<?php
/**
 * Marker page
 **/


global $post;

$history = (function_exists('get_field')) ? get_field('history_photo', get_the_ID()) : ''; 
$history_url = wp_get_attachment_url($history);
$history_img = wp_get_attachment_image($history, 'embed'); 

$modern = (function_exists('get_field')) ? get_field('modern_photo', get_the_ID()) : '';;
$modern_img = wp_get_attachment_image($modern, 'embed');
$modern_url = wp_get_attachment_url($modern);

$gallery_ref = uniqid('gallery-');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-marker-full'); ?>>

	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		
		<?php if(is_singular()) { ?>
			<h2 class="screen-reader-text wac-go-to-hell">Полный текст публикации</h2>
		<?php } ?>			
	</header>
		
	<div class="entry-media">
		<div class="frame">
			<div class="bit sm-6">
				<a data-fresco-group='<?php echo esc_attr($gallery_ref);?>' href='<?php echo $history_url;?>' rel='image-overlay' class='img-padder fresco'>
					<?php echo $history_img;?>
				</a>
			</div>
			<div class="bit sm-6">
				<a data-fresco-group='<?php echo esc_attr($gallery_ref);?>' href='<?php echo $modern_url;?>' rel='image-overlay' class='img-padder fresco'>
					<?php echo $modern_img;?>
				</a>
			</div>
		</div>
	</div>
	
	<div class="entry-columns">
		<div class="frame">
			<div class="bit sm-6 md-8">
				<div class="entry-content"><?php the_content();?></div>
			</div>
			<div class="bit sm-6 md-4">
				<div class="entry-summary"><?php dynamic_sidebar( 'story-sidebar' ); ?></div>
			</div>
		</div>
	</div>
	
	<footer class="post-footer single-footer">
		<div class="frame">
			<div class="bit sm-7 md-9">
				<span class="pubdate"><i class="fa fa-calendar"></i> <time><?php the_date();?></time></span>				
			</div>
			<div class="bit sm-5 md-3"><?php memo_post_nav(); ?></div>
		</div>
		
	</footer>
	
</article>