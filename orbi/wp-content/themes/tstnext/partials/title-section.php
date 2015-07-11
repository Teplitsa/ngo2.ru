<?php
/**
 * Title */
global $post;

?>

<?php if((is_singular('post') || is_page()) && !is_page('calendar')) { ?>
<div class="mdl-grid">
	<div class="mdl-cell mdl-cell--3-col mdl-cell--hide-phone mdl-cell--hide-tablet"></div>
	<div class="mdl-cell mdl-cell--6-col mdl-cell--5-col-tablet">	
		<h1 class="page-title"><?php echo get_the_title($post);?></h1>
	</div>
	<div class="mdl-cell mdl-cell--3-col "></div>
</div>
<?php } else { ?>
<div class="mdl-grid">
	<div class="mdl-cell mdl-cell--12-col">
	<h1 class="page-title"><?php
		if(is_home()){
			$p = get_post(get_option('page_for_posts'));
			if($p)
				echo get_the_title($p);
		}
		elseif(is_category() || is_tax()){		
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
		elseif(is_page()){
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
	<?php if(is_tax('auctor')) {
		$qo = get_queried_object();
		echo "<div class='author-description'>"; //print event empty - we need it for layout
		if(isset($qo->description)){			
			echo apply_filters('tst_the_title', $qo->description);			
		}
		echo "</div>";
	}
	?>
	</div>
</div>
<?php } //singular ?>


