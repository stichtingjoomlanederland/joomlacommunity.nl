ed.define('site/src/forums', ['edq', 'easydiscuss', 'abstract', 'chosen'], function($, EasyDiscuss, Abstract){

	var Forums = new Abstract(function(self) {
		return {
			opts: {
				id: null,
				activefiltertype: null,
				activeSortType: null,
				'{listWrapper}': '[data-list-wrapper]',
				'{itemList}': '[data-list-item]',
				'{pagination}': '[data-forums-pagination]',

				'{sortTab}': '[data-sort-tab]',
				'{filterTab}': '[data-filter-tab]',
				'{indexSortTab}' : '[data-index-sort-filter]'
			},

			init: function(element) {
				this.options.id = self.element.data('id');
			},

			doSort: function(sortType ) {
				this.sortTab()
					.removeClass('active')
					.filterBy("sortType", sortType)
					.addClass("active");

				filterType = this.options.activefiltertype;

				if (filterType == null || filterType == undefined) {
					filterType = $('[data-filter-tab]').filter('.active').data('filterType');
				}

				this.options.activeSortType = sortType;

				this.doFilter(filterType, sortType);
			},

			doFilter: function(filterType, sortType) {

				var listWrapper = self.listWrapper();
				var pagination = self.pagination();

				this.filterTab()
					.removeClass('active')
					.filterBy("filterType", filterType)
					.addClass("active");

				this.options.activefiltertype = filterType;

				if (sortType === undefined) {
					sortType = this.options.activeSortType;
					if (sortType == null) {
						sortType = 'latest';
					}
				}

				// clear existing content.
				self.itemList().html('');
				pagination.html('');

				// Show loading
				listWrapper.addClass('is-loading');

				EasyDiscuss.ajax('site/views/categories/filter', {
					'filter': filterType,
					'sort': sortType,
					'category_id': this.options.id,
				}).done(function(contents, paginationHTML) {

					self.itemList().html(contents);

					// Append pagination
					pagination.html(paginationHTML);

				})
				.always(function(){
					listWrapper.removeClass('is-loading');
				});
			},

			'{indexSortTab} change': function(el, ev) {
				self.doSort(el.val());
			},

			// List item being clicked
			'{filterTab} click' : function(element) {
				var type = element.data('filterType');
				this.doFilter(type, undefined);
			}

		}
	});

	return Forums;
});
