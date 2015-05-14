<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package bb
 */
?><!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<?php wp_head(); ?>
</head>

<body id="top" <?php body_class(); ?>>
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'ruka' ); ?></a>

<?php if(!is_front_page()) { ?>	
<header id="site_header" class="site-header" role="banner"><div class="container">
	
	<div class="frame">
		
		<div class="bit md-3">
			<div class="site-branding">
			
				<?php if(!is_front_page()) { ?>	
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-wrap">
				<?php } else { ?>
					<span class="logo-wrap">
				<?php }?>
				
				
					<?php $src = get_template_directory_uri().'/img/logo'; ?>
					<img src="<?php echo $src;?>.svg" onerror="this.onerror=null;this.src=<?php echo $src;?>.png" alt="<?php bloginfo( 'name' ); ?>">
				
					
				<?php if(!is_front_page()) { ?>	
					</a>
				<?php } else { ?>
					</span>
				<?php }?>
				
			</div><!-- .site-branding -->
			
		</div><!-- .bit -->
		
		<div class="bit md-9">
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
			
		</div><!-- .bit -->
		
	</div><!-- .frame -->
			
</div></header><!-- #masthead -->	

	
<div id="content" class="site-content"><div class="container">
<?php } ?>	