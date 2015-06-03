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
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'tst' ); ?></a>
	
	<nav id="top_nav" class="top-nav">
		<div class="search-holder"><div class="container"><?php get_search_form();?></div></div>
		
		<div class="nav-panel container">			
			<?php wp_nav_menu(array('theme_location' => 'social', 'container' => false, 'menu_class' => 'social-menu')); ?>
			<div class="search-trigger"><i class="fa fa-search"></i></div>				
		</div>
	</nav>
	
<div class="site-wrap">		
	<header id="site_header" class="site-header" role="banner">
		
		<div class="frame">
			<div class="bit md-7">
				
				<div class="site-branding">
					
					<?php if(!is_front_page()) { ?>	
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-wrap">
					<?php } else { ?>
						<span class="logo-wrap">
					<?php }?>
					
					<div class="site-logo">
						<?php $src = get_template_directory_uri().'/img/logo'; ?>
						<img src="<?php echo $src;?>.svg" onerror="this.onerror=null;this.src=<?php echo $src;?>.png" alt="<?php bloginfo( 'name' ); ?>">
					</div>
										
					<?php if(!is_front_page()) { ?>	
						</a>
					<?php } else { ?>
						</span>
					<?php }?>
					
				</div><!-- .site-branding -->
				
			</div><!-- bit -->
			
			<div class="bit md-4 md-offset-1"><?php dynamic_sidebar('header-sidebar'); ?></div>
		</div>	
	</header><!-- #masthead -->	
	
	<nav id="site_nav" class="site-nav">
		<span class="screen-reader-text"><?php _e( 'Primary menu', 'tst' ); ?></span>		
		
		<button id="menu-trigger" class="menu-toggle"><span class="fa fa-bars" aria-hidden="true"></span> <?php echo esc_html(__('Menu', 'tst')); ?></button>
		
		<div class="site-navigation-area">
			
			<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'main-menu')); ?>
			
		</div><!-- .site-navigation-area -->
		
	</nav>
	
<?php if(!is_front_page()) { ?>	
	<div id="content" class="site-content">
<?php }?>