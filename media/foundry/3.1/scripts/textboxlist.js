(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
$.require() 
 .script("autosize.input","scrollTo") 
 .done(function() { 
var exports = function() { 

// Constants
var KEYCODE = {
	BACKSPACE: 8,
	COMMA: 188,
	DELETE: 46,
	DOWN: 40,
	ENTER: 13,
	ESCAPE: 27,
	LEFT: 37,
	RIGHT: 39,
	SPACE: 32,
	TAB: 9,
	UP: 38
};


// Templates
$.template("textboxlist/item", '<div class="textboxlist-item" data-textboxlist-item><span class="textboxlist-itemContent" data-textboxlist-itemContent>[%== html %]</span><div class="textboxlist-itemRemoveButton" data-textboxlist-itemRemoveButton><i class="ies-cancel-2"></i></a></div>');
$.template("textboxlist/itemContent", '[%= title %]<input type="hidden" name="[%= name %]" value="[%= id %]"/>');

$.Controller("Textboxlist",
	{
		pluginName: "textboxlist",
		hostname: "textboxlist",

		defaultOptions: {

			view: {
				item: 'textboxlist/item',
				itemContent: 'textboxlist/itemContent'
			},

			plugin: {},

			// Options
			name: "items[]",
			unique: true,
			caseSensitive: false,
			max: null,

			// Events
			filterItem: null,

			"{item}"            : "[data-textboxlist-item]",
			"{itemContent}"     : "[data-textboxlist-itemContent]",
			"{itemRemoveButton}": "[data-textboxlist-itemRemoveButton]",
			"{textField}"       : "[data-textboxlist-textField]"
		}
	},
	function(self) { return {

		init: function() {

			var textField = self.textField();

			// Make textfield expandable
			textField.autosizeInput();

			// Keep the original placeholder text value
			textField.data("placeholderText", textField.attr("placeholder"));

			// Data attribute override options
			var name = textField.data("textboxlistName");
			if (name) {
				self.options.name = name;
			}

			// Go through existing item
			// and reconstruct item data.
			self.item().each(function(){

				var item = $(this),
					itemContent = item.find(self.itemContent.selector);

				self.createItem({

					id: item.data("id") || (function(){
						var id = $.uid("item-");
						item.data("id", id);
						return id;
					})(),

					title: item.data("title") || $.trim(itemContent.text()),

					html: itemContent.html()
				});
			});

			// Determine if there's autocomplete
			if (self.options.plugin.autocomplete || self.element.data("query")) {
				self.addPlugin("autocomplete");
			}

			// Prevent form submission
			self.on("keypress", self.textField(), function(event){
				if (event.keyCode==KEYCODE.ENTER) return event.preventDefault();
			});
		},

		setLayout: function() {

			var textField = self.textField(),
				placeholderText = textField.data("placeholderText");

			// Don't show placeholder if there are items.
			if (self.item().length > 0) {
				placeholderText = "";
			}

			textField
				.attr("placeholder", placeholderText)
				.data("autosizeInputInstance")
				.update();
		},

		enable: function() {
			self.element.removeClass("disabled");
			self.textField().enabled(true);
		},

		disable: function() {
			self.element.addClass("disabled");
			self.textField().disabled(true);
		},

		items: {},

		itemsByTitle: {},

		getItemKey: function(title){

			return (self.options.caseSensitive) ? title : title.toLowerCase();
		},

		filterItem: function(item) {

			var options = self.options;

			// Use custom filter if provided
			var filterItem = options.filterItem;

			if ($.isFunction(filterItem)) {
				item = filterItem.call(self, item);
			}

			var items = self.itemsByTitle;

			// If item is a string,
			if ($.isString(item) && item!=="") {

				var title = item,
					key = self.getItemKey(title);

				item =
					(items.hasOwnProperty(key)) ?

						// Get existing item
						self.itemsByTitle[key] :

						{
							id   : $.uid("item-"),
							title: title,
							key  : self.getItemKey(title)
						}
			}

			// This is for the name attribute for the hidden input
			item.name = item.name || self.options.name;

			// If item content is not created, then make one.
			item.html = item.html || self.view.itemContent(true, item);

			return item;
		},

		createItem: function(item) {

			// Create key for item
			item.key = self.getItemKey(item.title);

			// Store to items object
			self.items[item.id] = item;

			// Store to itemsByTitle object
			self.itemsByTitle[item.key] = item;
		},

		deleteItem: function(id) {

			var item = self.items[id];

			// Remove from items object
			delete self.items[id];

			// Remove from itemsByTitle object
			var key = (self.options.caseSensitive) ? item.title : item.title.toLowerCase();
			delete self.itemsByTitle[key];
		},

		addItem: function(item) {

			// Don't add invalid item
			if (!item) return;

			var options = self.options;

			// If we reached the maximum number of items, skip.
			var max = options.max;
			if (max!==null && self.item().length>=max) return;

			// Filter item
			item = self.filterItem(item);

			// At this point, if item if not an object, skip.
			if (!$.isPlainObject(item)) return;

			var itemEl,
				existingItemEl = self.item().filterBy("id", item.id);

			// If items should be unique,
			// and this item has already been added to the list
			if (options.unique && existingItemEl.length > 0) {

				// then use existing item.
				itemEl = existingItemEl;
			}

			// Else create a new item
			if (!itemEl) {

				itemEl =
					self.view.item(item)
						.addClass(item.className || "")
						.attr("data-id", item.id);
			}

			self.createItem(item);

			// Add item on to the list
			itemEl.insertBefore(self.textField());

			self.trigger("addItem", [item]);

			return item;
		},

		removeItem: function(id) {

			var item = self.items[id];

			// Remove item from the list
			self.item().filterBy("id", id)
				.remove();

			self.deleteItem(id);

			self.trigger("removeItem", [item]);
		},

		clearItems: function() {

			self.item().each(function(){
				self.removeItem($(this).data("id"));
			});
		},

		getAddedItems: function() {

			var addedItems = [];

			self.item().each(function(){

				var item = $(this),
					id = item.data("id");

				addedItems.push(self.items[id]);
			});

			return addedItems;
		},

		"{self} addItem": function() {

			self.setLayout();
		},

		"{self} removeItem": function() {

			self.setLayout();
		},

		"{itemRemoveButton} click": function(button) {

			var item = button.parents(self.item.selector);

			self.removeItem(item.data("id"));
		},

		"{textField} keydown": function(textField, event)
		{
			var keyCode = event.keyCode;

			textField.data("realEnterKey", keyCode==KEYCODE.ENTER);
		},

		"{textField} keypress": function(textField, event)
		{
			var keydownIsEnter = textField.data("realEnterKey"),

				// When a person enters the IME context menu,
				// the keyCode returned during keypress will
				// not be the enter keycode.
				keypressIsEnter = event.keyCode==KEYCODE.ENTER;

			textField.data("realEnterKey", keydownIsEnter && keypressIsEnter);

			var keyword = $.trim(self.textField().val());

			switch (event.keyCode) {

				// Add new item
				case KEYCODE.ENTER:

					if (textField.data("realEnterKey")) {

						var event = self.trigger("useItem", [keyword]),
							item = event.item;

						// If event handler did not decorate item,
						// use keyword as item.
						if (item===undefined) {
							item = keyword;
						}

						// If item was converted into a null/false object,
						// this means the custom keyup event wants to "preventDefault".
						if (item===false || item===null) return;

						self.addItem(item);

						// and clear text field.
						textField.val("");
					}
					break;
			}
		},

		"{textField} keyup": function(textField, event)
		{
			var item = $.trim(self.textField().val());

			// Optimization for compiler
			var canRemoveItemUsingBackspace = "canRemoveItemUsingBackspace";

			switch (event.keyCode) {

				// Remove last added item
				case KEYCODE.BACKSPACE:

					// If the text field is empty
					if (item==="") {

						// If this is the first time pressing the backspace key
						if (!self[canRemoveItemUsingBackspace]) {

							// Allow removal of item for subsequent backspace
							self[canRemoveItemUsingBackspace] = true;

						// If this is the subsequent time pressing the backspace key
						} else {

							// Look for the item before it
							var prevItem = textField.prev(self.item.selector);

							// If the item before it exists,
							if (prevItem.length > 0) {

								// Remove the item.
								self.removeItem(prevItem.data("id"));
							}
						}
					}
					break;

				default:
					// Reset backspace removal state
					self[canRemoveItemUsingBackspace] = false;
					break;
			}
		},

		"{self} click": function(el, event) {

			var textField = self.textField();

			if (!textField.is(event.target)) {
				textField.focus();
			}
		},

		"{textField} focusin": function() {

			if (self.activated) return;

			self.activated = true;
			self.trigger("textboxlistActivate");
		},

		"{self} mousedown": function() {
			self.focusing = true;
		},

		"{self} mouseup": function() {
			self.focusing = false;
		},

		"{self} focusout": function() {

			if (self.focusing) return;

			self.activated = false;

			self.deactivateTimer =
				setTimeout(function(){
					if (self.activated) return;
					self.trigger("textboxlistDeactivate");
				}, 1);
		}
	}}
);

$(document)
	.on('click.textboxlist.data-api', '[data-textboxlist]', function(event){
		$(this).addController($.Controller.TextboxList).textField().focus();
	})
	.on('focus.textboxlist.data-api', '[data-textboxlist] [data-textboxlist-textField]', function(event){
		$(this).parents("[data-textboxlist]").addController($.Controller.TextboxList);
	});
// Textboxlist ends

// Autocomplete starts
$.template("textboxlist/menu", '<div class="textboxlist-autocomplete" data-textboxlist-autocomplete><div class="textboxlist-autocomplete-inner" data-textboxlist-autocomplete-viewport><div class="textboxlist-autocomplete-loading" data-textboxlist-autocomplete-loading></div><div class="textboxlist-autocomplete-empty" data-textboxlist-autocomplete-empty></div><ul class="textboxlist-menu" data-textboxlist-menu></ul></div></div>');
$.template("textboxlist/menuItem", '<li class="textboxlist-menuItem" data-textboxlist-menuItem>[%== html %]</li>');
$.template("textboxlist/loadingHint", '<i class="textboxlist-autocomplete-loading-indicator"></i>');
$.template("textboxlist/emptyHint", '<span class="textboxlist-autocomplete-empty-text">No items found.</span>');

$.Controller("Textboxlist.Autocomplete",
{
	defaultOptions: {

		view: {
			menu: "textboxlist/menu",
			menuItem: "textboxlist/menuItem",
			loadingHint: "textboxlist/loadingHint",
			emptyHint: "textboxlist/emptyHint"
		},

		cache: true,

		minLength: 1,

		limit: 10,

		highlight: true,

		caseSensitive: false,

		exclusive: false,

		// Accepts url, function or array of objects.
		// If function, it should return a deferred object.
		query: null,

		position: {
			my: 'left top',
			at: 'left bottom',
			collision: 'none'
		},

		filterItem: null,

		showEmptyHint: false,

		showLoadingHint: false,

		"{menu}": "[data-textboxlist-menu]",
		"{menuItem}": "[data-textboxlist-menuItem]",
		"{viewport}": "[data-textboxlist-autocomplete-viewport]",
		"{loadingHint}": "[data-textboxlist-autocomplete-loading]",
		"{emptyHint}": "[data-textboxlist-autocomplete-empty]"
	}
},
function(self) { return {

	init: function() {

		// Destroy controller
		if (!self.element.data(self.Class.fullName)) {

			self.destroy();

			// And reimplement on the context menu we created ourselves
			self.view.menu()
				.appendTo("body")
				.data(self.Class.fullName, true)
				.addController(self.Class, self.options);

			return;
		}

		var textboxlist = self.textboxlist;

		textboxlist.autocomplete = self;
		textboxlist.pluginInstances["autocomplete"] = self;

		// Set the position to be relative to the textboxlist
		self.options.position.of = self.textboxlist.element;

		self.initQuery();

		// Loading hint
		self.view.loadingHint()
			.appendTo(self.loadingHint());

		// Empty hint
		self.view.emptyHint()
			.appendTo(self.emptyHint());

		// Only reattach element when autocomplete is needed.
		self.element.detach();
	},

	initQuery: function() {

		// Determine query method
		var query = self.options.query || self.textboxlist.element.data("query");

		// TODO: Wrap up query options and pass to query URL & query function.

		// Query URL
		if ($.isUrl(query)) {

			var url = query;

			self.query = function(keyword){
				return $.ajax(url + keyword);
			}

			return;
		}

		// Query function
		if ($.isFunction(query)) {

			var func = query;

			self.query = function(keyword) {
				return func.call(self, keyword);
			}

			return;
		}

		// Query dataset
		if ($.isArray(query)) {

			var dataset = query;

			self.query = function(keyword) {

				var task = $.Deferred(),
					keyword = keyword.toLowerCase();

				// Fork this process
				// so it won't choke on large dataset.
				setTimeout(function(){

					var result = $.grep(dataset, function(item){
						return item.title.toLowerCase().indexOf(keyword) > -1;
					});

					task.resolve(result);

				}, 0);

				return task;
			}

			return;
		}
	},

	setLayout: function() {

		if (!self.hidden) {

			self.element
				.css({
					opacity: 1,
					width: self.textboxlist.element.outerWidth()
				})
				.position(self.options.position);
		}
	},

	"{window} resize": $.debounce(function() {
		self.element.css("opacity", 0);
		self.setLayout();
	}, 250),

	"{window} scroll": $.debounce(function() {
		self.element.css("opacity", 0);
		self.setLayout();
	}, 250),

	"{window} dialogTransitionStart": function() {
		self.hidden = true;
		self.element.css("opacity", 0);
	},

	"{window} dialogTransitionEnd": function() {
		self.hidden = false;
		self.setLayout();
	},

	show: function() {

		clearTimeout(self.sleep);

		self.element
			.appendTo("body")
			.show();

		self.hidden = false;

		self.setLayout();
	},

	hide: function() {

		self.element.hide();

		var menuItem = self.menuItem(),
			activeMenuItem = menuItem.filter(".active");

		if (activeMenuItem.length > 0) {
			self.lastItem = {
				keyword: $.trim(self.textboxlist.textField().val()),
				item   : activeMenuItem.data("item")
			};
		}

		menuItem.removeClass("active");

		self.render.reset();

		self.hidden = true;

		// Clear any previous sleep timer first
		clearTimeout(self.sleep);

		// If no activity within 3000 seconds, detach myself.
		self.sleep = setTimeout(function(){
			self.element.detach();
		}, 3000);
	},

	queries: {},

	populated: false,

	populate: function(keyword) {

		self.populated = false;

		// Remove loading class
		var element = self.element,
			options = self.options;

		// Remove both loading & empty class
		element.removeClass("loading empty");

		if (options.showLoadingHint) {
			self.hide();
		}

		// Trigger populate event
		// If the populate event returns a modified keyword, use it.
		var event = self.trigger("populateKeyword", [keyword]);
		if (event.keyword) { keyword = event.keyword };


		var key = (options.caseSensitive) ? keyword : keyword.toLowerCase(),
			query = self.queries[key];

		var newQuery = !$.isDeferred(query) || !self.options.cache,

			runQuery = function(){

				// Show loading hint
				if (options.showLoadingHint) {
					element.addClass("loading");
					self.show();
				}

				// Query the keyword if:
				// - The query hasn't been made.
				// - The query has been rejected.
				if (newQuery || (!newQuery && query.state()=="rejected")) {

					query = self.queries[key] = self.query(keyword);
				}

				// When query is done, render items;
				query
					.done(
						self.render(function(items){
							return [items, keyword];
						})
					)
					.fail(function(){
						self.hide();
					})
					.always(function(){
						element.removeClass("loading");
					});

				// Trigger query event
				self.trigger("queryKeyword", [query, keyword]);
			}

		// If this is a new query
		if (newQuery) {

			// Don't run until we are sure that the user is finished typing
			clearTimeout(self.queryTask);
			self.queryTask = setTimeout(runQuery, 250);

		// Else run it immediately
		} else {
			runQuery();
		}
	},

	populateTask: null,

	populateFromTextField: function() {

		clearTimeout(self.populateTask);

		self.populateTask = setTimeout(function(){

			var textField = self.textboxlist.textField(),
				keyword = $.trim(textField.val());

			// If no keyword given or keyword doesn't meet minimum query length, stop.
			if (keyword==="" || (keyword.length < self.options.minLength)) {

				self.hide();

			// Else populate suggestions.
			} else {

				self.populate(keyword);
			}
		}, 1);
	},

	render: $.Enqueue(function(items, keyword){

		// If items passed in isn't an array,
		// fake an empty array.
		if (!$.isArray(items)) { items = [] };

		// Get textboxlist
		var textboxlist = self.textboxlist,
			autocomplete = self,
			element = self.element,
			options = self.options,
			menu = self.menu();

		// If there are no items, hide menu.
		if (items.length < 1) {

			// If we are supposed to show an empty hint
			if (options.showEmptyHint) {

				// Clear out menu
				menu.empty();

				// Add empty class
				element.addClass("empty");

				// Trigger renderMenu event
				textboxlist.trigger("renderMenu", [menu, autocomplete, textboxlist]);

				// Show menu
				self.show();

			// Just hide straight away
			} else {

				self.hide();
			}

			return;
		}

		// Remove empty class
		element.removeClass("empty");

		// Generate menu items
		if (!options.cache || menu.data("keyword")!==keyword) {

			// Clear out menu items
			menu.empty();

			$.each(items, function(i, item){

				textboxlist.trigger("filterItem", [item, autocomplete, textboxlist]);

				// Deprecated
				var filterItem = options.filterItem;
				if ($.isFunction(filterItem)) {
					item = filterItem.call(self, item, keyword);
				}

				// If the item is not an object,
				// or item should be discarded, stop.
				if (!$.isPlainObject(item) || item.discard) return;

				var html = item.menuHtml || item.title;

				self.view.menuItem({html: html})
					.addClass(item.className || "")
					.data("item", item)
					.appendTo(menu);
			});

			menu.data("keyword", keyword);
		}

		// Get menu Items
		var menuItems = self.menuItem();

		// Trigger filterMenu event
		textboxlist.trigger("filterMenu", [menu, menuItems, autocomplete, textboxlist]);

		// If menu is empty, toggle empty classname
		if (menuItems.filter(":not(.hidden)").length < 1) {

			element.addClass("empty");

			// If we shouldn't show an empty hint
			if (!options.showEmptyHint) {

				// Hide menu straightaway
				return self.hide();
			}
		}

		// If we only allow adding item from suggestions
		if (options.exclusive) {

			// Automatically select the first item
			self.menuItem(":not(.hidden):first").addClass("active");
		}

		// Trigger renderMenu event
		textboxlist.trigger("renderMenu", [menu, autocomplete, textboxlist]);

		self.show();
	}),

	"{textboxlist.textField} keydown": function(textField, event) {

		// Prevent autocomplete from falling asleep.
		clearTimeout(self.sleep);

		// Get active menu item
		var activeMenuItem = self.menuItem(".active:not(.hidden)");

		if (activeMenuItem.length < 1) {
			activeMenuItem = false;
		}

		var textField = self.textboxlist.textField();

		switch (event.keyCode) {

			// If up key is pressed
			case KEYCODE.UP:

				// Deactivate all menu item
				self.menuItem().removeClass("active");

				// If no menu items are activated,
				if (!activeMenuItem) {

					// activate the last one.
					self.menuItem(":not(.hidden):last").addClass("active");

				// Else find the menu item before it,
				} else {

					// and activate it.
					activeMenuItem.prev(self.menuItem.selector + ':not(.hidden)')
						.addClass("active");
				}

				// Prevent up/down keys from changing textfield cursor position.
				event.preventDefault();
				break;

			// If down key is pressed
			case KEYCODE.DOWN:

				// Deactivate all menu item
				self.menuItem().removeClass("active");

				// If no menu items are activated,
				if (!activeMenuItem) {

					// activate the first one.
					self.menuItem(":not(.hidden):first").addClass("active");

				// Else find the menu item after it,
				} else {

					// and activate it.
					activeMenuItem.next(self.menuItem.selector + ':not(.hidden)')
						.addClass("active");
				}

				// Prevent up/down keys from changing textfield cursor position.
				event.preventDefault();
				break;

			// If escape is pressed,
			case KEYCODE.ESCAPE:

				// hide menu.
				self.hide();
				break;

			// Don't do anything when enter is pressed.
			case KEYCODE.ENTER:
				break;

			default:
				self.populateFromTextField();
				break;
		}

		// Get newly activated item
		var activeMenuItem = self.menuItem(".active:not(.hidden)");

		// If we are reaching the end of the menu cycle,
		// select textfield as a visual indication, else
		// unselect textfield and let the menu item appear selected.
		if (activeMenuItem.length < 1) {
			return;
			// textField.selectAll(); return;
		} else {
			//textField.unselect();
		}

		// Scroll menu viewport if it is out of visible area.
		self.viewport().scrollIntoView(activeMenuItem);
	},

	"{textboxlist} textboxlistActivate": function(textboxlist) {

		self.populateFromTextField();
	},

	"{textboxlist} textboxlistDeactivate": function(textboxlist) {

		// Allow user to select menu first
		setTimeout(function(){
			self.hide();
		}, 150);
	},

	"{textboxlist} destroyed": function() {

		self.element.remove();
	},

	"{textboxlist} useItem": function(textField, event, keyword) {

		// If we only pick items exclusively from menu,
		// set item to false first. This prevents any
		// random keyword from being added to the list.
		var exclusive = self.options.exclusive;

		if (exclusive) event.item = false;

		// If menu is not visible
		if (self.hidden) {

			// and we are in exclusive mode
			// and the last item before we hide the menu
			// matches the current keyword,
			var lastItem = self.lastItem;

			if (exclusive && lastItem && lastItem.keyword==keyword) {

				// then we will automatically use the last
				// item as the item to be added to the list.
				event.item = lastItem.item;
			}

			return;
		}

		// If there are activated items
		var activeMenuItem = self.menuItem(".active");

		if (activeMenuItem.length > 0) {

			// get the item data,
			var item = activeMenuItem.data("item");

			// and return the item data to the textboxlist.
			event.item = item;
		}

		// Hide the menu
		self.hide();
	},

	"{menuItem} mousedown": function() {

		self.textboxlist.focusing = true;
	},

	"{menuItem} mouseup": function() {

		self.textboxlist.focusing = false;
	},

	"{menuItem} click": function(menuItem) {

		// Hide context menu
		self.hide();

		// Add item
		var item = menuItem.data("item");
		self.textboxlist.addItem(item);

		// Get text field & clear text field
		var textField = self.textboxlist.textField().val("");

		// Refocus text field
		setTimeout(function(){

			// Due to event delegation, this needs to be slightly delayed.
			textField.focus();
		}, 150);
	},

	"{menuItem} mouseover": function(menuItem) {

		self.menuItem().removeClass("active");

		menuItem.addClass("active");
	},

	"{menuItem} mouseout": function(menuItem) {

		self.menuItem().removeClass("active");
	}
}}
);
// Autocomplete ends
}; 

exports(); 
module.resolveWith(exports); 

}); 
// module body: end

}; 
// module factory: end

FD31.module("textboxlist", moduleFactory);

}());