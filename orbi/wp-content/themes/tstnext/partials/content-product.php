<?php
/**
 * Product
 **/

global $post;

	$price = (function_exists('get_field')) ? get_field('product_price', get_the_ID()) : '';
	//$img = tst_get_post_thumbnail_src($post, 'embed');
?>

<div class="tpl-product mdl-card mdl-shadow--2dp">
	<div class="mdl-card__title">
		<div class="mdl-card__title-text">
			<a href="<?php the_permalink();?>"><?php the_title();?></a>
		</div>		
	</div>
	
	<?php if(!empty($price)) { ?>
		<div class="price-mark"><?php echo number_format ((int)$price , 0 , "." , " " );?> руб.</div>
	<?php } ?>
		
	<?php if(has_post_thumbnail()){ ?>
	<div class="mdl-card__media">
		<?php echo tst_get_post_thumbnail(null, 'thumbnail-long'); ?>		
	</div>			
	<?php } ?>
	
	<div class="mdl-card__supporting-text">
	<?php
		$e = (!empty($post->post_excerpt)) ? $post->post_excerpt : wp_trim_words(strip_shortcodes($post->post_content), 30);
		echo apply_filters('tst_the_title', $e);
	?>
		<div class="mdl-typography--caption">Средства пойдут на борьбу с инсультом</div>
	</div>
	
	<div class="mdl-card--expand"></div>
	<div class="mdl-card__actions mdl-card--border">
		<a href="<?php the_permalink();?>" class="mdl-button mdl-js-button mdl-button--colored">Купить</a>
	</div>
</div>