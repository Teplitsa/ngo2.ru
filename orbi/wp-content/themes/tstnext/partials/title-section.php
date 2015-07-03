<?php
/**
 * Title */
global $post;

?>
<header class="section-header">
<?php
// breadcrumbs
if(is_singular(array('post'))){
	echo tst_breadcrumbs();
}
else {
?>
<h1 class="section-title"><?php
	if(is_home()){
		$p = get_post(get_option('page_for_posts'));
		if($p)
			echo get_the_title($p);
	}
	elseif(is_category()){
		$p = get_post(get_option('page_for_posts'));
		if($p){
			echo get_the_title($p);
			echo "<span>";
			single_cat_title(' // ');
			echo "</span>";
		}
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
	elseif(is_page()) {
		global $post;
		
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
<?php } ?>
</header>

