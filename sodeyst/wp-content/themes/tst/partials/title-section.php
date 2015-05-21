<?php
/**
 * Title */
global $post;


// breadcrumbs
//echo frl_breadcrumbs();	
?>
<header class="section-header">
<?php
if(is_singular(array('post', 'event', 'leyka_campaign'))) {
	echo tst_breadcrumbs();	
} else { ?>
<h1 class="section-title"><?php
	if(is_home()){
		$p = get_post(get_option('page_for_posts'));
		if($p)
			echo get_the_title($p);
	}
	elseif(is_category()){
		$p = get_post(get_option('page_for_posts'));
		if($p){
			single_cat_title();			
			echo "<span> / ";
			echo get_the_title($p);
			echo "</span>";
		}
	}	
	elseif(is_post_type_archive('event')) {
		$p = get_page_by_path('events');
		if($p){
			echo tst_get_post_type_archive_title('event');			
			echo "<span> / ";
			echo get_the_title($p);
			echo "</span>";
		}
	}
	elseif(is_page()) {
		global $post;
		
		if($post->post_parent > 0){
			$p = get_page($post->post_parent);
			if($p){
				echo get_the_title($post);				
				echo "<span> / ";
				echo get_the_title($p);
				echo "</span>";
			}
		}
		else {
			echo get_the_title($post);
		}
		
	}
	elseif(is_search()){
		_e('Search results', 'tst');
	}
	elseif(is_404()){
		_e('404: Page not found', 'tst');
	}
?></h1>
<?php } ?>
</header>

