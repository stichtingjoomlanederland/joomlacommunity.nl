(function($) {
	'use strict';

	$.fn.rsjoomlafilter = function(options) {
		// Set variabels
		var condition		= typeof options != 'undefined' ? options.condition		: null;
		var events			= typeof options != 'undefined' ? options.events		: null;
		
		// Prepare elements
		$('#rsepro-navbar .dropdown').each(function (i,element) {
			initialize($(element));
		});
		
		// Add custom events on elements
		if (events) {
			$(events).each(function(i,element) {
				$.each(element, function(selector, fnct) {
					$(selector).on('click', function() {
						var func = window[fnct];
						if (typeof func == 'function') {
							func($, $(selector + ' > a').prop('rel'));
						}
					});
				});
			});
		}
		
		// Hide the last condition label
		$('.rsepro-filter-conditions:last-child').css('display','none');
		
		// Add the sort functionality
		$('#rsepro-filter-order ul li a, #rsepro-filter-order-dir ul li a').on('click', function() {
			rsepro_order();
		});
		
		// Append event to the filter button
		$('#rsepro-filter-btn').on('click', function() {
			add_filter(condition);
		});
		
		// Append the add_filter function to the search input
		$('#rsepro-filter').on('keypress', function(e) {
			if (e.which == '13') {	
				add_filter(condition);
			}
		});
		
		// Set the action for the clear filters button
		$('#rsepro-clear-btn').on('click', function () {
			$('.rsepro-filter-filters > li').each(function (i,el) {
				if (condition && $(el).hasClass(condition.replace('.',''))) {
					return;
				}
				
				$(el).remove();
			});
			
			if (condition) {
				$(condition).css('display','none');
			}
			
			submitForm();
		});
		
		// Set the remove filter action
		$('.rsepro-close').each(function (i,el) {
			$(el).on('click', function () {
				remove_filter($(this).parent().parent().prop('id'), condition);
			});
		});
		
		// Do we have the AND/OR condition available
		if (condition) {
			$(condition).find('div > ul > li > a').on('click', function() {
				$(condition).find('div > a span').text($(this).text());
				$('.rsepro-filter-conditions > a').text($(this).text());
				$(condition).find('input').val($(this).prop('rel'));
				submitForm();
			});
		}
	};
	
	// Init the filter select lists
	function initialize(element) {
		$(element).find('ul > li > a').on('click', function() {
			// Update the name of the current filter
			$(element).find('a span').text($(this).text());
			$(element).find('a').first().prop('rel', $(this).prop('rel'));
		});
	}
	
	// Create a HASH for the filter blocks	
	function create_hash(str) {
		var rotate_left = function (n, s) {
			var t4 = (n << s) | (n >>> (32 - s));
			return t4;
		};

		var cvt_hex = function (val) {
			var str = '';
			var i;
			var v;

			for (i = 7; i >= 0; i--) {
				v = (val >>> (i * 4)) & 0x0f;
				str += v.toString(16);
			}
			return str;
		};

		var blockstart;
		var i, j;
		var W = new Array(80);
		var H0 = 0x67452301;
		var H1 = 0xEFCDAB89;
		var H2 = 0x98BADCFE;
		var H3 = 0x10325476;
		var H4 = 0xC3D2E1F0;
		var A, B, C, D, E;
		var temp;

		// utf8_encode
		str = unescape(encodeURIComponent(str));
		var str_len = str.length;

		var word_array = [];
		for (i = 0; i < str_len - 3; i += 4) {
			j = str.charCodeAt(i) << 24 | str.charCodeAt(i + 1) << 16 | str.charCodeAt(i + 2) << 8 | str.charCodeAt(i + 3);
			word_array.push(j);
		}

		switch (str_len % 4) {
			case 0:
				i = 0x080000000;
			break;
			case 1:
				i = str.charCodeAt(str_len - 1) << 24 | 0x0800000;
			break;
			case 2:
				i = str.charCodeAt(str_len - 2) << 24 | str.charCodeAt(str_len - 1) << 16 | 0x08000;
			break;
			case 3:
				i = str.charCodeAt(str_len - 3) << 24 | str.charCodeAt(str_len - 2) << 16 | str.charCodeAt(str_len - 1) << 8 | 0x80;
			break;
		}

		word_array.push(i);

		while ((word_array.length % 16) != 14) {
			word_array.push(0);
		}

		word_array.push(str_len >>> 29);
		word_array.push((str_len << 3) & 0x0ffffffff);

		for (blockstart = 0; blockstart < word_array.length; blockstart += 16) {
			for (i = 0; i < 16; i++) {
				W[i] = word_array[blockstart + i];
			}
			
			for (i = 16; i <= 79; i++) {
			W[i] = rotate_left(W[i - 3] ^ W[i - 8] ^ W[i - 14] ^ W[i - 16], 1);
			}

			A = H0;
			B = H1;
			C = H2;
			D = H3;
			E = H4;

			for (i = 0; i <= 19; i++) {
				temp = (rotate_left(A, 5) + ((B & C) | (~B & D)) + E + W[i] + 0x5A827999) & 0x0ffffffff;
				E = D;
				D = C;
				C = rotate_left(B, 30);
				B = A;
				A = temp;
			}

			for (i = 20; i <= 39; i++) {
				temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff;
				E = D;
				D = C;
				C = rotate_left(B, 30);
				B = A;
				A = temp;
			}

			for (i = 40; i <= 59; i++) {
				temp = (rotate_left(A, 5) + ((B & C) | (B & D) | (C & D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff;
				E = D;
				D = C;
				C = rotate_left(B, 30);
				B = A;
				A = temp;
			}

			for (i = 60; i <= 79; i++) {
				temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff;
				E = D;
				D = C;
				C = rotate_left(B, 30);
				B = A;
				A = temp;
			}

			H0 = (H0 + A) & 0x0ffffffff;
			H1 = (H1 + B) & 0x0ffffffff;
			H2 = (H2 + C) & 0x0ffffffff;
			H3 = (H3 + D) & 0x0ffffffff;
			H4 = (H4 + E) & 0x0ffffffff;
		}

		temp = cvt_hex(H0) + cvt_hex(H1) + cvt_hex(H2) + cvt_hex(H3) + cvt_hex(H4);
		return temp.toLowerCase();
	}
	
	// Add filter
	function add_filter(condition) {
		var li			= $('<li>');
		var div			= $('<div>').addClass('btn-group');
		var close		= $('<i>').addClass('icon-delete');
		var closeLink	= $('<a>', {'href':'javascript:void(0)'}).addClass('btn btn-small rsepro-close').append(close);
		var emptyInp	= false;
		var liid		= '';
		var filterValue	= $('#rsepro-filter-from > a').prop('rel');
		var values		= {};
		
		if (condition) {
			var licond	= $('<li>').addClass('rsepro-filter-conditions').css('display','none');
		}
		
		// Add the filter values
		if (filterValue == 'events' || filterValue == 'description' || filterValue == 'locations' || filterValue == 'categories' || filterValue == 'tags') {
			div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter-from').find('a span').text()));
			div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter-condition').find('a span').text()));
			div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter').val()));
			
			div.append($('<input>', {'type': 'hidden', 'name': 'filter_from[]', 'value': $('#rsepro-filter-from > a').prop('rel')}));
			div.append($('<input>', {'type': 'hidden', 'name': 'filter_condition[]', 'value': $('#rsepro-filter-condition > a').prop('rel')}));
			div.append($('<input>', {'type': 'hidden', 'name': 'search[]', 'value': $('#rsepro-filter').val()}));
			
			liid = $('#rsepro-filter-from > a').prop('rel') + $('#rsepro-filter-condition > a').prop('rel') + $('#rsepro-filter').val();
			
			emptyInp = $('#rsepro-filter').val() == '' && $('#rsepro-filter').css('display') != 'none';
			
		} else if (filterValue == 'featured') {
			if ($('.rsepro-filter-filters input[name="filter_featured[]"]').length) {
				$($('.rsepro-filter-filters input[name="filter_featured[]"]').parent().find('span:eq(1)')).text($('#rsepro-filter-featured').find('a span').text());
				$('.rsepro-filter-filters input[name="filter_featured[]"]').val($('#rsepro-filter-featured > a').prop('rel'));
				submitForm();
				return;
			} else {
				div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter-from').find('a span').text()));
				div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter-featured').find('a span').text()));
				div.append($('<input>', {'type': 'hidden', 'name': 'filter_featured[]', 'value': $('#rsepro-filter-featured > a').prop('rel')}));
			}
			
			liid = 'featured';
			
		} else if (filterValue == 'status') {
			div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter-from').find('a span').text()));
			div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter-status').find('a span').text()));
			div.append($('<input>', {'type': 'hidden', 'name': 'filter_status[]', 'value': $('#rsepro-filter-status > a').prop('rel')}));
			
			liid = 'status' + $('#rsepro-filter-status > a').prop('rel');
			
		} else if (filterValue == 'child') {
			if ($('.rsepro-filter-filters input[name="filter_child[]"]').length) {
				$($('.rsepro-filter-filters input[name="filter_child[]"]').parent().find('span:eq(1)')).text($('#rsepro-filter-child').find('a span').text());
				$('.rsepro-filter-filters input[name="filter_child[]"]').val($('#rsepro-filter-child > a').prop('rel'));
				submitForm();
				return;
			} else {
				div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter-from').find('a span').text()));
				div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter-child').find('a span').text()));
				div.append($('<input>', {'type': 'hidden', 'name': 'filter_child[]', 'value': $('#rsepro-filter-child > a').prop('rel')}));
			}
			
			liid = 'child';
		} else if (filterValue == 'start') {
			emptyInp = $('#start_date').val() == '';
			
			if (!emptyInp) {
				if ($('.rsepro-filter-filters input[name="filter_start[]"]').length) {
					$($('.rsepro-filter-filters input[name="filter_start[]"]').parent().find('span:eq(1)')).text($('#start_date').val());
					$('.rsepro-filter-filters input[name="filter_start[]"]').val($('#start_date').val());
					submitForm();
					return;
				} else {
					div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter-from').find('a span').text()));
					div.append($('<span>').addClass('btn btn-small').text($('#start_date').val()));
					div.append($('<input>', {'type': 'hidden', 'name': 'filter_start[]', 'value': $('#start_date').val()}));
				}
			}
			
			liid = 'start_date';
			
		} else if (filterValue == 'end') {
			emptyInp = $('#end_date').val() == '';
			
			if (!emptyInp) {
				if ($('.rsepro-filter-filters input[name="filter_end[]"]').length) {
					$($('.rsepro-filter-filters input[name="filter_end[]"]').parent().find('span:eq(1)')).text($('#end_date').val());
					$('.rsepro-filter-filters input[name="filter_end[]"]').val($('#end_date').val());
					submitForm();
					return;
				} else {
					div.append($('<span>').addClass('btn btn-small').text($('#rsepro-filter-from').find('a span').text()));
					div.append($('<span>').addClass('btn btn-small').text($('#end_date').val()));
					div.append($('<input>', {'type': 'hidden', 'name': 'filter_end[]', 'value': $('#end_date').val()}));
				}
			}
			
			liid = 'end_date';
		}
		
		// Set the ID 
		var hashID = create_hash(liid);
		
		// If the filter exists or the input is empty, do not set the filter
		if ($('.rsepro-filter-filters li#'+hashID).length != 0 || emptyInp) {
			return;
		}
		
		// Set the action of the close button
		closeLink.on('click', function() {
			remove_filter(hashID, condition);
		});
		
		div.append(closeLink);
		
		li.prop('id',hashID);
		li.append(div);
		
		if (condition) {
			licond.append($('<a>').addClass('btn btn-small').text($(condition).find('div > a span').text()));
		}
		
		$('.rsepro-filter-filters').append(li);
		
		if (condition) {
			$('.rsepro-filter-filters').append(licond);
		}
		
		// Reset the search value
		$('#rsepro-filter').val('');
		
		var count_filters = $('.rsepro-filter-filters > li:not([class])').length;
		
		if (count_filters > 1) {
			
			if (condition) {
				$(condition).css('display','');
			}
			
			$('.rsepro-filter-conditions').css('display','');
			$('.rsepro-filter-conditions:last-child').css('display','none');
		}
		
		submitForm();
	}
	
	// Remove filter
	function remove_filter(id, condition) {
		var cond = $('#'+id).next();
		var item = $('#'+id);
		
		cond.remove();
		item.remove();
		$('.rsepro-filter-conditions:last-child').css('display','none');
		
		if ($('.rsepro-filter-filters > li:not([class])').length <= 1) {
			if (condition) {
				$(condition).css('display','none');
			}
		}
		
		submitForm();
	}
	
	// Set the order by function
	function rsepro_order() {
		$('#filter_order').val($('#rsepro-filter-order > a').prop('rel'));
		$('#filter_order_Dir').val($('#rsepro-filter-order-dir > a').prop('rel'));
		submitForm();
	}
	
	// Submit the form
	function submitForm() {
		if ($('input[name=limitstart]').length) {
			$('input[name=limitstart]').val(0);
		}
		
		$('#task').val('');
		$('#adminForm').submit();
	}
	
})(jQuery);

function rsepro_select($, val) {
	if (val == 'events' || val == 'description' || val == 'locations' || val == 'categories' || val == 'tags') {
		$('#rsepro-filter-condition').css('display','');
		$('#rsepro-search').css('display','');
		$('#rsepro-filter-featured').css('display','none');
		$('#rsepro-filter-child').css('display','none');
		$('#rsepro-filter-status').css('display','none');
		$('#rsepro-filter-start').css('display','none');
		$('#rsepro-filter-end').css('display','none');
	} else if (val == 'featured') {
		$('#rsepro-filter-condition').css('display','none');
		$('#rsepro-search').css('display','none');
		$('#rsepro-filter-featured').css('display','');
		$('#rsepro-filter-child').css('display','none');
		$('#rsepro-filter-status').css('display','none');
		$('#rsepro-filter-start').css('display','none');
		$('#rsepro-filter-end').css('display','none');
	} else if (val == 'status') {
		$('#rsepro-filter-condition').css('display','none');
		$('#rsepro-search').css('display','none');
		$('#rsepro-filter-featured').css('display','none');
		$('#rsepro-filter-child').css('display','none');
		$('#rsepro-filter-status').css('display','');
		$('#rsepro-filter-start').css('display','none');
		$('#rsepro-filter-end').css('display','none');
	} else if (val == 'child') {
		$('#rsepro-filter-condition').css('display','none');
		$('#rsepro-search').css('display','none');
		$('#rsepro-filter-featured').css('display','none');
		$('#rsepro-filter-child').css('display','');
		$('#rsepro-filter-status').css('display','none');
		$('#rsepro-filter-start').css('display','none');
		$('#rsepro-filter-end').css('display','none');
	} else if (val == 'start') {
		$('#rsepro-filter-condition').css('display','none');
		$('#rsepro-search').css('display','none');
		$('#rsepro-filter-featured').css('display','none');
		$('#rsepro-filter-child').css('display','none');
		$('#rsepro-filter-status').css('display','none');
		$('#rsepro-filter-start').css('display','');
		$('#rsepro-filter-end').css('display','none');
	} else if (val == 'end') {
		$('#rsepro-filter-condition').css('display','none');
		$('#rsepro-search').css('display','none');
		$('#rsepro-filter-featured').css('display','none');
		$('#rsepro-filter-child').css('display','none');
		$('#rsepro-filter-status').css('display','none');
		$('#rsepro-filter-start').css('display','none');
		$('#rsepro-filter-end').css('display','');
	}
}