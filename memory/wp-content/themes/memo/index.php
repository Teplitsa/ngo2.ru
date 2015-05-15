<?php
/**
 * The main template file.
 *
 */

global $wp_query;
 
get_header(); ?>

<?php get_template_part('partials/title', 'section');?>	

<?php if(is_post_type_archive(array('document', 'book')) || is_home()) { ?>
<div class="complex-loop content-area">
	<div class="frame">
	<?php
		if(have_posts()){
			$count = 1;
			while(have_posts()){			
				
				if($count > ceil(count($wp_query->posts)/3)){
					$count = 1;
					echo "</div>";
				}
				
				if($count == 1)
					echo "<div class='bit md-4'>";
				
				the_post();
				get_template_part( 'partials/content', get_post_type() );
				$count++;			
			}
			echo "</div>";
		}
	?>
	</div>
	<?php memo_paging_nav(); ?>
</div>
<?php
	}
	else {
		
	}
?>

<?php get_footer(); ?>
