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
	
	/** Calendar **/	
	$('.event-modal').easyModal({
		overlayParent :'.page-content',
		hasVariableWidth : true,
		top : 100,
		transitionIn: 'animated zoomIn',
		transitionOut: 'animated zoomOut',
		onClose : function(){ $('#modal-card').empty(); }
	});
	
	$('body').on('click','.day-link', function(e){
		
		var trigger = $(e.target),
			target, targetEl;
		
		if (trigger.hasClass('day-link')) {
			target = trigger.attr('data-emodal');
		}
		else {
			target = trigger.parents('.day-link').attr('data-emodal');
		}
				
		targetEl = $(target).clone();
		
		$('#modal-card').empty().append(targetEl).trigger('openModal');
		
		e.preventDefault();
	});
	
	$('body').on('click','.calendar-scroll', function(e){
	
		e.preventDefault();		
		var target = $(e.target),
			container = $('#calendar-place');
				
		$.ajax({
			type : "post",
			dataType : "json",
			url : frontend.ajaxurl,
			data : {
				'action': 'calendar_scroll',			
				'nonce' : target.attr('data-nonce'),
				'month' : target.attr('data-month'),
				'year'  : target.attr('data-year')
			},
			beforeSend : function () {
				
				var h = container.height();
				container.addClass('loading').css({height : h+'px'});
			},				
			success: function(response) {
				
				if (response.type == 'ok') {
					container.empty().html(response.data).removeClass('loading').removeAttr('style');
				}
			}
		});
	});
	
	
	
}); //jQuery