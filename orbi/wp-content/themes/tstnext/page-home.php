<?php
/**
 * Template Name: Homepage
 * 
 */

global $post; 
 
$home_id = $post->ID;
$dnd = array();
$today = strtotime(sprintf('now %s hours', get_option('gmt_offset')));


//featured event
$f_event = (function_exists('get_field')) ? get_field('featured_event', $home_id) : 0; 
if($f_event > 0){
	$dnd[] = $f_event;
}

$f_event = get_post($f_event);


//featured articles
$f_post = new WP_Query(array(
	'post_type' => 'post',
	'posts_per_page' => 1,
	'tax_query' => array(
		array(
			'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => array('news'),
			'operator' => 'NOT IN'
		)
	)
));
$dnd[] = (isset($f_post->posts[0])) ? $f_post->posts[0]->ID : 0;


// posts
$blog = new WP_Query(array(
	'post_type' => 'post',
	'posts_per_page' => 4,
	'post__not_in' => $dnd
));


// events
$events = new WP_Query(
	array(
		'post_type' => 'event',
		'posts_per_page' => 4,
		'meta_query' => array(
			array(
				'key' => 'event_date',
				'value' => date('Y', $today).date('m', $today).date('d', $today),
				'compare' => '>=',
			),
		),
		'post__not_in' => $dnd
	)
);

get_header();
?>
<div class="home-content-grid">
<div class="mdl-grid">
	
	<div class="mdl-cell mdl-cell--8-col">
		<?php if($f_event) { ?>
		<div class="home-featured-event mdl-card mdl-shadow--2dp">
				
		</div>
		<?php } ?>
		
		<div class="home-blog-section-header">
			<h3>Бежим вместе</h3>
			<a href="">Все истории</a>
		</div>
		
		<div class="mdl-grid mdl-grid--no-spacing blog-items">
			<div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-tablet">				
			<?php
				if(isset($f_post->posts[0])){
					echo "<div class='tpl-post featured mdl-card mdl-shadow--2dp invert'>";
					tst_post_card_content($f_post->posts[0]);
					echo "</div>";
				}
			?>
			</div><!-- .mdl-cell -->
			
			<div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-tablet"><div class="blog-list">
			<?php
				if($blog->have_posts()){
					while($blog->have_posts()){
						$blog->the_post();	
						tst_compact_post_item($post, false, 'embed-small');
					}
					wp_reset_postdata();
				}
			?>
			</div></div><!-- .mdl-cell -->
		</div><!-- .mdl-grid -->
		
	</div>
	
	<div class="mdl-cell mdl-cell--4-col">
		<div class="mdl-grid mdl-grid--no-spacing">
			<div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-tablet"><?php get_sidebar(); ?></div>
			<div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-tablet">
			<?php
				if($events->have_posts()){
					foreach($events->posts as $ev){
						tst_compact_event_item($ev);
					}
				}
			?>
			</div>
		</div>
	</div>
</div>
</div>
<?php get_footer(); ?>
