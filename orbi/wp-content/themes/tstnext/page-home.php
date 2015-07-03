<?php
/**
 * Template Name: Homepage
 * 
 */

global $post; 
 
$home_id = $post->ID;

//get real pictures if needed
$src_1 = get_template_directory_uri().'/img/slide_1.jpg';
$src_2 = get_template_directory_uri().'/img/slide_2.jpg';

get_header();
?>

<div class="home-slide-one parallax-container">
<header id="site_header" class="site-header section" role="banner">
	<div class="container">
		<div class="row">
			<div class="col s12 m9">
				<div class="site-branding">
					
					
					
						<h4><?php bloginfo('name'); ?></h4>
						<h5><?php bloginfo('description');?></h5>
					
						
					
				</div><!-- .branding -->
			</div>
			<div class="col s12 m3">
				<?php dynamic_sidebar('header-sidebar'); ?>
			</div>
		</div>
	</div>
</header>
	
	<div class="parallax"><img src="<?php echo $src_1;?>"></div>
</div>

<div class="home-content-section white">
	<div class="section">
		<div class="container">
			<?php if(function_exists('have_rows')) { if(have_rows('home_blocks', $home_id)) { ?>
			<div class="row">	
				<?php
					while(have_rows('home_blocks', $home_id)){
						the_row();
						
						$title = get_sub_field('home_block_title');
						$desc = get_sub_field('home_block_desc');  
						$url = get_sub_field('home_block_url');
						$url = (!empty($url)) ? esc_url($url) : $url;
						
						$logo_id = get_sub_field('home_block_img');
						$icon_class = get_sub_field('home_block_icon');
						//$logo_url = wp_get_attachment_url($logo_id);
						
						$img = $icon = '';
						if(!empty($logo_id))
							$img = wp_get_attachment_image($logo_id, 'post-thumbnail', false, array('class' => 'responsive-img'));
						elseif(!empty($icon_class))
							$icon = "<div class='icon-wrap materialize-red-text text-lighten-2'><i class='".esc_attr($icon_class)."'></i></div>";
				?>
					
					<div class="block col s12 l4">
						<div class="row">
							<div class="col s5 l12">
							<?php if(!empty($img)) { ?>	
								<a href="<?php echo $url;?>" class="block-link"><span></span><?php echo $img;?></a>
							<?php } elseif(!empty($icon)) { echo $icon; } ?>
							</div>
							<div class="col s7 l12">
								<h5><?php echo $title;?></h5>
								<p><?php echo apply_filters('tst_the_title', $desc);?></p>
								<p><a href="<?php echo $url;?>" class="btn deep-purple darken-1">Подробнее</a></p>
							</div>
						</div>
					</div>			
				<?php } ?>
				
			</div>
			<?php  }} //endif ?>
		</div>
	</div>
</div>

<div class="home-slide-two parallax-container white-text">
	<div class="section">
		<div class="container valign-wrapper">
		<?php while(have_posts()){ the_post(); ?>
			<div class="entry-content valign"><?php the_content(); ?></div>
		<?php } ?>
		</div>
	</div>
	<div class="parallax"><img src="<?php echo $src_2;?>"></div>
</div>

<?php get_footer(); ?>
