ed.require(['edq'], function($){

	$(document).on('click.sidebar.item', '[data-sidebar-parent]', function(event) {

		var item = $(this);
		var hasChild = item.data('childs') > 0 ? true : false;
		
		if (!hasChild) {
			return;
		}

		event.preventDefault();

		var clickedItem = item.parent('[data-sidebar-item]');
		var sidebarItems = $('[data-sidebar-item]');

		// Remove active class on all sidebar items
		sidebarItems.removeClass('active');

		// Since the rest of the sidebar items are now collapsed, show parent badges if there are any
		sidebarItems.find('[data-parent-badge].has-counter')
			.removeClass('t-d--none')
			.addClass('t-d--block');

		// Hide any badges if there are any badge on the parent.
		clickedItem.find('[data-parent-badge]')
			.removeClass('t-d--block')
			.addClass('t-d--none');

		clickedItem.toggleClass('active');
	});

});
