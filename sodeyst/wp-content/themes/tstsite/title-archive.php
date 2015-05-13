<?php
/**
 * Archive headers
 **/


	if(is_category()) :				
		single_cat_title();

	elseif(is_tag()) :		
		single_tag_title();
		
	elseif(is_tax()):
		single_term_title();
	
	elseif(is_post_type_archive()):
		post_type_archive_title();
	
	elseif (is_home()):
		_e('All news', 'tst');
		
	else :
		_e('Archive', 'tst');

	endif;

