<?php
/**
 * The main template file.
 *
 */

global $wp_query;

$card_loop = is_post_type_archive(array('document', 'book')) ? 'card-board' : '';

get_header();?>

<?php get_template_part('partials/title', 'section');?>	

<?php if(is_home() || is_tax('place')) {?>
<div class="complex-loop content-area <?php echo $card_loop;?>">

	<?php if(is_home() || is_tax('place')) { ?>
		<div class="tag-nav"><?php memo_tags_widget();?></div>
	<?php } ?>
	<div class="frame">
	<?php
		if(have_posts()) {

			$count = 1; $col = 1;
			while(have_posts()){			

				if($count > ceil(count($wp_query->posts)/3)){
					$count = 1;
					$col++;
					echo "</div>";
				}
				
				if($count == 1){
					echo "<div class='bit md-4'>";					
				}
				
				the_post();

                if(is_tax('place')) {
                    get_template_part('partials/content', 'story');
                } else {
                    get_template_part('partials/content', get_post_type());
                }

				$count++;			
			}
			echo "</div>";
		}?>

	</div>

	<?php memo_paging_nav();?>
</div>

<?php } elseif(is_tag()) {?>

	<div class="complex-loop content-area"><div class="frame">
		<?php
			if(have_posts()) {

				while(have_posts()) {

					the_post();
					get_template_part('partials/content');
				}
			}
		?>
		</div>
		<?php memo_paging_nav(); ?>
	</div>

<?php } elseif(is_post_type_archive(array('document', 'book'))) {?>

<div class="complex-loop content-area <?php echo $card_loop;?>">
	<div class="frame">
	<?php
		if(have_posts()) {

			while(have_posts()) {

				the_post();
				get_template_part('partials/content', get_post_type());
			}
		}
	?>
	</div>

	<?php memo_paging_nav();?>
</div>

<?php } elseif(is_search()) {?>

	<div class="frame">
		<div class="content-area search-result bit md-8">
		<?php
			if(have_posts()){
				while(have_posts()){
					the_post();
					get_template_part( 'partials/content', 'search');
				}
			}

            memo_paging_nav();?>
		</div>

		<?php get_sidebar();?>
	</div>

<?php }?>

<?php get_footer(); ?>
