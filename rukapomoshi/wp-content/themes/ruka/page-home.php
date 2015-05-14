<?php
/**
 * Template Name: Homepage
 **/

global $post; 
 
$page_id = $post->ID;
$news = new WP_Query(array(
	'post_type' => 'post',
	'posts_per_page' => 3
));

$cta_text = (function_exists('get_field')) ? get_field('cta_text', $page_id) : '';
$cta_link = (function_exists('get_field')) ? get_field('cta_link', $page_id) : '';

$programms = (function_exists('get_field')) ? get_field('featured_programms', $page_id) : array();

get_header();
?>
<header id="site_header" class="site-header" role="banner"><div class="container">
	
	<div class="frame">
		
		<div class="bit md-3">
			<div class="site-branding">		
				<span class="logo-wrap">				
					<?php $src = get_template_directory_uri().'/img/logo'; ?>
					<img src="<?php echo $src;?>.svg" onerror="this.onerror=null;this.src=<?php echo $src;?>.png" alt="<?php bloginfo( 'name' ); ?>">
				</span>				
			</div><!-- .site-branding -->
			
			
			<div class="home-sidebar full-w">
				<?php dynamic_sidebar( 'home-sidebar' ); ?>
			</div>
			
		</div><!-- .bit -->
		
		<div class="bit md-8 md-offset-1">
			<nav id="site_nav" class="site-nav">
				<span class="screen-reader-text"><?php _e( 'Primary menu', 'ruka' ); ?></span>				
				<button id="menu-trigger" class="menu-toggle"><span class="fa fa-bars" aria-hidden="true"></span> <?php echo esc_html(__('Menu', 'ruka')); ?></button>
				
				<div class="site-navigation-area">
					
					<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'main-menu')); ?>
					
					<div id="search-toggle">
						<div class="search-trigger"><i class="fa fa-search"></i></div>
						<div class="search-holder"><?php get_search_form();?></div>
					</div>
					
				</div><!-- .site-navigation-area -->	
			</nav>
			
			<!-- prgramms here -->
			<?php if(!empty($programms)) { ?>
				<div class="programms frame">
			<?php
				foreach($programms as $i => $prog){
					if($i > 1)
						continue;
					
			?>
				<div class="tpl-programm bit sm-6">
					<a href="<?php echo get_the_permalink($prog);?>" class="thumbnail-link">
					<?php
						$attr = array(
							'alt' => sprintf(__('Thumbnail for - %s', 'ruka'), get_the_title($prog->ID)),
						);
						$tagline = (function_exists('get_field')) ? get_field('prog_tagline', $prog->ID) : '';
						
						echo get_the_post_thumbnail($prog->ID, 'post-thumbnail', $attr);
					?>
					</a>
					<header class="entry-header">
						<h3 class="entry-title"><a href="<?php echo get_the_permalink($prog);?>"><?php echo get_the_title($prog->ID);?></a></h3>
					<?php if(!empty($tagline)) { ?>
						<div class="entry-meta"><?php echo apply_filters('ruka_the_title', $tagline);?></div>
					<?php  } ?>
					</header>
				</div>
			<?php
				}				
			?>
				</div>
			<?php } ?>
			<div class="cta-block">
				<div class="cta-content">
				<?php
					while(have_posts()) {
						the_post();
						the_content();
					}
				?>
				</div>
				<?php if(!empty($cta_link) && !empty($cta_text)) { ?>
				<div class="cta-btn">
					<a href="<?php echo esc_url($cta_link);?>"><?php echo $cta_text;?></a>
				</div>
				<?php } ?>
			</div>
			
			<div class="home-sidebar full-small">
				<?php dynamic_sidebar( 'home-sidebar' ); ?>
			</div>
		</div><!-- .bit -->
		
	</div><!-- .frame -->
			
</div></header><!-- #masthead -->	

	
<div id="content" class="site-content"><div class="container">

<section class="page-section news home-section">
	<h3 class="page-section-title">Наши новости <a href="<?php echo get_permalink(get_option('page_for_posts'));?>">Все &raquo;</a></h3>
	<?php if($news->have_posts()) { ?>
	<div id="home-news" class="frame">
		<?php
			while($news->have_posts()){
				$news->the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class('tpl-post bit md-4 eq-item'); ?>>
		<div class="screen-reader-text"><?php _e('Article', 'ruka');?></div>
		<div class="post-frame">
		
			<div class="entry-meta">
				<?php if(has_category('digests')) { ?>
					<span class="issue">Дайджест</span>
				<?php } ?>
				<time><?php echo get_the_date();?></time>
			</div>
			
			<a href="<?php the_permalink();?>" class="thumbnail-link">
			<?php
				$attr = array(
					'alt' => sprintf(__('Thumbnail for - %s', 'ruka'), get_the_title()),
				);
				the_post_thumbnail('long', $attr);
			?>
			</a>	
			
			<header class="entry-header">
				<h3 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>			
			</header>	
			
			<div class="entry-summary"><?php the_excerpt(); ?></div>	
			
		</div>	
		</article><!-- #post-## -->
		<?php
			}
			wp_reset_postdata();
		?>
	</div>
	<?php } ?>
</section>

<section class="page-section partners home-section">
	<h3 class="page-section-title">Нас поддерживают</h3>	
	
	<!-- gallery -->
	<?php if(function_exists('have_rows')) { if(have_rows('our_partners', $page_id)) { ?>
	<ul class="partners-gallery frame">
	<?php
		while(have_rows('our_partners', $page_id)){
			the_row();
			
			$title = get_sub_field('partner_title');  
			$url = get_sub_field('partner_link');
			$url = (!empty($url)) ? esc_url($url) : $url;
			$logo_id = get_sub_field('partner_logo');
			$logo = wp_get_attachment_image($logo_id, 'full', false, array('alt' => $title));
		?>	
		<div class="logo bit mf-6 sm-3 lg-2"><div class="logo-frame">			
			<a class="logo-link" title="<?php echo esc_attr($title);?>" href="<?php echo $url;?>"><?php echo $logo ;?></a>
		</div></div>
	<?php } ?>
	</ul>
	<?php  }} //endif ?>
	
	
</section>
<?php get_footer(); ?>