(function($) {
	$(document).ready(function() {
		if ( typeof MooTools != 'undefined' ) {
			(function($) {
				$("div[id$='datetimepicker']").each(function (i,el) {
					if (typeof $(el)[0] != 'undefined') {
						$(el)[0].hide = null;
					}
				});
				
				$('.modal').each(function(){
					this.hide = null;
				});
			})(jQuery);
		}
		
		$('.accordion').each(function() {
			$(this).find('.accordion-toggle').each(function(i,title){
				if ($(title).parent().siblings('.accordion-body').hasClass('in') === false) {
					$(title).addClass('collapsed');
				}
			});
		});
		
		$('.accordion-toggle').click(function(){
			$(this).parents('.accordion').each(function(){
				$(this).find('.accordion-toggle').each(function(i,title) {
					$(title).addClass('collapsed');
				});
			});
		});
		
		if ( typeof MooTools != 'undefined' ) {
			(function($) { 
				$$('[data-toggle=collapse]').each(function (e) {
					if ( typeof $$(e.get('data-target'))[0] != 'undefined' ) {
						$$(e.get('data-target'))[0].hide = null;
					}
				});
			})(MooTools);
			
			$('.bootstrap-datetimepicker-widget li').each(function(i, el){
				if (typeof $(el) != 'undefined' || typeof $(el)[0] != 'undefined') {
					$(el)[0].hide = null;
				}
			});
			
			$('.uk-navbar-nav li').each(function(i, el){
				if (typeof $(el) != 'undefined' || typeof $(el)[0] != 'undefined') {
					$(el)[0].hide = null;
				}
			});
		}
	});
})(jQuery);

if (window.MooTools) {
	// Mootools conflict fix for toggle with Bootstrap 3/JQuery
	window.addEvent('load', function() {
		$$("[rel=tooltip],[data-toggle],a[data-toggle],button[data-toggle],[data-toggle=collapse],a[data-toggle=dropdown]").each(function (e) {
			e.getParent().hide = null;
			e.hide = null;
		});
	});
}