<?php
/**
 * Title */
global $post;


// breadcrumbs
//echo frl_breadcrumbs();	
?>


<h1 class="section-title"><?php
	if(is_singular('post')){
		//to-do crumbs		
	}
	elseif(is_home()){
		$p = get_post(get_option('page_for_posts'));
		if($p)
			echo get_the_title($p);
	}
	elseif(is_category()){
		$p = get_post(get_option('page_for_posts'));
		if($p){
			echo get_the_title($p);
			single_cat_title(' / ');
		}
	}
	
	elseif(is_search()){
		_e('Search results', 'svet');
	}
?></h1>

