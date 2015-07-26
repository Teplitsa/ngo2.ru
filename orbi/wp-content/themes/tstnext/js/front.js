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
				//console.log($(this).scrollTop());
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
	
	/** Tooltips in calendar **/
	function position_tooltip(trigger, tip){
		
		var	tPosition = trigger.offset(),
			tW = trigger.width(),
			tH = trigger.height(),
			tipMarginLeft = -1*tip.width()/2,
			tipLeft = tW/2 + tPosition.left,
			tipTop = parseInt(tPosition.top) + tH + 10 - parseInt($(window).scrollTop());
		
		
		
		if (tipLeft + tipMarginLeft < 0) {
			tipLeft = 0;
			tipMarginLeft = 0;
		}
		
		tip.css({
			'left' : tipLeft+'px',
			'top' : tipTop + 'px',
			'margin-left' : tipMarginLeft + 'px'
		});
	}
		
	//visibility
	$('body').on('mouseenter', '.tst-add-calendar', function(e){
		e.stopPropagation();
		var trigger = $(this),
			tipTarget = trigger.attr('id'),
			tip = $('span[for="'+tipTarget+'"]');
				
		//don't open if d-down is visible
		if('visible' != trigger.find('.atcb-list').css('visibility')){
			//position			
			position_tooltip(trigger, tip);
			
			tip.addClass('active');
			
			//listen for click
			trigger.on('click', function(ev){
				//hide
				tip.removeClass('active').removeAttr('style');
			});	
		}
	})
	.on('mouseleave', '.tst-add-calendar', function(e){
		e.stopPropagation();
		var trigger = $(this),
			tipTarget = trigger.attr('id'),
			tip = $('span[for="'+tipTarget+'"]');
			
			tip.removeClass('active').removeAttr('style');
	});
	
	
	/** Calendar **/	
	var topPad = (windowWidth > 940) ? 100 : 50;
	
	$('.event-modal').easyModal({
		//overlayParent :'.page-content',
		hasVariableWidth : true,
		top : topPad,
		transitionIn: 'animated zoomIn',
		transitionOut: 'animated zoomOut',
		onClose : function(){ $('#modal-card').empty(); },
		onOpen : function() {
			
			//bind tip interaction
			//$('#modal-card').find('.in-modal-add-tip').mouseenter(function(e){
			//	
			//	e.stopPropagation();
			//	var trigger = $(this),
			//		tipTarget = trigger.attr('id'),
			//		tip = $('span[for="'+tipTarget+'"]');
			//		
			//	//don't open if d-down is visible
			//	if('visible' != trigger.find('.atcb-list').css('visibility')){
			//		//position			
			//		position_tooltip(trigger, tip);
			//		
			//		tip.addClass('active');
			//		
			//		//listen for click
			//		trigger.on('click', function(ev){
			//			//hide
			//			tip.removeClass('active').removeAttr('style');
			//		});	
			//	}
			//})
			//.mouseleave(function(e){
			//	e.stopPropagation();
			//	var trigger = $(this),
			//		tipTarget = trigger.attr('id'),
			//		tip = $('span[for="'+tipTarget+'"]');
			//		
			//		tip.removeClass('active').removeAttr('style');
			//}); //mouseactions			
		}
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
				
		targetEl = $(target).clone(true);
		
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
	
	
	/** Select dropdown **/
	var selectMat = $('.tst-select');
	selectMat.each(function(i){
				
		var selectContainer = $(this),
			optionsData = selectContainer.find('option'),
			selectedVal = selectContainer.find('select').val(),
			selectedText = selectContainer.find('option').filter('[value = "'+selectedVal+'"]').text(),
			insnanceId = 'tst-select-'+i,
			trigger = $('<div class="tst-menu-trigger mdl-button mdl-js-button" id="'+insnanceId+'">'+selectedText+'</div>')
			menuUl = $('<ul class="tst-select-menu mdl-menu mdl-menu--bottom-left mdl-js-menu mdl-js-ripple-effect" for="'+insnanceId+'"></ul>');
		
		if (optionsData.length) {
			optionsData.each(function(){
				var opt = $(this),
					value = $(this).val(),
					label = $(this).text(),
					li = $('<li class="mdl-menu__item">'+label+'</li>');
					
				if (value == selectedVal) {
					li.addClass('selected');
				}
								
				li.attr({'data-value': value}).appendTo(menuUl);				
			});
		}
		
		selectContainer.find('select').hide();		
		selectContainer.append(trigger).append(menuUl);
		
		menuUl.on('click', 'li', function(e){
			
			var selVal = $(this).attr('data-value'),
				field = $(this).parents('.tst-select');
			
			field.find('.tst-menu-trigger').text(selVal);
			field.find('select').val(selVal);
			field.find('.mdl-menu__item').removeClass('selected');
			$(this).addClass('selected');
			
			//field.find('options').prop('selected', false).filter('[value="'+selVal+'"]').prop('selected', true);
			
		});
	});
	
	/** Leyka custom modal **/
	$('.leyka-custom-modal').easyModal({
		overlayParent :'.leyka-custom-template',
		hasVariableWidth : true,
		top : 100,
		transitionIn: 'animated zoomIn',
		transitionOut: 'animated zoomOut',
		onClose : function(){  }
	});
	
	$('body').on('click','.leyka-custom-confirmation-trigger', function(e){
		
		var trigger = $(e.target),
			target;
		
		if (trigger.hasClass('leyka-custom-confirmation-trigger')) {
			target = trigger.attr('data-lmodal');
		}
		else {
			target = trigger.parents('.leyka-custom-confirmation-trigger').attr('data-lmodal');
		}
			
		
		$('#leyka-agree-text').trigger('openModal');
		
		
		e.preventDefault();
	});
	
	$('body').on('click', '.leyka-modal-close', function(e){
		$('#leyka-agree-text').trigger('closeModal');
	});
	
	
}); //jQuery