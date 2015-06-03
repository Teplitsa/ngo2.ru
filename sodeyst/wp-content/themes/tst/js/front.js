/* actions */
jQuery(document).ready(function($){
    
    //has js
	$('html').removeClass('no-js').addClass('js');
	
    // Window width 
	var windowWidth = $('#top').width();	


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
    
    // focus on search form */
	$('.search-field').focus(function(){
		$(this).parents('form').addClass('focus');	
	});
	
	$('.search-field').blur(function(){
		$(this).parents('form').removeClass('focus');	
	});
    
	// search toggle
	var searchArea = $('#top_nav');
	searchArea.on('click', '.search-trigger', function(e){
		e.preventDefault();
		if (searchArea.hasClass('toggled')) { 
            //remove
            searchArea.find('.search-holder').slideUp('normal', function(){
				searchArea.removeClass('toggled');
				searchArea.find('.search-holder').removeAttr('style');
				searchArea.find('.search-trigger').find('.fa').removeClass('fa-times-circle').addClass('fa-search');
			});
            
        }
        else { 
            //add
            searchArea.find('.search-holder').slideDown('normal', function(){
				searchArea.addClass('toggled');
				searchArea.find('.search-holder').removeAttr('style');
				searchArea.find('.search-trigger').find('.fa').removeClass('fa-search').addClass('fa-times-circle');
			});
            
        }
	});
	
    
    // Center logos 
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
	imagesLoaded('.events-gallery', function(){
		$('.events-gallery .tpl-event-in-gallery').responsiveEqualHeightGrid();		
	});
	
	$(window).resize(function(){
		$('.events-gallery .tpl-event-in-gallery').responsiveEqualHeightGrid();
	});
    
	// Home page
    var $home_intro = $('#home_intro');
	if($home_intro.length) {
        $home_intro.find('.wrap').lettering('words');
	}
});