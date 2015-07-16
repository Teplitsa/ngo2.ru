<?php
/**
 * The template for displaying search forms 
 */
?>
<form method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">	
	<div class="mdl-textfield mdl-js-textfield">
		<input class="mdl-textfield__input" type="search" id="s" value="<?php echo esc_attr( get_search_query() ); ?>" name="s"/>	  
	</div>
	<button class="mdl-button mdl-js-button mdl-button--icon" type="submit">
		<i class="material-icons">search</i>
	</button>
</form>