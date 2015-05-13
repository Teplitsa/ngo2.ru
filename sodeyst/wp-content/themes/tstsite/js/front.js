/* scripts */

/* Top link Plugin by David Walsh (http://davidwalsh.name/jquery-top-link)
  rewritten by @foralien to be safely used in no-conflict mode */

(function($) {
 
	$.fn.topLink = function(settings) {
	    var config = {
	    	'min'       : 400,
	    	'fadeSpeed' : 200
	    };
 
		if (settings) $.extend(config, settings);
 
		this.each(function() {
       		//listen for scroll
			var el = $(this);
			el.hide(); //in case the user forgot
			
			$(window).scroll(function() {
				if($(window).scrollTop() >= settings.min){
					el.fadeIn(settings.fadeSpeed);
					
				} else {
					el.fadeOut(settings.fadeSpeed);
				}
			});			
    	});
 
    	return this; 
	};
 
})(jQuery);


jQuery(function($){
		
	$('html').removeClass('no-js').addClass('js');
	
	/* no rich interations fot ie6/ie7 */
	var windowWidth = $(window).width();
	var testIE = false;
	if($('html').hasClass('ie6') || $('html').hasClass('ie7')){
		testIE = true;
	}
	
	/* top link	 */
	var toplinkTrigger = $('#top-link');
	
	if( windowWidth > 600 && !testIE ) {
		toplinkTrigger
		.topLink({ //appearance
			min: 400,
			fadeSpeed: 500
			
		})
		.on('click', function(event){ //smoth scroll
			event.preventDefault();
			var full_url = toplinkTrigger.find('a').attr('href');
			
			var parts = full_url.split("#");
			var trgt = parts[1];
			
			var target_offset = $("#"+trgt).offset();
			var target_top = target_offset.top;
			
				
			$('html, body').animate({scrollTop:target_top}, 900);
		});    
	}
	
	
});