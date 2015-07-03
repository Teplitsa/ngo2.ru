/* Scripts */
jQuery(document).ready(function($){
    
    //has js
	$('html').removeClass('no-js').addClass('js');
	
    // Window width 
	var windowWidth = $('#top').width();	

    
    // focus on search form */
	$('.search-field').focus(function(){
		$(this).parents('form').addClass('focus');	
	});
	
	$('.search-field').blur(function(){
		$(this).parents('form').removeClass('focus');	
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
	imagesLoaded('#primary', function(){
		$('.eq-item').responsiveEqualHeightGrid();		
	});
	
	$(window).resize(function(){
		$('.eq-item').responsiveEqualHeightGrid();
	});
	  
	
        
});


(function($){
	$.fn.parallax = function () {
      var window_width = $(window).width();
      // Parallax Scripts
      return this.each(function(i) {
        var $this = $(this);
        $this.addClass('parallax');

        function updateParallax(initial) {
          var container_height;
          if (window_width < 601) {
            container_height = ($this.height() > 0) ? $this.height() : $this.children("img").height();
          }
          else {
            container_height = ($this.height() > 0) ? $this.height() : 500;
          }
          var $img = $this.children("img").first();
          var img_height = $img.height();
          var parallax_dist = img_height - container_height;
          var bottom = $this.offset().top + container_height;
          var top = $this.offset().top;
          var scrollTop = $(window).scrollTop();
          var windowHeight = window.innerHeight;
          var windowBottom = scrollTop + windowHeight;
          var percentScrolled = (windowBottom - top) / (container_height + windowHeight);
          var parallax = Math.round((parallax_dist * percentScrolled));

          if (initial) {
            $img.css('display', 'block');
          }
          if ((bottom > scrollTop) && (top < (scrollTop + windowHeight))) {
            $img.css('transform', "translate3D(-50%," + parallax + "px, 0)");
          }

        }

        // Wait for image load
        $this.children("img").one("load", function() {
          updateParallax(true);
        }).each(function() {
          if(this.complete) $(this).load();
        });

        $(window).scroll(function() {
          window_width = $(window).width();
          updateParallax(false);
        });

        $(window).resize(function() {
          window_width = $(window).width();
          updateParallax(false);
        });

      });

    };
	
	$(document).ready(function(){
		$('.parallax').parallax();
    });	
})(jQuery); // end of jQuery name space