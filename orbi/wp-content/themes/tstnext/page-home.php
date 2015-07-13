<?php
/**
 * Template Name: Homepage
 * 
 */

global $post; 
 
$home_id = $post->ID;


get_header();
?>
<div class="home-content-grid">
<div class="mdl-grid">
	
	<div class="mdl-cell mdl-cell--8-col">
		<div class="home-featured-event mdl-card mdl-shadow--2dp">
			Event
		</div>
		
		<div class="mdl-grid mdl-grid--no-spacing">
			<div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-tablet">Story</div>
			<div class="mdl-cell mdl-cell--6-col mdl-cell--4-col-tablet">Blog</div>
		</div>
	</div>
	
	<div class="mdl-cell mdl-cell--4-col">
		<div class="mdl-grid mdl-grid--no-spacing">
			<div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-tablet"><?php get_sidebar(); ?></div>
			<div class="mdl-cell mdl-cell--12-col mdl-cell--4-col-tablet">Future events</div>
		</div>
	</div>
</div>
</div>
<?php get_footer(); ?>
