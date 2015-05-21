<?php
/**
 * Single Event
 */

 
$post_release = (function_exists('get_field')) ? (bool)get_field('post-release') : true; 
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-event-full'); ?>>

	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		
		<?php if(is_singular()) { ?>
			<h2 class="screen-reader-text wac-go-to-hell">Полный текст публикации</h2>
		<?php } ?>	
		
	</header>
	
	<div class="frame">
		<div class="bit md-4">
			<a href="<?php the_permalink();?>" class="thumbnail-link">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'tst'), get_the_title()),
				);
				the_post_thumbnail('thumbnail', $attr);
			?>
			</a>	
		</div>
		
		<div class="bit md-8">
			<div class="entry-meta"><?php tst_event_meta(); ?></div>
		</div>
	</div>
	
		
	<div class="entry-summary"><?php the_excerpt();?></div>
	
	<div class="entry-content"><?php the_content(); ?></div><!-- .entry-content -->
	
	<?php if(!empty($post_release)) { ?>
	<div class="entry-content post-release">
		<h3>Как это было</h3>
		<?php echo apply_filters('tst_the_content', $post_release);?>
	</div>
	<?php } ?>
	
	
	<!-- gallery -->
	<?php if(function_exists('have_rows')) { if(have_rows('event-partners')) { ?>
	<div class="entry-partners">
		<h3>Партнеры мероприятия</h3>
		
		<ul class="partners-gallery frame">
		<?php
			while(have_rows('event-partners')){
				the_row();
				
				$title = get_sub_field('event-partner-name');  
				$url = get_sub_field('event-partner-website');
				$url = (!empty($url)) ? esc_url($url) : $url;
				$logo_id = get_sub_field('event-partner-logo');
				$logo = wp_get_attachment_image($logo_id, 'full', false, array('alt' => $title));
			?>	
			<div class="logo bit mf-6 md-3"><div class="logo-frame">			
				<a class="logo-link" title="<?php echo esc_attr($title);?>" href="<?php echo $url;?>"><?php echo $logo ;?></a>
			</div></div>
		<?php } ?>
		</ul>		
	</div>
	<?php  }} //endif ?>
		
</article><!-- #post-## -->

