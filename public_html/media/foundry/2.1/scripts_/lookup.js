(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

/**
 * jquery.lookup.
 * Object search plugin.
 *
 * Copyright (c) 2012 Jensen Tonne
 * www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

var defaultOptions = {
	using: [],
	inside: {},
	within: [],
	exclude: [],
	match: function(data){}, // Callback to transform the data if required.
	depth: 1 // How deep should lookup dig if nested objects are found.
};

var $match = function(value, keyword)
{
	if (typeof value=='string' || typeof value=='number')
		return value.toUpperCase().indexOf(keyword.toUpperCase()) >= 0;

	return false;
};

$.lookup = function(options)
{
	// If $.lookup(keyword, dataset) was called instead.
	if (typeof options=="string")
	{
		options = {
			using: arguments[0],
			dataset: arguments[1] || {}
		}
	}

	// Sanitize options.
	options = $.extend({}, defaultOptions, options);

	// Don't search the object if there's nothing to search.
	if ($.isEmptyObject(options.inside))
		return {};

	// If keyword given is a string, split spaces and turn
	// them into an array of keywords.
	if (typeof options.using=="string")
		options.using = $.trim(options.using).split(' ');

	// If there are no keywords, return the entire dataset.
	if (options.using.length < 1)
		return options.inside;

	// If the search context is a string, split spaces and
	// turn them into an array of context.
	if (typeof options.within=="string")
		options.within = $.trim(options.within).split(' ');

	// Clone a copy of the dataset so we can work with it
	// without affecting the original dataset.
	var dataset = $.makeArray($.extend(true, {length: options.inside.length}, options.inside));

	// Go through each keyword
	var result = [];
	$.each(options.using, function(i, keyword)
	{
		// TODO: Nested object lookup
		result = result.concat(
			$.grep(dataset, function(data)
			{
				var match = false;

				// If data has been added to the result, then skip it.
				if (data['.added']) return;

				// Exclude object if it matches the pattern given in the exclude list.
				$.each(options.exclude, function(i, pattern)
				{
					for (key in pattern)
					{
						match = $match(data[key], pattern[key]);
						return !match;
					}
				});

				// If the object is excluded, skip & conitnue to the next object in the dataset.
				if (match) return false;

				// If a search context was given, search WITHIN that context.
				if (options.within.length > 0)
				{
					// TODO: Transform matched data with provided match callback
					$.each(options.within, function(i, key)
					{
						match = $match(data[key], keyword);
						return !match;
					});

				// Else, go through every property in the object.
				} else {

					// TODO: Transform matched data with provided match callback
					for (key in data)
					{
						match = $match(data[key], keyword);
						if (match) break;
					}
				}

				// Mark the object as added, so we won't add it again the second time around.
				if (match) data['.added'] = true;

				return match;
			})
		);
	});

	return result;
};

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

dispatch("lookup")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());