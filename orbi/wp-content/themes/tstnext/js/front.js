/* Scripts */
jQuery(document).ready(function($){
    
    //has js
	$('html').removeClass('no-js').addClass('js');
	
    // Window width 
	var windowWidth = $('#top').width();	

    /** Fixed navbar **/
	
	// grab the initial top offset of the navigation
	var stickyNav = $('#site_nav'),
		sticky_navigation_offset_top = stickyNav.length ? stickyNav.offset().top : 0;
	
	// our function that decides weather the navigation bar should have "fixed" css position or not.
	var sticky_navigation = function(){
		var scroll_top = $(window).scrollTop(); // our current vertical position from the top			
		
		// if we've scrolled more than the navigation, change its position to fixed to stick to top, otherwise change it back to relative
		if (scroll_top > sticky_navigation_offset_top) { 
			stickyNav.addClass('fixed');
			//$('body').css("padding-top", "66px");//code
			
			
		} else {
			stickyNav.removeClass('fixed');
			//$('body').css("padding-top", "0");
		}   
	};
	
	// run our function on load
	sticky_navigation();
	
	// and run it again every time you scroll
	$(window).scroll(function() {
		sticky_navigation();
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


