/* Scripts */
jQuery(document).ready(function($){
	
	
	/** Has js **/
	$('html').removeClass('no-js').addClass('js');
	
    /** Window width **/
	var windowWidth = $('#top').width();
	
	/** movable widget **/
	var sidebar = $('.masonry-grid').find('.movable-widget').detach();		
	sidebar.insertAfter('.masonry-grid .masonry-item:nth-of-type(2)');
	
	
	/** smart crumbs **/
	var crumb = $('.crumb-name');
	if (crumb.length) {
		$('.mdl-layout__content').scroll(function(){
			
			if($(this).scrollTop() >= 165){
				crumb.css({opacity : 1});			
			} else {
				console.log($(this).scrollTop());
				crumb.css({opacity : 0});		
			}		
		});
	}
	
	/** Float panel **/
	var floatPanel = $('#float-panel'),
		docHeight = $(document).height();
		
	$('.mdl-layout__content').scroll(function() {
		if($('.mdl-layout__content').scrollTop() >= 450 && ($('.mdl-layout__content').scrollTop() + $(window).height() +50 <= docHeight)){
			floatPanel.slideDown(300);			
		} else {
			floatPanel.slideUp(300);
		}		
	});
	
	/**  Second sharing **/
	if ($('.sharing-on-bottom').length) {
		var sharingDist = $('.sharing-on-bottom').offset().top - $('.sharing-on-top').offset().top;
		if (sharingDist <= $(window).height() *0.8) {
			$('.sharing-on-bottom').hide();
		}
	}
	
	$('.event-modal').easyModal({
		overlayParent :'.page-content',
		hasVariableWidth : true,
		top : 70,
		transitionIn: 'animated zoomIn',
		transitionOut: 'animated zoomOut',
		onClose : function(){ $('#modal-card').empty(); }
	});
	
	
	$('.day-link').click(function(e) {
		var target = $(this).attr('data-emodal'),
			targetEl = $(target).clone();
		
		$('#modal-card').empty().append(targetEl).trigger('openModal');
		
		e.preventDefault();
	});
	
}); //jQuery