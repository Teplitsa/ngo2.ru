<?php
/**
 * Template Name: Events
 **/

global $post;

$page_id = $post->ID;

get_header(); ?>

<?php get_template_part('partials/title', 'section');?>

<div class="frame">
	
	<div id="primary" class="content-area bit md-9">
		<main id="main" class="site-main" role="main">
			
		<?php //slider
			$slider = (function_exists('get_field')) ? get_field('event_slider', $page_id) : '';
			if(!empty($slider)) {
		?>
		<section class="events-slider"><?php echo do_shortcode($slider); ?></section>
		<?php } ?>
		
		<?php if(!empty($post->post_content)) { ?>
		<section class="events-content">
			<div class="entry-content"><?php
				while(have_posts()){
					the_post();
					the_content();
				}				
			?>
			</div>
		</section>
		<?php } ?>
		
		<?php //events gallery
			$gallery = (function_exists('get_field')) ? get_field('featured_events', $page_id) : array();
			if(!empty($gallery)) {
		?>
		<section class="events-gallery">
			<h3>Мы провели <span>/ <a href="<?php echo get_post_type_archive_link('event');?>">Архив событий</a></span></h3>
			<div class="frame">
			<?php foreach($gallery as $ep) { ?> 
				<div class="bit sm-6 tpl-event-in-gallery">
				<div class="event-inner">				
					<a href="<?php echo get_permalink($ep);?>" class="thumbnail-link">
						<?php echo get_the_post_thumbnail($ep->ID);?>
						<time><?php echo tst_event_date($ep);?></time>
					</a>					
					<h4 class="entry-title"><a href="<?php echo get_permalink($ep);?>"><?php echo get_the_title($ep);?></a></h4>
				</div>
				</div>	
			<?php } ?>
			</div>
		</section>
		<?php  }?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>

</div>
<?php get_footer(); ?>
