<?php
/**
 * The template for displaying search forms 
 */
?>
<form method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	
	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mdl-textfield--align-right">
		
		<input class="mdl-textfield__input" type="text" id="s" value="<?php echo esc_attr( get_search_query() ); ?>" name="s"/>
		<label class="mdl-textfield__label" for="s">Поиск...</label>
		<button class="mdl-button mdl-js-button mdl-button--icon" type="submit">
			<i class="material-icons">search</i>
		</button>
	</div>
	
</form>
