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
		
		$('#material-overlay').fadeIn(200); //@to-do: make this smooth 
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
        $container.masonry({
            itemSelector: '.hentry'
        });
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


