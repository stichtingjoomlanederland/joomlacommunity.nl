ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

$(document).ready(function() {


	$('[data-category-back]').on('click', function(e) {
		event.preventDefault();
		event.stopPropagation();

		var element = $(this);
		var parent = element.parent('div');

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

	$('[data-mod-category-nav]').on('click.category.nav', function(e) { 
		event.preventDefault();
		event.stopPropagation();

		

		var element = $(this).parents('[data-mod-category-item]');
		var parent = element.parent('[data-category-filter]');
		var categoryContainer = element.parents('[data-ed-category-container]');
		var categoryGroup = categoryContainer.find('[data-ed-category-group]');

		// Since we are traversing, the top level should always have the submenu class
		categoryGroup.addClass('has-submenu');

		this.hideParents = function() {
			parent.siblings().addClass('t-d--none');
		}

		this.hideSiblingsAndSelf = function() {
			element.addClass('t-d--none');
		}

		var self = this;

		var nestedParent = parent.parent('[data-category-nested]');

		if (nestedParent.length > 0) {
			nestedParent.addClass('has-submenu');
		}

		var nestedList = element.siblings('[data-category-nested]');

		if (nestedList.length > 0) {
			self.hideParents();
			self.hideSiblingsAndSelf();

			nestedList.removeClass('t-d--none');
			return;
		}
	});

});

});