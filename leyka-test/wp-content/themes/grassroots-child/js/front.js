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

jQuery(document).ready(function($){
	
	// Top link	 
	var toplinkTrigger = $('#top-link');
	
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
	
	
	
	// Responsive nav:
    var navCont = $('#docs-menu');
    navCont.on('click', '.docs-menu-trigger', function(e){

        e.preventDefault();
        if (navCont.hasClass('toggled')) { 
            //remove
            navCont.find('ul').slideUp('normal', function(){
				navCont.removeClass('toggled');
				$(this).removeAttr('style');
			});
            
        }
        else { 
            //add
            navCont.find('ul').slideDown('normal', function(){ navCont.addClass('toggled'); });
            
        }
    });

	
	//scroll for docs
	$('.docs-menu-items').on('click', 'a', function(e){
		e.preventDefault();
		
		var targetRaw = $(this).parent('li'),
			target = targetRaw.removeClass('page_item').removeClass('page_item_has_children').attr('class');
		console.log(target);
			if(target) {
				var target_top = $("#"+target).offset().top;
				$('html, body').animate({scrollTop:target_top}, 900);
			}
				
			
	});
});