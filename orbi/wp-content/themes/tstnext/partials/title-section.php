<?php
/**
 * Title */
global $post;

?>
<header class="section-header">
<?php if(is_singular('post')) { ?>
	<div class="row">
		<div class="col md-8 lg-6 lg-offset-3">
			<h1 class="section-title"><?php echo get_the_title($post);?></h1>
		</div>
		<div class="col md-4 lg-3">&nbsp;</div>
	</div>
<?php } else { ?>
<h1 class="section-title"><?php
	if(is_home()){
		$p = get_post(get_option('page_for_posts'));
		if($p)
			echo get_the_title($p);
	}
	elseif(is_category()){		
		single_cat_title();
		
	}
	elseif(is_post_type_archive('event')) {
		$p = get_post(get_option('page_for_posts'));
		if($p){
			echo get_the_title($p);
			echo "<span>";
			post_type_archive_title(' // ');
			echo "</span>";
		}
	}
	elseif(is_page() || is_single()) {
		echo get_the_title($post);
	}
	elseif(is_search()){
		_e('Search results', 'tst');
	}
	elseif(is_404()){
		_e('404: Page not found', 'tst');
	}
?>
</h1>
<?php } //singular ?>
</header>

