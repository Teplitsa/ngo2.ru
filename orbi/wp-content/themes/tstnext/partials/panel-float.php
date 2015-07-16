<?php
/**
 * Floating panel
 **/

global $post;

?>
<div id="float-panel">
<div class="mdl-grid full-width">
	
<?php if(is_singular('event')) { ?>	
	<div class="mdl-cell mdl-cell--7-col mdl-cell--hide-phone mdl-cell--hide-tablet">
		<div class="event-meta-panel pictured-card-item">
		<?php
			$addr = array();
			$date = (function_exists('get_field')) ? get_field('event_date', $post->ID) : $post->post_date;
			$time = (function_exists('get_field')) ? get_field('event_time', $post->ID) : '';
			$addr[] = (function_exists('get_field')) ? get_field('event_location', $post->ID) : '';
			$addr[] = (function_exists('get_field')) ? get_field('event_address', $post->ID) : '';
		
		?>
			<div class="ev-avatar round-image pci-img"><?php echo get_the_post_thumbnail(get_the_ID(), 'thumbnail')?></div>
			
			<div class="ev-content pci-content">
				<h5 class="ev-date mdl-typography--body-1">
					<?php echo date('d.m.Y', strtotime($date)).'&ndash;'.esc_attr($time); ?>
				</h5>
				<p class="ev-addr mdl-typography--caption">
					<?php echo apply_filters('tst_the_title', implode(', ', $addr)); ?>
				</p>
			</div>
		</div>
	</div>
<?php } elseif(is_singular('product')) { ?>
	<div class="mdl-cell mdl-cell--7-col mdl-cell--hide-phone mdl-cell--hide-tablet">
		<div class="product-meta-panel pictured-card-item">
		<?php $price = (function_exists('get_field')) ? get_field('product_price', get_the_ID()) : ''; ?>
			<div class="pr-avatar round-image pci-img"><?php echo get_the_post_thumbnail(get_the_ID(), 'thumbnail')?></div>
			
			<div class="pr-content pci-content">
				<h5 class="pr-title mdl-typography--body-1">
					<?php the_title();?>
				</h5>
				<p class="pr-price mdl-typography--caption">
					<?php echo number_format ((int)$price , 0 , "." , " " );?> руб.
				</p>
			</div>
		</div>
	</div>
<?php } else { ?>	
	<div class="mdl-cell mdl-cell--7-col mdl-cell--hide-phone mdl-cell--hide-tablet">
		<div class="mdl-grid mdl-grid--no-spacing">
			<div class="mdl-cell mdl-cell--6-col">
			<?php
				$author = tst_get_post_author();					
				if(!empty($author)) {
					$avatar = tst_get_author_avatar($author->term_id) ;
			?>
				<div class="entry-author pictured-card-item">
					<div class="author-avatar round-image pci-img"><?php echo $avatar;?></div>
					
					<div class="author-content pci-content">
						<h5 class="author-name mdl-typography--body-1">
							<a href="<?php echo get_term_link($author);?>"><?php echo apply_filters('tst_the_title', $author->name);?></a>
						</h5>
						<p class="author-role mdl-typography--caption">
							<?php echo apply_filters('tst_the_title', $author->description);?>
						</p>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="mdl-cell mdl-cell--6-col">
				<div class="captioned-text">
					<div class="caption"><?php _e('Published', 'tst');?></div>
					<div class="text"><?php echo tst_posted_on($post);?></div>
				</div>
			</div>
		</div><!-- .row -->
	</div>
<?php } ?>

	<div class="mdl-cell mdl-cell--8-col-tablet mdl-cell--5-col">
		<div class="mdl-grid mdl-grid--no-spacing">
			<div class="mdl-cell mdl-cell--9-col mdl-cell--2-col-phone mdl-cell--5-col-tablet">
				<div class="sharing-on-panel"><?php tst_social_share();?></div>
			</div>
			<div class="mdl-cell mdl-cell--3-col mdl-cell--2-col-phone mdl-cell--3-col-tablet">
				<span class="next-link"><?php echo tst_next_link($post); ?></span>
			</div>
		</div><!-- .row -->
	</div>


</div><!-- .row -->
</div>