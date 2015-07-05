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


