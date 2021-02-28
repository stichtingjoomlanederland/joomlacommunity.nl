ed.require(['edq', 'easydiscuss', 'perfect-scrollbar'], function($, EasyDiscuss) {


// Apply perfect scrollbar for filters dropdown
var initPerfectScrollbar = function () {
	var filterContainer = $('[data-ed-filter-container]');

	if (filterContainer.length > 0) {
		new PerfectScrollbar('[data-ed-filter-container]', {
		});
	}

	var categoryContainer = $('[data-ed-category-container]');

	if (categoryContainer.length > 0) {
		new PerfectScrollbar('[data-ed-category-container]', {
			suppressScrollX: true
		});
	}
}


initPerfectScrollbar();

var wrapper = $('[data-ed-filters]');
var filter = $('[data-ed-filter]');

/**
 * Retrieves the active category id
 */
var getActiveCategory = function() {
	var categoryId = wrapper.data('category');

	return categoryId;
};

/**
 * Retrieves the active tag id
 */
var getActiveTag = function() {
	var tagId = wrapper.data('tag');

	return tagId;
}

var updateActiveCategory = function(categoryId, baseUrl, title) {
	wrapper
		.find('[data-category-title]')
		.html(title);

	wrapper
		.attr('data-category', categoryId)
		.data('category', categoryId);
};

var getBaseUrl = function() {
	var baseUrl = wrapper.data('baseurl');

	return baseUrl;
}

/**
 * Handle click events on container
 *
 */
$('body').on('click.ed.filter.body', '[data-ed-filter-container]', function(event) {
	event.preventDefault();
	event.stopPropagation();
});


/**
 * Filters that can be used anywhere on the site and fires the real trigger 
 */
$('body').on('click.ed.filter.api', '[data-ed-filter-api]', function(event) {
	event.preventDefault();
	event.stopPropagation();

	
	var element = $(this);
	var type = element.data('ed-filter-api');
	var id = element.data('id');
	var selector = '[data-ed-filter=' + type + '][data-id=' + id + ']';
	var filterItem = filter.filter(selector);

	// If it has already been filtered, it shouldn't toggle it again
	if (filterItem.length <= 0 || filterItem.hasClass('is-active')) {
		return;
	}

	// Simulate the clicking of the filter
	filterItem.click();
});


/**
 * Handles click events for sorting of result set
 */
var sortingWrapper = $('[data-sorting-wrapper]');

$('body').on('click.ed.sorting', '[data-ed-sorting]', function(event) {

	var element = $(this);
	var sort = element.data('ed-sorting');
	var title = element.find('> a').html();

	$('[data-ed-sorting]').removeClass('active');
	element.addClass('active');

	// Update the title of the dropdown button
	sortingWrapper.find('[data-sorting-title]').html(title);

	$('body').trigger('easydiscuss.sorting', [sort, getActiveCategory(), getBaseUrl(), getActiveTag()]);
});



/**
 * Navigation for category filters
 */
$('body').on('click.ed.filter.category.back', '[data-category-back]', function(event) {
	event.preventDefault();
	event.stopPropagation();

	var element = $(this);
	var parent = element.parent('ul');

	// Hide the current submenu layer
	parent.addClass('t-d--none');

	// Activate the previous level
	var previous = parent.parent('[data-category-filter]');
		
	// Get immediate UL 
	var previousParent = previous.parent();
	
	previousParent.removeClass('has-submenu');

	parent.siblings().removeClass('t-d--none');	
	previous.removeClass('t-d--none');
	previous.siblings().removeClass('t-d--none');
});

$('body').on('click.ed.filter.category.navigation', '[data-category-filter] [data-category-nav]', function(event) {

	event.preventDefault();
	event.stopPropagation();

	var element = $(this);
	var parent = element.parent('[data-category-filter]');	
	var categoryId = parent.data('category-filter');
	var categoryContainer = element.parents('[data-ed-category-container]');
	var categoryGroup = categoryContainer.find('[data-ed-category-group]');

	// Since we are traversing, the top level should always have the submenu class
	categoryGroup.addClass('has-submenu');

	this.loading = function() {
		// Set loading state for category dropdown
		categoryGroup.addClass('t-d--none');
		categoryContainer.addClass('is-loading');
	};

	this.doneLoading = function() {
		categoryGroup.removeClass('t-d--none');
		categoryContainer.removeClass('is-loading');		
	}

	this.hideParents = function() {
		parent.siblings().addClass('t-d--none');
	}

	this.hideSiblingsAndSelf = function() {
		element.addClass('t-d--none');
		element.siblings().addClass('t-d--none');
	}

	var self = this;

	var nestedParent = parent.parent('[data-category-nested]');

	if (nestedParent.length > 0) {
		nestedParent.addClass('has-submenu');
	}

	// If they have already been rendered before, do not run any ajax calls
	var nestedList = element.siblings('[data-category-nested]');

	if (nestedList.length > 0) {
		self.hideParents();
		self.hideSiblingsAndSelf();

		nestedList.removeClass('t-d--none');
		return;
	}

	self.loading();

	EasyDiscuss.ajax('site/views/categories/getCategoriesForFilter', {
		parent_id: categoryId,
		activeCategoryId: getActiveCategory()
	})
	.done(function(contents) {
		self.hideParents();
		self.hideSiblingsAndSelf();

		parent.append(contents);

		// Make sure to scroll back to the top
		categoryContainer[0].scrollTop = 0;
	})
	.always(function() {
		self.doneLoading();
	});

});



/**
 * Handles click events for each filter item
 *
 */
$('body').on('click.ed.filter', '[data-ed-filter]', function(event) {

	// event.preventDefault();
	// event.stopPropagation();

	var element = $(this);

	// Could be label, type
	var type = element.data('ed-filter');
	var id = element.data('id');
	var activeCategoryId = getActiveCategory();
	var activeTagId = getActiveTag();

	// Filter items by category
	if (type == 'category') {
		var url = element.data('url');
		var title = $.trim(element.text());

		updateActiveCategory(id, url, title);

		activeCategoryId = id;
	}

	// Set active for main filter. Only 1 main filter can be activated at a given point of time
	if (type == 'main') {
		// Ensure that the user isn't toggling the same filter
		var parent = element.parent();

		if (parent.hasClass('active')) {
			return;
		}

		var mainFilters = $('[data-main-filters] > li');

		mainFilters.removeClass('active');
		parent.addClass('active');
	}

	if (type != 'main' && type != 'category') {
		element.toggleClass('is-active');

		// The id could be an array of selected items
		var items = filter.filter('[data-ed-filter=' + type + '].is-active');
		var id = [];

		items.each(function(i, item) {
			id.push($(item).data('id'));
		});
	}

	$('body').trigger('easydiscuss.render', [type, activeCategoryId, id, element, getBaseUrl(), activeTagId]);
});

//
// Handle click event to remove filter
//
$('body').on('click.ed.remove.filter', '[data-ed-remove-filter]', function(event) {
	event.preventDefault();
	event.stopPropagation();

	var element = $(this);
	var type = element.data('ed-remove-filter');
	var id = element.data('id');
	var activeFilter = filter.filter('[data-ed-filter=' + type + '][data-id=' + id + ']');
	
	if (activeFilter.hasClass('is-active')) {
		activeFilter.click();
		return;
	}

});

});