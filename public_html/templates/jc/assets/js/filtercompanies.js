(function ($) {
	jQuery(document).ready(function () {
		listingManager.init({list: collection});

		listingManager.addFilter({key: 'specialisms', field: '.js-specialisms input:checkbox'});
		listingManager.addFilter({key: 'province', field: '.js-province input:checkbox'});
		listingManager.addFilter({key: 'size', field: '.js-size input:checkbox'});

		// allow for a user to search for a specific string in the specific keys of our items
		jQuery('#searchbox').off('keyup').on('keyup', function () {
			listingManager.filterOnSearch(this.value, ['title', 'description', 'city']);
		});

		// make sure the sorting works
		jQuery(".bedrijvengids__sorting input").on('change', function () {
			var sortOptions = this.value.split('-');
			listingManager.setSort(sortOptions[0], sortOptions[1]);

			return false;
		});

		// After changing a filter, we want to filter on searchword again
		jQuery('.bedrijvengids__filter--filters input[type="checkbox"]').on('click', function () {
			listingManager.filterOnSearch(jQuery('#searchbox').val(), ['title', 'description', 'city', 'address']);
		});

		// quick and dirty, get any pre-selected. this must be called before listingManager.display();
		var filters = parseUrlHash();
		for (var f in filters) {
			var i = filters[f].length;
			while (i--) {
				jQuery('.js-' + f + ' [value="' + filters[f][i] + '"]').click();
			}
		}

		// Work-around for checkboxes
		jQuery('.bedrijvengids__filter--filters .list-group-item').on('click', function () {
			jQuery(this).prev('input[type="checkbox"]').click();
		});

		// we display after we have setup everything
		listingManager.display();
	});

	function parseUrlHash() {
		var values = {}, hash = window.location.hash;

		if (hash.length === 0) {
			return values;
		}

		var params = hash.split('#')[1].split('&');

		$.each(params, function (i, param) {
			var f = param.split('=');
			if (f[1] && f[1].length) {
				values[f[0]] = f[1].split(',');
			}
		});

		return values;
	}
}(jQuery));

var listingManager = (function () {
	var config = {
		list: [],
		sort_key: 'title',
		sort_direction: 'random',
		option_template: '#checkbox_template',
		list_item_template: '#collection-template',
		collection_container: '#collection',
		show_per_page: 15,
		pagination_container: '#pagination',
		counter_display: '#total_partners'
	};

	var filters, _aFilters;

	var _manager,
		displayList,
		_bInitialized,
		_itemTemplate,
		_paginationIndex;

	function _init(options) {
		if (!_bInitialized) {
			for (var prop in options) {
				if (options.hasOwnProperty(prop)) {
					config[prop] = options[prop];
				}
			}

			// init default values
			_itemTemplate = jQuery(config.list_item_template).html();
			filters = [];
			_aFilters = [];

			_paginationIndex = 0;

			_manager = this;

			_bInitialized = 1;

			// if list was provided use that
			if (config.list.length) {
				displayList = config.list;
			} else {
				displayList = [];
			}
		}

		return true;
	}

	function _addFilter(itemKey, cssSelector) {
		var $filters = jQuery(cssSelector);
		_aFilters.push({key: itemKey, cssSelector: cssSelector}); // we keep track of our original filters to keep a count

		var i = $filters.length;
		while (i--) {
			jQuery($filters[i]).off('click').on('click', function () {
				// for now we assume we are only dealing with checkboxes
				if (jQuery(this).is(":checked")) {
					_setFilter(itemKey, this.value);
				} else {
					_unsetFilter(itemKey, this.value);
				}
				// reset to begin after changes to filtering
				_paginationIndex = 0;

				_manager.display();
			});
		}
	}

	function _setFilter(key, value) {
		filters.push({key: key, value: value});
	}

	function _unsetFilter(key, value) {
		for (var i = filters.length - 1; i >= 0; i--) {
			if (filters[i].key === key && (typeof value == 'undefined' || filters[i].value.toLowerCase() === value.toLowerCase())) {
				filters.splice(i, 1);
			}
		}
	}

	// thanks to filter.js
	function _renderHTML(str, data) {
		var tmpl = 'var __p=[],print=function(){__p.push.apply(__p,arguments);};' +
			'with(obj||{}){__p.push(\'' +
			str.replace(/\\/g, '\\\\')
				.replace(/'/g, "\\'")
				.replace(/<%-([\s\S]+?)%>/g, function (match, code) {
					return "',escapeStr(" + code.replace(/\\'/g, "'") + "),'";
				})
				.replace(/<%=([\s\S]+?)%>/g, function (match, code) {
					return "'," + code.replace(/\\'/g, "'") + ",'";
				})
				.replace(/<%([\s\S]+?)%>/g || null, function (match, code) {
					return "');" + code.replace(/\\'/g, "'")
						.replace(/[\r\n\t]/g, ' ') + ";__p.push('";
				})
				.replace(/\r/g, '\\r')
				.replace(/\n/g, '\\n')
				.replace(/\t/g, '\\t')
			+ "');}return __p.join('');";

		var func = new Function('obj', tmpl);
		return data ? func(data) : function (data) {
			return func(data)
		};
	}

	function _filterList() {
		// clear our current list
		displayList = [];
		var urlFilters = [];

		// we need to loop over all the items, and check for each that item that all the filters are found
		var i = config.list.length;
		while (i--) {
			var item = config.list[i]; // cache the object for readability

			var f = filters.length;
			var include = true;
			while (f-- && include) {
				var filter = filters[f];  // cache the object for readability

				// i'm running out of ideas here...
				if (typeof urlFilters[filter.key] == 'undefined') {
					urlFilters[filter.key] = filter.value;
				} else if (urlFilters[filter.key].indexOf(filter.value) === -1) {
					urlFilters[filter.key] += ',' + filter.value;
				}

				// is our key an array?
				if (item[filter.key].constructor === Array) {
					if (item[filter.key].includes(filter.value)) {
						continue; // we found it, so check for next filter
					} else {
						include = false; // we didn't find it, so we set include to false which automaticly cancels the loop
					}
				}

				// is our key a string?
				// TODO: use regexp here
			}

			if (include) {
				displayList.push(item);
			}
		}

		var hash = '';
		for (var x in urlFilters) {
			hash += x + '=' + urlFilters[x] + '&';
		}

		if (!hash.length) {
			hash = 'all';
		}

		window.location.hash = hash;
	}

	function _display() {
		// first, clear out our current view
		jQuery(config.collection_container).empty();

		var startIndex = _paginationIndex * config.show_per_page;
		// show items, starting at startIndex, aslong as we have items and we aren't above our limit
		for (var i = startIndex, j = displayList.length; i < j && i < (startIndex + config.show_per_page); i++) {
			jQuery(config.collection_container).append(_renderHTML(_itemTemplate, displayList[i]));
		}

		// display counter if we have one
		if (jQuery(config.counter_display).length) {
			jQuery(config.counter_display).html(displayList.length);
		}

		_displayPagination();

		_aFilters.forEach(function (filterObj) {
			jQuery(filterObj.cssSelector).each(function () {
				var i = displayList.length;
				var count = 0;

				while (i--) {
					var tmp = displayList[i];

					if (tmp[filterObj.key].includes(this.value)) {
						count++;
					}
				}

				jQuery(this).next('label').html(jQuery(this).data('label') + ' (' + count + ')');
			});
		});

	}

	function _hasFilter(key, value) {
		var i = filters.length;
		while (i--) {
			if (filters[i].key === key && (typeof value == 'undefined' || filters[i].value.toLowerCase() === value.toLowerCase())) {
				return true;
			}
		}

		return false;
	}

	function _displayPagination() {
		var $pagination = jQuery(config.pagination_container);
		jQuery('.js-filter').off('click');
		$pagination.empty();

		if (jQuery(config.pagination_container).length && displayList.length > config.show_per_page) {
			var amountButtons = displayList.length / config.show_per_page;

			if (amountButtons > 0) {
				var html = '<nav><ul class="pagination">';

				var bHasPrevious = _paginationIndex > 0;
				html += '<li class="' + (bHasPrevious ? '' : 'inactive') + '"><a href="" class="js-filter js-filter-prev" data-page="prev">&lt;</a></li>';

				for (var i = 0; i < amountButtons; i++) {
					html += '<li class="' + (i == _paginationIndex ? 'active' : '') + '"><a href="" class="js-filter js-filter-page" data-page="' + i + '">' + (i + 1) + '</a>';
				}

				var bHasNext = displayList.length > ((_paginationIndex + 1) * config.show_per_page);
				html += '<li class="' + (bHasNext ? '' : 'inactive') + '"><a href="" class="js-filter js-filter-next" data-page="next">&gt;</a></li>';

				html += '</ul></nav>';

				$pagination.append(html);
			}

			jQuery('.js-filter').off('click').on('click', function () {
				if (!jQuery(this).parent().hasClass('inactive')) {
					var next = jQuery(this).data('page');
					_paginationIndex = (Number.isInteger(next) ? next : (next == 'prev' ? _paginationIndex - 1 : _paginationIndex + 1));

					_displayPagination();
					jQuery('html, body').animate({scrollTop: jQuery('.bedrijvengids__wrapper').offset().top}, 'fast');
					_display();
				}

				return false;
			});
		}
	}

	function _sort(key, order) {
		if (order === 'random') {
			displayList.sort(function (a, b) {
				return 0.5 - Math.random()
			});
		} else {
			displayList.sort(function (a, b) {
				var sASort = a[key].toLowerCase(),
					sBSort = b[key].toLowerCase();

				if (order === 'desc') {
					return sASort > sBSort ? -1 : (sBSort > sASort ? 1 : 0);
				} else {
					return sASort > sBSort ? 1 : (sBSort > sASort ? -1 : 0);
				}
			});
		}

		return displayList;
	}

	return {
		init: _init,

		filter: function () {
			_filterList();
		},

		setFilter: function (key, value) {
			alert('not implemented');
		},

		removeFilter: function (key, value) {
			alert('not implemented');
		},

		display: function () {
			// show all the entries
			if (!config.list.length) {
				return;
			}

			// filter first
			_manager.filter();

			// then sort
			_manager.sort();

			// ready for display
			_display();
		},

		setPagination: function (index) {
			_paginationIndex = index;

			_display();
		},

		addFilter: function (filter) {
			// reset to first page when searching add filter
			_paginationIndex = 0;

			_addFilter(filter.key, filter.field);
		},

		setSort: function (key, order) {
			if (typeof key === 'undefined' || typeof order === 'undefined') {
				return false;
			}
			// reset to first page when sorting changes
			_paginationIndex = 0;

			_sort(key, order);
			_display();
		},

		filterOnSearch: function (word, keys) {
			// reset to first page when searching something else
			_paginationIndex = 0;

			// we call filter here to renew our displayList and apply any filters there might be
			_manager.filter();

			// set to lowercase
			word = word.toLowerCase();

			// we need to loop over all the items, and check for each that item that all the filters are found
			var i = displayList.length;
			while (i--) {
				var item = displayList[i]; // cache the object for readability

				var f = keys.length;
				var include = false;
				while (f-- && !include) {
					var key = keys[f];  // cache the object for readability

					if (item[key].toLowerCase().indexOf(word) !== -1) {
						include = true; // we found it, so we want to include it
					}
				}

				if (!include) {
					displayList.splice(i, 1);
				}
			}

			// handle any sorting of the list
			_manager.sort();

			_display();
		},

		sort: function (key, order) {
			if (typeof key === 'undefined') {
				key = config.sort_key;
				order = config.sort_direction;
			}

			return _sort(key, order);
		},

		setList: function (list) {
			config.list = list;

			_manager.display();
			_displayPagination();
		},

		getDisplayList: function () {
			return displayList;
		}
	}
})();
