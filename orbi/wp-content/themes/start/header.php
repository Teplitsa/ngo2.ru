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
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'bb' ); ?></a>
	
	<nav id="langs_nav" class="langs-nav">
		<span class="screen-reader-text"><?php _e( 'Languages', 'bb' ); ?></span>
		<?php wp_nav_menu(array('theme_location' => 'langs', 'container' => false, 'menu_class' => 'langs-menu', 'fallback_cb'=> false)); ?>
	</nav>
	
		
	<header id="site_header" class="site-header" role="banner">
		
		<div class="frame">
			<div class="bit md-8">
				
				<div class="site-branding">
					
					<?php if(!is_front_page()) { ?>	
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-wrap">
					<?php } else { ?>
						<span class="logo-wrap">
					<?php }?>
					
					<div class="site-logo">
						<?php $src = get_template_directory_uri().'/img/logo'; ?>
						<img src="<?php echo $src;?>.svg" onerror="this.onerror=null;this.src=<?php echo $src;?>.png">
					</div>
					<div class="site-title">
						
						<div class="site-title"><!-- print it directry - no other way -->
							<span class="w1-ru">Лучшие</span> <span class="w2-ru">друзья</span> <span class="w3-ru">Россиия</span> <span class='sep'>|</span> <span class="w1-en">Best</span> <span class="w2-en">Buddies</span> <span class="w3-en">Russia</span>
						</div>			
						<div class="site-description"><?php bloginfo( 'description' ); ?></div>						
					</div>
					
					
					<?php if(!is_front_page()) { ?>	
						</a>
					<?php } else { ?>
						</span>
					<?php }?>
					
				</div><!-- .site-branding -->
				
			</div><!-- bit -->
			
			<div class="bit md-4">
				<span class="screen-reader-text"><?php _e( 'Social menu', 'bb' ); ?></span>
				<?php dynamic_sidebar('header-sidebar'); ?>
			</div>
		</div>	
	</header><!-- #masthead -->	
	
	<nav id="site_nav" class="site-nav">
		<span class="screen-reader-text"><?php _e( 'Primary menu', 'bb' ); ?></span>
		
		
		<button id="menu-trigger" class="menu-toggle"><span class="fa fa-bars" aria-hidden="true"></span> <?php echo esc_html(__('Menu', 'bb')); ?></button>
		
		<div class="site-navigation-area">
			<div class="first-row">
				<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'main-menu')); ?>
			</div>
			<div class="second-row">
				<?php wp_nav_menu(array('theme_location' => 'secondary', 'container' => false, 'menu_class' => 'aux-menu')); ?>
				<div id="search-toggle">
					<div class="search-trigger"><i class="fa fa-search"></i></div>
					<div class="search-holder"><?php get_search_form();?></div>
				</div>
			</div>
		</div><!-- .site-navigation-area -->
		
	</nav>
	
	<div id="content" class="site-content">
