<?php
/**
 * @package bb
 */


global $post;

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post-full'); ?>>

	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
		
		<?php if(is_singular()) { ?>
			<h2 class="screen-reader-text wac-go-to-hell">Полный текст публикации</h2>
		<?php } ?>	
		<div class="entry-meta"><?php the_excerpt();?></div>
	</header>
	
	<div class="frame intro-section">
		<div class="bit md-3">
			<div class="entry-media">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'memo'), get_the_title()),
				);
				the_post_thumbnail('portrait', $attr);
			?>
			</div>
		</div>
		<div class="bit md-9">
			<div class="entry-summary">
		<?php
			$intro = (function_exists('get_field')) ? get_field('annotation_full') : '';
			if(empty($intro)){
				$intro = wp_trim_words($post->post_content, 65);
			}
			echo apply_filters('memo_the_content', $intro);
		?>
			</div>
		</div>
	</div>
	
	<div class="frame content-section">
		<div class="bit md-8">
			<div class="entry-content"><?php the_content();?></div>
		</div>
		<div class="bit md-4">
			<div class="column-inner side">
				
				<?php $audios = get_field('audio');
				if($audios) {?>
				<div class="widget">
					<h3 class="widget-title">Аудио</h3>
					<div class="widget-content">
                    <?php
                        if(count($audios) > 1) {

                            $ids = array();
                            foreach($audios as $audio) {
								if( !empty($audio['audio-file']['ID']) ) {
									$ids[] = $audio['audio-file']['ID'];
								}
                            }

							if($ids) {
								echo do_shortcode('[playlist type="audio" ids="'.implode(',', $ids).'" artists="0"]');
							}

                        } elseif(count($audios) == 1 && !empty($audios[0]['audio-file']['url'])) {
                            echo do_shortcode('[audio src="'.$audios[0]['audio-file']['url'].'"]');
                        }
                    ?>
                    </div>
				</div>
				<?php }?>
				
				<?php
					$gallery = memo_post_attached_gallery(get_the_ID(), 2);
					if($gallery) {
				?>
					<div class="widget">
						<h3 class="widget-title">Фото</h3>
						<div class="widget-content"><?php echo memo_post_attached_gallery(get_the_ID(), 2);?></div>
					</div>
				<?php }?>

				<?php dynamic_sidebar( 'story-sidebar' ); ?>
			</div>
		</div>
	</div>
	
	<footer class="post-footer single-footer">
		<div class="frame">
			<div class="bit sm-7 md-9">
				<span class="pubdate"><i class="fa fa-calendar"></i> <time><?php the_date();?></time></span>
				<?php echo get_the_term_list(get_the_ID(), 'place', ' <span class="tags"><i class="fa fa-map-marker"></i>', ', ', '</span>'); ?>
			</div>
			<div class="bit sm-5 md-3"><?php memo_post_nav(); ?></div>
		</div>
		
	</footer>
	
</article><!-- #post-## -->

