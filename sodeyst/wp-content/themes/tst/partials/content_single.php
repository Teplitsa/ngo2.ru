<?php
/**
 * @package bb
 */

$show_thumb = (function_exists('get_field')) ? (bool)get_field('show_thumbnail') : true;

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post-full'); ?>>

	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		
		<?php if(is_singular()) { ?>
			<h2 class="screen-reader-text wac-go-to-hell">Полный текст публикации</h2>
		<?php } ?>	
		
	</header>
		
	<div class="entry-summary"><?php the_excerpt();?></div>
	
	<?php if($show_thumb) { ?>
		<div class="entry-media">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'tst'), get_the_title()),
				);
				the_post_thumbnail('embed', $attr);
			?>
		</div>
	<?php } ?>
	
	<div class="entry-content"><?php the_content(); ?></div><!-- .entry-content -->
	
	<?php if(!is_singular('leyka_campaign')) { ?>
	<footer class="entry-footer">
		<div class="frame">
			<div class="bit sm-7 md-9">
				<span class="pubdate"><time><?php the_date();?></time></span>
				<?php echo get_the_term_list(get_the_ID(), 'category', ' <span class="category">', ', ', '</span>'); ?>
			</div>
			<div class="bit sm-5 md-3"><?php tst_post_nav(); ?></div>
		</div>
	</footer>
	<?php } ?>
</article><!-- #post-## -->

<?php
	//related
	$num = 4;	
	$r_query = frl_get_related_query($post, 'category', $num); 
	if($r_query && $r_query->have_posts()){
?>
	<aside class="related-posts entry-relations">
		<h3 class="aside-title">Еще почитать</h3>
	<?php
		echo "<div class='frame'>";
			
		while($r_query->have_posts()){
			$r_query->the_post();
			
		?>
			<div class="tpl-related-item bit md-6">
				<div class="frame">
					<div class="bit mf-4 ld-5">
						<a href="<?php the_permalink();?>" class="thumbnail-link">
						<?php
							$attr = array('alt' => sprintf(__('Thumbnail for - %s', 'tst'), get_the_title()));
							the_post_thumbnail('thumbnail', $attr);
						?>
						</a>
					</div>
					<div class="bit mf-8 ld-7">
						<h4 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
						<div class="entry-meta"><?php tst_posted_on(); ?></div>
					</div>
				</div>
			</div>
		<?php	
		}
		wp_reset_postdata();
		
		echo "</div>";
	?>
	</aside>
<?php } ?>