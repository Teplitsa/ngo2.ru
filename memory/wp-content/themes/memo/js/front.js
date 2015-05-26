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
    
    //has js
	$('html').removeClass('no-js').addClass('js');
	
    // Window width 
	var windowWidth = $('#top').width();	

	// Top link	 
	var toplinkTrigger = $('#top-link');

	if( windowWidth > 600 ) {
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
   

    // Resize all embed media iframes to fit the page width
    var resize_embed_media = function(){

        $('iframe').each(function(){

            var $iframe = $(this),
                $parent = $iframe.parent(),
                do_resize = false;
            if($parent.hasClass('embed-content'))
                do_resize = true;            
            else {                
                
                $parent = $iframe.parents('.entry-content');
                if($parent.length)
                    do_resize = true;
            }

            if(do_resize) {

                var change_ratio = 0.98*$parent.width()/$iframe.attr('width');
                $iframe.width(change_ratio*$iframe.attr('width'));
                $iframe.height(change_ratio*$iframe.attr('height'));
            }
        });
    };
    resize_embed_media(); // Initial page rendering
    $(window).resize(resize_embed_media());


    var navCont = $('#site_nav');
    $('#menu-trigger').on('click', function(e){

        e.preventDefault();
        if (navCont.hasClass('toggled')) { 
            //remove
            navCont.find('.site-navigation-area').slideUp('normal', function(){
				navCont.removeClass('toggled');
				navCont.find('.site-navigation-area').removeAttr('style');
			});
            
        }
        else { 
            //add
            navCont.find('.site-navigation-area').slideDown('normal', function(){
				navCont.addClass('toggled');
				navCont.find('.site-navigation-area').removeAttr('style');
			});
            
        }
    });
    
    // focus on search form 
	$('.search-field').focus(function(){
		$(this).parents('form').addClass('focus');	
	});
	
	$('.search-field').blur(function(){
		$(this).parents('form').removeClass('focus');	
	});
    
	// search toggle
	var searchArea = $('#search-toggle');
	searchArea.on('click', '.search-trigger', function(e){
		e.preventDefault();
		if (searchArea.hasClass('toggled')) { 
            //remove
            searchArea.find('.search-holder').slideUp('fast', function(){
				searchArea.removeClass('toggled');
				searchArea.find('.search-holder').removeAttr('style');
				searchArea.find('.search-trigger').find('.fa').removeClass('fa-times-circle').addClass('fa-search');
			});
            
        }
        else { 
            //add
            searchArea.find('.search-holder').slideDown('fast', function(){
				searchArea.addClass('toggled');
				searchArea.find('.search-holder').removeAttr('style');
				searchArea.find('.search-trigger').find('.fa').removeClass('fa-search').addClass('fa-times-circle');
			});
            
        }
	});
    
    // center logos 
	function logo_vertical_center() {
		
		var logos = $('.logo-frame'),
			logoH = logos.eq(0).parents('.logo').height() - 3;
			
		logos.find('span, a').css({'line-height' : logoH + 'px'});
	}
		
	
	imagesLoaded('#primary', function(){
		logo_vertical_center();
	});
		
	$(window).resize(function(){
		logo_vertical_center();
	});
	
	
	// Equal height blocks eq-item	
	imagesLoaded('.card-board', function(){
		$('.tpl-story').responsiveEqualHeightGrid();		
	});
	
	$(window).resize(function(){
		$('.card-board .tpl-story').responsiveEqualHeightGrid();
	});
	
	
	/** Map page */
    if($('#map').length) {

        var map = L.map('map').setView([59.54, 30.90], 13); // set to the Tosno coords

        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            maxZoom: 18
        }).addTo(map);

        for(var i=0; i<markers.length; i++) {

            markers[i].history_photo = typeof markers[i].history_photo != 'undefined' && markers[i].history_photo ?
                '<img src="'+markers[i].history_photo+'">' : '';
            markers[i].modern_photo = typeof markers[i].modern_photo != 'undefined' && markers[i].modern_photo ?
                '<img src="'+markers[i].modern_photo+'">' : '';

            L.marker([markers[i].lat, markers[i].lng], {
                title: markers[i].addr,
                alt: markers[i].addr
            }).addTo(map).bindPopup(
                markers[i].history_photo+
                markers[i].modern_photo+
                '<br><br>'+
                markers[i].text
            );
        }
    }
    
});