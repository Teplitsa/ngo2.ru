<?php
/**
 * The template for displaying search forms 
 */
?>
	<form method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">		
		<input type="search" class="search-field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" id="s">
		<div class="submit-icon">
			<i class="mdi-action-search"></i>			
			<input type="submit" class="search-submit ir" value="">
		</div>
	</form>