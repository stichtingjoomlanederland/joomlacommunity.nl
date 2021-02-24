ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

var lib = {
	wrapper: $('[data-ed-filters]'),

	/**
	 * Retrieves the active category id
	 */
	getActiveCategory: function() {
		return this.wrapper.data('category');
	},

	/**
	 * Retrieves the active tag id
	 */
	getActiveTag: function() {
		return this.wrapper.data('tag');
	},

	/**
	 * Retrieves the base url of the current page
	 */
	getBaseUrl: function() {
		return this.wrapper.data('baseurl');
	}
}




var filter = $('[data-module-filters] [data-module-filter]');

filter.on('click', function() {

	var element = $(this);
	var type = $(this).data('module-filter');
	var id = $(this).data('id');

	// Standard filters
	if (type == 'main') {

		// Remove all active classes
		var parent = element.parents('[data-module-filters]');
		parent.find('[data-module-filter]').removeClass('is-active');

		// Apply active class to itself
		element.addClass('is-active');

		// Update the element so that the trigger can update the contents accordingly
		var element = $('<div>').html(element.find('>a').html());
	}

	if (type != 'main' && type != 'category') {
		var filter = $('[data-ed-filter=' + type + '][data-id=' + id + ']');
		filter.click();

		var isActive = filter.hasClass('is-active');

		if (isActive) {
			element.addClass('is-active');
			return;
		}
		
		element.removeClass('is-active');
		return;
	}

	$('body').trigger('easydiscuss.render', [type, lib.getActiveCategory(), id, element, lib.getBaseUrl(), lib.getActiveTag()]);
});


});