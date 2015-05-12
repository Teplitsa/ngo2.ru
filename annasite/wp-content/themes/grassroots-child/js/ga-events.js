/* GA events */

jQuery(function($){
	
	
	/** Leyka at WP **/
	$("a[href*='wordpress.org/plugins/leyka']").on('click', function(e){
		var label;
		
		if ($(this).attr('data-position')) {
			label = $(this).attr('data-position');
		}
		else {
			label = 'other_link_leyka_wp'
		}
		
		ga('send', 'event', 'leyka_wordpress_org', 'click', label, 1);
	});
	
	/** Leyka at GitHub **/
	$("a[href*='github.com/Teplitsa/Leyka']").on('click', function(e){
		var label;
		
		if ($(this).attr('data-position')) {
			label = $(this).attr('data-position');
		}
		else {
			label = 'other_link_leyka_github'
		}
		
		ga('send', 'event', 'leyka_wordpress_github', 'click', label, 1);
	});	
	
	
	/** Grassroots Downloads **/
	$("a.download-grassroots").on('click', function(e){
		var label;
		
		if ($(this).attr('data-position')) {
			label = $(this).attr('data-position');
		}
		else {
			label = 'other_link_grassroots_download'
		}
		
		ga('send', 'event', 'grassroots_download', 'click', label, 1);
	});
	
	/** Grassroots at GitHub **/
	$("a[href*='github.com/Teplitsa/grassroots']").on('click', function(e){
		var label;
		
		if ($(this).attr('data-position')) {
			label = $(this).attr('data-position');
		}
		else {
			label = 'other_link_grassroots_github'
		}
		
		ga('send', 'event', 'grassroots_github', 'click', label, 1);
	});
	
});