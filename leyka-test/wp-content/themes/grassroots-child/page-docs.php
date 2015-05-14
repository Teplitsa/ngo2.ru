<?php
/**
 * Template Name: Documentstion
 **/

global $post; 

get_header();


?>
<div class="content thin">
	
	<article id="post-<?php the_ID(); ?>" <?php post_class('documents'); ?>>
		
		<div class="content-inner"><div class="container-wide">
	  
			<div class="frame">
				<div class="bit md-3">
					<nav id="docs-menu" class="docs-menu">
						<span class="docs-menu-trigger"><i class="dashicons dashicons-menu"></i> Содержание</span>
						<ul class="docs-menu-items">
						<?php
							$nav_args = array(							
								'post_type'    => 'document',							
								'sort_column'  => 'menu_order, post_title',
								'sort_order'   => 'ASC',
								'title_li'     => ''
							);
								wp_list_pages( $nav_args );
						?>
						</ul>
					</nav>
				</div>
				
				<div class="bit md-9">
					<header class="docs-header">										
						<h2 class="docs-title"><?php the_title(); ?></h2>
					</header>
					<div class="docs-loop">
					<?php
						$loop_args = array(
							'sort_order' => 'ASC',
							'sort_column' => 'menu_order,post_title',
							'hierarchical' => 1,							
							'number' => '',							
							'post_type' => 'document'
						);
						$loop = get_pages($loop_args);
						
						foreach($loop as $doc){
							$h_tag = ($doc->post_parent == 0) ? 'h3' : 'h4';
					?>		
						<article class="tpl-document" id="page-item-<?php echo (int)$doc->ID; ?>">
							<h2 class="document-title"><?php echo get_the_title($doc);?></h2>
							<div class="document-content">
								<?php echo apply_filters('the_content', $doc->post_content); ?>
							</div>
						</article>	
					<?php } ?>
					</div>
				</div>
				
			</div>
	
		</div></div><!-- /content-inner -->
	
	</article>
		
	<?php do_action('grt_content_bottom');?>
	
</div> <!-- /content -->
		
<?php get_footer(); ?>