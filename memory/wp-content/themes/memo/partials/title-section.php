<?php
/**
 * Title */
global $post;


// breadcrumbs
//echo frl_breadcrumbs();	
?>
<header class="section-header">

<?php
if(is_singular(array('post', 'book', 'document'))) {
	echo memo_breadcrumbs();	
} else { ?>
<h1 class="section-title"><?php
	if(is_home()){
		$p = get_post(get_option('page_for_posts'));
		if($p)
			echo get_the_title($p);
	}
	if(is_tax('place')){
		
		single_term_title();
				
		$p = get_post(get_option('page_for_posts'));
		if($p){
			
			echo "<span><a href='".get_permalink($p)."'>";
			echo get_the_title($p);
			echo "</a></span>";
		}
	}
	elseif(is_post_type_archive('document')) {
		
		echo memo_get_post_type_archive_title('document');
	}
	elseif(is_post_type_archive('book')) {
		
		echo memo_get_post_type_archive_title('book');
	}
	elseif(is_singular('leyka_campaign')) {
		global $post;
		
		echo get_the_title($post);
	}
	elseif(is_page()) {
		global $post;
		
		echo get_the_title($post);
	}
	elseif(is_search()){
		_e('Search results', 'memo');
	}
	elseif(is_404()){
		_e('404: Page not found', 'memo');
	}
?></h1>
<?php } ?>
</header>

