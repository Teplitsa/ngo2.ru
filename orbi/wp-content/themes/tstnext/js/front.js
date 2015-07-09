/* Scripts */
jQuery(document).ready(function($){
	
	
	/** Has js **/
	$('html').removeClass('no-js').addClass('js');
	
    /** Window width **/
	var windowWidth = $('#top').width();
	
	var sidebar = $('.masonry-grid').find('.movable-widget').detach();		
	sidebar.insertAfter('.masonry-grid .masonry-item:nth-of-type(2)');
	
	/** Masonry for cards **/	
//	var $container = $('.masonry-grid');
//    $container.imagesLoaded(function(){
//		
//		//var sidebar = $container.find('.movable-widget').detach();		
//		//sidebar.insertAfter('.masonry-grid .masonry-item:nth-of-type(2)');
//		
//        $container.masonry({
//            itemSelector: '.masonry-item',
//			gutter : 24
//        });
//    });
	
	
	/** Float panel **/
	//var floatPanel = $('#float-panel');
	//
	//$(window).scroll(function() {
	//	if($(window).scrollTop() >= 450){
	//		floatPanel.slideDown(300,function(){
	//			
	//			if($(window).scrollTop() + $(window).height() == $(document).height()) {
	//				floatPanel.slideUp(300);
	//			}				
	//		});			
	//	} else {
	//		floatPanel.slideUp(300);
	//	}		
	//});
	
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
	
	
	
}); //jQuery