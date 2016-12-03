EasyDiscuss.module('filterbar', function($) {
	var module = this;

	EasyDiscuss.Controller('DiscussFilterBar',
		{
			defaultOptions: {

				'catId': null,
				'{allpostsButton}': '.allpostsBtn',
				'{featuredButton}': '.featuredBtn',
				'{newButton}': '.newBtn',
				'{unansweredButton}': '.unansweredBtn'
			}
		},
		function(self) {
			return{
				init: function() {
				},
				'{allpostsButton} click': function() {
					self.filter('allposts');
				},

				'{featuredButton} click': function() {
					self.filter('featured');
				},

				'{newButton} click': function() {
					self.filter('new');
				},

				'{unansweredButton} click': function() {
					self.filter('unanswered');
				},

				filter: function(type ) {

					var id = ((self.options.catId == 0) ? null : self.options.catId);

					EasyDiscuss.ajax('site.views.index.filter' , { args: [type, id] } ,
					{
						beforeSend: function() {

							// Show loading
							discuss.spinner.show('index-loading');

							// Hide the main list item
							$('#dc_list').hide();

							// Hide all paginations during filter
							$('#dc_pagination').hide();

							// Remove all active classes from the child filters.
							$('#filter-links').children().removeClass('active');
						},
						success: function(showFeaturedList , content , sorting , type , nextLimit , paginationContent ) {

							// Show only if necessary
							if (showFeaturedList)
							{
								$('#dc_featured_list').show();
							}

							// Assign the new content
							$('#dc_list').html(content);

							// Update the sorting content
							$('#sort-wrapper').html();

							// Update the pagination type.
							$('#pagination-filter').val(type);

							// Update the pagination limit
							$('#pagination-start').val(nextLimit);

							// Update pagination content
							$('#dc_pagination').html(paginationContent);
						},
						complete: function() {

							// Hide loading once the process is complete
							discuss.spinner.hide('index-loading');

							// Since we hid it earlier, show the list content
							$('#dc_list').show();

							// Show the pagination since we hid it earlier.
							$('#dc_pagination').show();

							// Add active class for the child filter
							$('#filter-links').find('.' + type).addClass('active');
						}
					});
				}
			};
		});
	module.resolve();
});
