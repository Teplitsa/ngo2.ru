<?php
/**
 * Title */
global $post;


// breadcrumbs
//echo frl_breadcrumbs();	
?>
<header class="section-header"><div class="header-block">

<?php
	if(is_singular(array('post'))) { 
	
		$cat = get_the_category_list(', ', '', $post->ID);
		if(!empty($cat)){
			echo "<div class='category'>".strip_tags($cat)."</div>";
		}		
?>
	<time><?php echo get_the_date('d.m.Y', $post->ID);?></time>
	
<?php } else { ?>
<h1 class="section-title"><?php
	if(is_home()){
		$p = get_post(get_option('page_for_posts'));
		if($p)
			echo get_the_title($p);
	}
	elseif(is_help()) {
		echo 'Как помочь';
	}
	elseif(is_programms()) {
		$p = get_page_by_path('programms');
		echo apply_filters('ruka_the_title', $p->post_title);
	}
	elseif(is_category()){
		
		single_cat_title();
			
	}	
	elseif(is_page()) {
		global $post;
		
		echo get_the_title($post);
	}
	elseif(is_search()){
		_e('Search results', 'ruka');
	}
	elseif(is_404()){
		_e('404: Page not found', 'ruka');
	}
?></h1>
<?php } ?>

</div></header>

