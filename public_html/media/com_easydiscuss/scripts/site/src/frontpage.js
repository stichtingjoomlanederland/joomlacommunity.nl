ed.define('site/src/frontpage', ['edq', 'easydiscuss', 'abstract', 'selectize'], function($, EasyDiscuss, Abstract){

	var Frontpage = new Abstract(function(self) {
		return {
			opts: {
				activefiltertype: null,
				activeSortType: null,
				'{allPostsFilter}'      : '.allPostsFilter',
				'{newPostsFilter}'      : '.newPostsFilter',
				'{unResolvedFilter}'    : '.unResolvedFilter',
				'{resolvedFilter}'      : '.resolvedFilter',
				'{unAnsweredFilter}'    : '.unAnsweredFilter',
				'{sortLatest}'          : '.sortLatest',
				'{sortPopular}'         : '.sortPopular',
				// '{ulList}'              : 'ul.normal',
				'{itemList}'            : '[data-list-item]',
				'{emptyList}'           : 'div.empty',
				'{pagination}'          : '[data-frontpage-pagination]',

				'{sortTab}': '[data-sort-tab]',
				'{filterTab}': '[data-filter-tab]',
				'{indexSortTab}' : '[data-index-sort-filter]',
			},

			init: function(element) {

				this.indexSortTab().selectize({
					onChange: function(item){
						self.doSort(item);
					}
				});
			},

			doSort: function(sortType ) {
				this.sortTab()
					.removeClass('active')
					.filterBy("sortType", sortType)
					.addClass("active");

				filterType = this.options.activefiltertype;

				this.options.activeSortType = sortType;

				this.doFilter(filterType, sortType);
			},

			doFilter: function(filterType, sortType) {

				var list = this.itemList();
				var emptyList = this.emptyList();
				var pagination = this.pagination();

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
				list.children('div.ed-post-item').remove();

				// Show loading
				list.addClass('is-loading');

				// Hide empty
				list.removeClass('is-empty');

				EasyDiscuss.ajax('site/views/index/filter', {
					'filter': filterType,
					'sort'  : sortType,
					'id'    : this.element.data('id'),
					'view'  : this.element.data('view')
				}).done(function(contents, paginationHTML) {

					if (contents.length <= 0) {
						list.addClass('is-empty');
					} else {
						// now append the content.
						list.append(contents);
					}

					// Append pagination
					pagination.html(paginationHTML);

				})
				.always(function(){
					list.removeClass('is-loading');
				});
			},

			// List item being clicked
			'{filterTab} click' : function(element) {
				var type = element.data('filterType');

				this.doFilter(type);
			}

			// '{sortTab} click' : function(element) {
			//     var type = element.data('sortType');

			//     this.doSort(type);
			// },

			// '{sortTab} change' : function(element) {
			//     var type = element.data('sortType');

			//     this.doSort(type);
			// }
		}
	});

	return Frontpage;
});
