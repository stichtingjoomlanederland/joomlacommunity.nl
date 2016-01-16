(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
/*
 * jquery.suggest
 * Auto-suggestion plugin for textbox that filters and
 * return matching data entity within a given object dataset.
 *
 * Copyright (c) 2011 Jason Ramos
 * www.stackideas.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

$.require()
 .library(
 	'lookup',
 	'ui/core',
 	'ui/position'
 )
 .template(
 	'suggest/contextmenu',
 	'suggest/contextmenu.item'
 )
 .done(function() {
$.Controller(
	'Foundry.Suggest',
	{
		defaults: {
			lookup: {

				// lookup.inside
				//
				// The dataset where suggestions are.
				// Expects an array of objects.

				inside: [],

				// lookup.within
				//
				// Limit the lookup of keywords within certain properties of the object.
				// Expects an array of string, where the string refers to the name of the property.

				within: [],

				// lookup.exclude
				//
				// The suggestion data to exclude from being shown in the contextMenu.

				exclude: []
			},

			keyword: {

				// keyword.separator
				// (default: ' ')
				//
				// The separator to split up the value in the text field
				// into multiple keywords

				separator: ' ',

				// keyword.includeAsSuggestion
				// (default: false)
				//
				// The value provided in the text field will be
				// included as one of the suggestion

				includeAsSuggestion: false,

				// keyword.clearAfterSelection
				// (default: true)
				//
				// This will empty out the text field after a suggested entry picked.

				clearAfterSelection: true
			},

			contextMenu: {

				display: {

					// contextMenu.display.format
					//
					// An EJS string which represents the text to be displayed in the
					// menu item.
					//
					// The contextMenu will use the "title" property in the given object
					// as the text to be displayed in the menu item. If the "title" property
					// does not exist, it will display the 8 characters surrounding the string
					// from the data buffer (.suggestBuffer) where the keyword is found.
					//
					format: '<@= title @>',

					// contextMenu.display.after
					// (default: 200)
					//
					// Display the contextMenu after a delay in milliseconds.

					after: 500,

					// contextMenu.display.whenFocused
					// (default: true)
					//
					// When the text field's "click" or "focus" event is triggered,
					// the contextMenu will display suggestions based on the
					// current value in the text field.

					whenFocused: true,

					// contextMenu.display.withoutKeyword
					// (default: true)
					//
					// If the contextMenu is triggered (either by click, focus or keystroke)
					// but the text field does not contain any value, the contextMenu
					// will display all suggestions available in the dataset.
					//
					// If the dataset is empty, the contextMenu will not show,
					// except if keyword.includeAsSuggestion is set to true.

					withoutKeyword: true,

					// contextMenu.display.persist
					// (default: false)
					//
					// This is used internally to prevent hiding of contextMenu
					// when contextMenu.hide() is called.

					persist: false,

					// contextMenu.display.hideAfterSelection

					hideAfterSelection: true,

					// contextMenu.display.position
					// (default: {my: 'left top', at: 'left bottom', of: textField})
					//
					// The position where the contextMenu is displayed,
					// relative to the text field.
					//
					// Follows jquery.ui.position's format.

					position: {
						my: 'left top',
						at: 'left bottom',
						collision: 'none'
					},

					css: {}
				},

				// contextMenu.onRenderItem(data)
				// contextMenu.onRenderKeywordItem(keyword)
				//
				// If overriding "@Item" or "@DefaultItem" template to your own is not
				// flexible enough for your customized menu item, you can provide your own
				// rendering function instead.
				//
				// The contextMenu will pass in the suggestion data to the custom function,
				// and expects the custom function to return the rendered template in
				// HTML string or jQuery object.

				onRenderItem: function(data, type){ return undefined; },

				// contextMenu.onSelectItem(data)
				//
				// Function to trigger when an item is selected.
				//
				// The contextMenu will pass in the suggestion data to the function.
				// Function to trigger when the default item is selected.
				//
				// The contextMenu will pass in the value in the text field
				// after it has been sanitized by $.cleanDelimiters.
				//
				onSelectItem: function(data, type){},

				"@menu": 'suggest/contextmenu',

				"@menuItem": 'suggest/contextmenu.item',

				"{menu}": '.suggest-contextmenu',

				"{menuItemGroup}": '.suggest-contextmenu-itemgroup',

				"{menuItem}": '.suggest-contextmenu-item'
			}
		}
	},
	function(self) { return {

		init: function()
		{
			self.textField = self.element;

			// Initialize dataset
			self.dataset(self.options.lookup.inside);

			// Create context menu
			var contextMenu = $($.View(self.options.contextMenu["@menu"],{}));

			contextMenu
				.appendTo('body')
				.implement(
					'Foundry.Suggest.ContextMenu',
					$.extend(self.options.contextMenu, {parent: self}),
					function()
					{
						self.contextMenu = this;
					});
		},

		dataset: function(dataset)
		{
			if (dataset==undefined)
				return self.options.lookup.inside;

			if (!$.isArray(dataset)) return;

			// Reset datamap
			self.datamap = {};

			// Attach metadata to all entries in the dataset.
			$.map(dataset, function(data)
			{
				self.attachMeta(data);
			});

			// Remap it back to lookup options
			self.options.lookup.inside = dataset;

			// TODO: Resume populate after change of dataset
		},

		attachMeta: function(data, customMeta)
		{
			var suggestId = $.uid('suggestId_');

			return self.datamap[suggestId] = $.extend(true,
				data,
				{
					'.suggestId'    : suggestId,
					'.suggestType'  : 'data',
					'.suggestBuffer': self.buffer(data)
				},
				customMeta
			);
		},

		// TODO: Buffering is not required if the lookup context has only one property.
		// TODO: Buffering can be done in $.lookup by turning $.lookup into a class.
		buffer: function(data)
		{
			var buffer = '',
				appendBuffer = function(value)
				{
					if (typeof value==='string' || $.isNumeric(value))
						buffer += value + ' ';
				};

			// If lookup context was given, we will
			// buffer all values within that context.

			var props = self.options.lookup.within;

			if (props.length > 0)
			{
				$.each(props, function(i, prop)
				{
					appendBuffer(data[prop]);
				});

			// Else, go through every property in the data object
			// and buffer all values where possible.

			} else {
				$.each(data, function(key, value)
				{
					if (!key.match('.suggest'))
						appendBuffer(value);
				});
			}

			return $.trim(buffer);
		},

		search: function(keyword)
		{
			if (keyword==undefined)
				keyword = '';

			// Lookup for matching data within the dataset
			return $.lookup(
				$.extend(
					{},
					self.options.lookup,
					{
						using: keyword,
						within: ['.suggestBuffer']
					}
				)
			);
		},

		populate: function(keyword)
		{
			// populate()        - Populate using keyword from text field.
			// populate(keyword) - Populate using provided keyword.
			keyword = $.trimSeparators(keyword || self.textField.val(), self.options.keyword.separator);

			// If no keyword is given, we can only provide suggestions if:
			// - Context menu is configured to display all suggestions
			//   from the dataset when no keyword is given.
			// - Our dataset is not empty.
			if (keyword=='' && (!self.contextMenu.options.display.withoutKeyword ||
				                 self.dataset().length < 1))
			{
				self.contextMenu.hide();
				return;
			}

			var results = self.search(keyword);

			// Include user provided keyword as suggestion if required.
			if (keyword!='' && self.options.keyword.includeAsSuggestion)
			{
				var data = self.attachMeta({title: keyword}, {'.suggestType': 'user'});
				results.push(data);
			}

			// Hide context menu there are no results
			if (results.length < 1)
			{
				self.contextMenu.hide(true);
				return;
			}

			self.contextMenu.show(results);
		},

		exclude: function(suggestId)
		{
			self.options.lookup.exclude.push({'.suggestId': suggestId});
		},

		include: function(suggestId)
		{
			self.options.lookup.exclude = $.grep(
				self.options.lookup.exclude,
				function(rule)
				{
					return rule['.suggestId']==suggestId;
				},
				true);
		},

		focusin: function(textField, event)
		{
			if (self.contextMenu.options.display.whenFocused)
			{
				self.populate();
			}
		},

		focusout: function(textField, event)
		{
		},

		keydown: function(textField, event)
		{
			self.realEnterKey = (event.keyCode==$.ui.keyCode.ENTER);

			// When a user starts typing in the text field,
			// we want to prevent the cursor from intefering
			// with the display of the context menu.
			self.contextMenu.keyboardLock = true;

			switch (event.keyCode)
			{
				case $.ui.keyCode.UP:
					self.contextMenu.activate('previousItem');
					break;

				case $.ui.keyCode.DOWN:
					self.contextMenu.activate('nextItem');
					break;
			}
		},

		keypress: function(textField, event)
		{
			// We need to verify whether or not the user is actually entering
			// an ENTER key or exiting from an IME context menu.
			self.realEnterKey = self.realEnterKey && (event.keyCode==$.ui.keyCode.ENTER);
		},

		keyup: function(textField, event)
		{
			switch(event.keyCode)
			{
				case $.ui.keyCode.ESCAPE:
					self.contextMenu.hide();
					break;

				case $.ui.keyCode.ENTER:
					if (self.realEnterKey)
					{
						self.contextMenu.select();
					} else {
						self.populate();
					}
					break;

				default:
					self.populate();
			}
		}

	}}
);

$.Controller(
	'Foundry.Suggest.ContextMenu',
	{
		defaults: {
			// See default properties in main controller.
		}
	},

	function(self) { return {

		init: function()
		{
			self.parent = self.options.parent;

			self.contextMenu = self.element;

			if (self.options.display.position.of==undefined)
				self.options.display.position.of = self.parent.textField;

			if (self.options.display.css.width==undefined)
				self.options.display.css.width = $(self.options.display.position.of).outerWidth();
		},

		show: function(dataset)
		{
			self.render(dataset);

			self.contextMenu.show(0, function()
			{
				self.contextMenu
					.css(self.options.display.css)
					.position(self.options.display.position);
			});
		},

		hide: function(forceHide)
		{
			// Ensure no suggestions are active as it might get selected unknowingly
			self.menuItem().removeClass('active');

			if (self.parent.restoreTextFieldFocus)
			{
				self.parent.textField.focus();
			}

			if (!self.options.persist || forceHide)
			{
				self.element.hide();
			}
		},

		render: function(dataset)
		{
			var menuItemGroup = self.menuItemGroup();

			// Empty out the suggestions in the contextMenu
			menuItemGroup.empty();

			// Regenerate suggestions
			$.each(dataset, function(i, data)
			{
				var suggestId   = data['.suggestId'],
					suggestType = data['.suggestType'];

				var menuItem = $(
					self.options.onRenderItem(data, suggestType) ||
					$.View(
						self.options["@menuItem"],
						{
							data: data,
							title: data.title // TODO: Integrate contextMenu.display.format
						}
					)
				);

				// We are adding suggestId as both classname and data attribute because:
				// - Locating the DOM element by classname is faster
				// - Retrieving suggestId from DOM element is faster/easier via its data attribute.
				menuItem
					.addClass(suggestId)
					.addClass('suggestType_' + suggestType)
					.data('suggestId', suggestId)
					.appendTo(menuItemGroup);
			});

			// Activate item based on the following order:
			// - Last activated item before list update
			// - User item (from text field)
			// - First item in the list
			var itemToActivate = $(
				self.menuItem('.' + self.lastActivatedItem)[0] ||
				self.menuItem('.suggestType_user')[0] ||
				self.menuItem(':first')
			);

			itemToActivate.addClass('active');

			self.lastActivatedItem = itemToActivate.data('suggestId');
		},

		// @important: Keep the null string in place.
		lastActivatedItem: 'null',

		activate: function(suggestId)
		{
			var menuItem,
				menuItems = self.menuItem(),
				currentMenu = menuItems.filter('.active'),
				currentIndex = menuItems.index(currentMenu);

			switch (suggestId)
			{
				case 'nextItem':
					menuItem = menuItems.eq($.Number.rotate(currentIndex, 0, menuItems.length-1, 1));
					break;

				case 'previousItem':
					menuItem = menuItems.eq($.Number.rotate(currentIndex, 0, menuItems.length-1, -1));
					break;

				default:
					menuItem = self.menuItem('.' + suggestId);
					break;
			}

			if (menuItem.length < 1)
				return;

			menuItems.removeClass('active');

			menuItem.addClass('active');

			self.lastActivatedItem = menuItem.data('suggestId');

			if (self.keyboardLock)
			{
				// TODO: Use a proper plugin that does scrolling & checking for element visiblity.
				//       May need to hack $.scrollTo() for that functionality.

				var topline  = self.contextMenu.scrollTop(),
					baseline = self.contextMenu.height() + topline,
					itemTop  = menuItem.position().top + topline,
					itemBottom = itemTop + menuItem.outerHeight(true);

				if (itemTop <= topline || itemBottom >= baseline)
				{
					menuItem[0].scrollIntoView();
				}
			}
		},

		select: function()
		{
			// Because select() is invoked by various events,
			// the most definitive way of determining the exact
			// menu item is by its active state.

			var menuItem = self.menuItem('.active');

			if (menuItem.length < 1) return;

			var data = self.parent.datamap[menuItem.data('suggestId')];

			// TODO: Integrate contextMenu.display.format
			self.options.onSelectItem(data, data['.suggestType']);

			if (self.parent.options.keyword.clearAfterSelection)
				self.parent.textField.val('');

			self.parent.restoreTextFieldFocus = true;

			if (self.options.display.hideAfterSelection)
				self.hide(true);
		},

		mouseover: function()
		{
			self.options.persist = true;
		},

		mouseout: function()
		{
			self.options.persist = false;
		},

		mousemove: function()
		{
			self.keyboardLock = false
		},

		"{menuItem} mouseover": function(menuItem)
		{
			if (self.keyboardLock==false)
			{
				self.activate(menuItem.data('suggestId'));
			}
		},

		"{menuItem} click": function(menuItem, event)
		{
			self.menuItem()
				.removeClass('active');

			menuItem
				.addClass('active');

			self.select();
		}
	}}
);

    module.resolve();

 });
// module body: end

}; 
// module factory: end

dispatch("suggest")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());