/* Scripts */
jQuery(document).ready(function($){
    
    /** Has js **/
	$('html').removeClass('no-js').addClass('js');
	
    /** Window width **/
	var windowWidth = $('#top').width();	

    /** Fixed navbar **/
	var stickyNav = $('#site_nav'),
		sticky_navigation_offset_top = stickyNav.length ? stickyNav.offset().top : 0;
	
	var sticky_navigation = function(){
		var scroll_top = $(window).scrollTop(); 
		if (scroll_top > sticky_navigation_offset_top) { 
			stickyNav.addClass('fixed');
			
		} else {
			stickyNav.removeClass('fixed');		
			
		}   
	};
		
	sticky_navigation();
	$(window).scroll(function() {
		sticky_navigation();
	});
	
	
	/** SideNav **/
	$('.menu-trigger').on('click', function(e){
		
		e.preventDefault();
		
		var $this = $(this),
			target_id = "#"+ $this.attr('data-activates'),
			target = $(target_id);
		
		$('#material-overlay').fadeIn(90); //@to-do: make this smooth 
		target.animate({left : 0}, 600, function(){
			target.addClass('active');
			
			$('body').on('click', ':not('+target_id+')', function(ev){
				if (target.hasClass('active')) {
					target.animate({left : '-105%'}, 600).removeClass('active');
					$('#material-overlay').fadeOut(200); 
				}				
			});
			
			$('body').keyup(function(ev) {
				if (ev.keyCode == 27 && target.hasClass('active')) { // escape key maps to keycode `27`
					target.animate({left : '-105%'}, 600).removeClass('active');
					$('#material-overlay').fadeOut(200); 
				}
			});
		}); //animate
		
	});
	
	/** Masonry for cards **/	
	var $container = $('.masonry-grid');
    $container.imagesLoaded(function(){
		
		var sidebar = $container.find('.movable-widget').detach();		
		sidebar.insertAfter('.masonry-grid .masonry-item:nth-of-type(2)');
		
        $container.masonry({
            itemSelector: '.masonry-item'
        });
    });
	
	/** Float panel **/
	var floatPanel = $('#float-panel');
	
	$(window).scroll(function() {
		if($(window).scrollTop() >= 450){
			floatPanel.slideDown(300,function(){
				
				if($(window).scrollTop() + $(window).height() == $(document).height()) {
					floatPanel.slideUp(300);
				}				
			});			
		} else {
			floatPanel.slideUp(300);
		}		
	});
	
	/* Smart crumbs **/
	var crumb = $('.navbar-title').find('.crumbs').find('span');
	if (crumb.length) {
		$(window).scroll(function() {
			//console.log($(window).scrollTop());
			if($(window).scrollTop() >= 150){
				crumb.fadeIn(300);			
			} else {
				crumb.fadeOut(300);	
			}		
		});
	}
	
	
	/** Responsive media **/
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

                var change_ratio = $parent.width()/$iframe.attr('width');
                $iframe.width(change_ratio*$iframe.attr('width'));
                $iframe.height(change_ratio*$iframe.attr('height'));
            }
        });
    };
	
    resize_embed_media(); // Initial page rendering
    $(window).resize(function(){		
		resize_embed_media();	
	});
	
//    // focus on search form */
//	$('.search-field').focus(function(){
//		$(this).parents('form').addClass('focus');	
//	});
//	
//	$('.search-field').blur(function(){
//		$(this).parents('form').removeClass('focus');	
//	});
//    
//    
//    // Center logos 
//	function logo_vertical_center() {
//		
//		var logos = $('.logo-frame'),
//			logoH = logos.eq(0).parents('.logo').height() - 3;
//			
//		logos.find('span, a').css({'line-height' : logoH + 'px'});
//	}
//		
//	
//	imagesLoaded('#primary', function(){
//		logo_vertical_center();
//	});
//		
//	$(window).resize(function(){
//		logo_vertical_center();
//	});
//	
//	
//	// Equal height blocks eq-item	
//	imagesLoaded('#primary', function(){
//		$('.eq-item').responsiveEqualHeightGrid();		
//	});
//	
//	$(window).resize(function(){
//		$('.eq-item').responsiveEqualHeightGrid();
//	});
	  
	
        
});


