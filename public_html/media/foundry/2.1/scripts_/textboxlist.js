(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
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

// Textboxlist starts
$.require()
	.library('mvc/controller')
	.done(function(){

		// Templates
		$.template("textboxlist/item", '<li class="TextboxList-item"><span class="TextboxList-itemContent"><@== html @></span><a class="TextboxList-itemRemoveButton" href="javascript: void(0);"></a></li>');
		$.template("textboxlist/itemContent", '<@= title @><input type="hidden" name="items" value="<@= id @>"/>');

		$.Controller("TextboxList",
			{
				isPlugin: true,

				pluginName: "textboxlist",

				defaultOptions: {

					view: {
						item: 'textboxlist/item',
						itemContent: 'textboxlist/itemContent'
					},

					// Autocomplete
					autocomplete: null,

					// Options
					unique: true,
					caseSensitive: false,
					max: null,

					// Events
					filter: null,

					"{item}": ".TextboxList-item",
					"{itemRemoveButton}": ".TextboxList-itemRemoveButton",
					"{itemContent}": ".TextboxList-itemContent",
					"{textField}": ".TextboxList-textField"
				},

				implement: function(el) {
					if (el.controller(TextboxList)) return;
					el.implement(TextboxList, {}, function(){this.click();});
				}
			},
			function(self) {

				return {

				init: function() {

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
					var autocomplete = self.options.autocomplete;

					if (autocomplete || self.element.data("query")) {

						// Implement autocomplete when the module is ready
						$.module("textboxlist/autocomplete")
							.done(function(){

								self.element.implement(
									TextboxList.Autocomplete,
									$.extend({controller: {textboxList: self}}, autocomplete || {})
								);
							});
					}
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

					var items = self.itemsByTitle,
						newItem = false;

					// If item is a string,
					if ($._.isString(item) && item!=="") {

						var title = item,
							key = self.getItemKey(title);

						item =
							(items.hasOwnProperty(key)) ?

								// Get existing item
								self.itemsByTitle[key] :

								// Or create a new one
								(function(){
									var item = {id: $.uid("item-"), title: title, key: self.getItemKey(title)};
									newItem = true;
									return item;
								})();
					}

					// If item content is not created, then make one.
					if (item.html===undefined) {
						item.html = self.view.itemContent(true, item);
					}

					// If items should be unique
					if (options.unique &&

						// and this item has already been added to the list
						(self.items.hasOwnProperty(item.id) ||

							// or item of the same title already exists
							(newItem && items.hasOwnProperty[item.key])
						)

					   )
					{
						// Then don't create this item anymore
						return null;
					}

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

					// Don't add empty title
					if (item==="") return;

					var options = self.options;

					// If we reached the maximum number of items, skip.
					var max = options.max;
					if (max!==null && self.item().length>=max) return;

					// Filter item
					item = self.filterItem(item);

					// At this point, if item if not an object, skip.
					if (!$.isPlainObject(item)) return;

					self.createItem(item);

					// Add item on to the list
					self.view.item(item)
						.attr("data-id", item.id)
						.insertBefore(self.textField());

					return item;
				},

				removeItem: function(id) {

					// Remove item from the list
					self.item("[data-id=" + id + "]")
						.remove();

					self.deleteItem(id);
				},

				"click": function() {
					self.textField().focus();
				},

				"{itemRemoveButton} click": function(item) {

					self.removeItem(item.data("id"));
				},

				"{textField} keydown": function(textField, event)
				{
					var keyCode = event.keyCode;

					textField.data("realEnterKey", keyCode==KEYCODE.ENTER);

					var textFieldKeydown = self.options.textFieldKeydown;

					$.isFunction(textFieldKeydown) && textFieldKeydown.call(self, textField, event);
				},

				"{textField} keypress": function(textField, event)
				{
					var keydownIsEnter = textField.data("realEnterKey"),

						// When a person enters the IME context menu,
						// the keyCode returned during keypress will
						// not be the enter keycode.
						keypressIsEnter = event.keyCode==KEYCODE.ENTER;

					textField.data("realEnterKey", keydownIsEnter && keypressIsEnter);

					var item = $.trim(self.textField().val());

					// Trigger custom event
					var textFieldKeypress = self.options.textFieldKeypress;

					if ($.isFunction(textFieldKeypress)) {

						item = textFieldKeypress.call(self, textField, event, item);
					}

					// If item was converted into a null object,
					// this means the custom keyup event wants to "preventDefault".
					if (item===undefined || item===null) return;

					switch (event.keyCode) {

						// Add new item
						case KEYCODE.ENTER:

							if (textField.data("realEnterKey")) {

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

					// Trigger custom event if exists
					var textFieldKeyup = self.options.textFieldKeyup;

					if ($.isFunction(textFieldKeyup)) {

						item = textFieldKeyup.call(self, textField, event, item);
					}

					// If item was converted into a null object,
					// this means the custom keyup event wants to "preventDefault".
					if (item===undefined || item===null) return;

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
				}
			}}
		);

		$(document)
			.on('click.textboxlist.data-api', '[data-provide="textboxlist"]', function(event){
				TextboxList.implement($(this));
			})
			.on('focus.textboxlist.data-api', '[data-provide="textboxlist"] .TextboxList-textField', function(event){
				TextboxList.implement($(this).parents('.TextboxList'));
			});
	});
// Textboxlist ends

// Textboxlist.autocomplete start
$.module('textboxlist/autocomplete', function(){

	var module = this;

	$.require()
		.library('mvc/controller', 'ui/position')
		.done(function(){

			$.template("textboxlist/menu", '<div class="TextboxList-autocomplete"><div class="inner"><ul class="TextboxList-menu"></ul></div></div>');
			$.template("textboxlist/menuItem", '<li class="TextboxList-menuItem"><@== html @></li>');

			$.Controller("TextboxList.Autocomplete",
			{
				defaultOptions: {

					view: {
						menu: "textboxlist/menu",
						menuItem: "textboxlist/menuItem"
					},

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

					"{menu}": ".TextboxList-menu",
					"{menuItem}": ".TextboxList-menuItem"
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
							.implement(TextboxList.Autocomplete, self.options);

						return;
					}

					// Bind to the keyup event
					self.textboxList.update({
						textFieldKeypress: self.textFieldKeypress,
						textFieldKeyup: self.textFieldKeyup
					});

					// Set the position to be relative to the textboxList
					self.options.position.of = self.textboxList.element;

					self.initQuery();
				},

				initQuery: function() {

					// Determine query method
					var query = self.options.query || self.textboxList.element.data("query");

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

				show: function() {

					var textboxList = self.textboxList.element;

					self.element
						.show()
						.css({
							width: textboxList.outerWidth()
						})
						.position(self.options.position);

					self.hidden = false;
				},

				hide: function() {

					self.element.hide();

					self.menuItem().removeClass("active");

					self.render.reset();

					self.hidden = true;
				},

				queries: {},

				populated: false,

				populate: function(keyword) {

					self.populated = false;

					var options = self.options,
						key = (options.caseSensitive) ? keyword : keyword.toLowerCase(),
						query = self.queries[key];

					var newQuery = !$.isDeferred(query),

						runQuery = function(){

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
								});
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

				render: $.Enqueue(function(items, keyword){

					if (!$.isArray(items)) return;

					// If there are no items, hide menu.
					if (items.length < 1) {
						self.hide();
						return;
					}

					var menu = self.menu();

					if (menu.data("keyword")!==keyword)
					{
						// Clear out menu items
						menu.empty();

						$.each(items, function(i, item){

							var filterItem = self.options.filterItem;

							if ($.isFunction(filterItem)) {
								item = filterItem.call(self, item, keyword);
							}

							// If the item is not an object, stop.
							if (!$.isPlainObject(item)) return;

							var html = item.menuHtml || item.title;

							self.view.menuItem({html: html})
								.data("item", item)
								.appendTo(menu);
						});

						menu.data("keyword", keyword);
					}

					self.show();
				}),

				getActiveMenuItem: function() {

					var activeMenuItem = self.menuItem(".active");

					if (activeMenuItem.length < 1) {
						activeMenuItem = undefined;
					}

					return activeMenuItem;
				},

				textFieldKeypress: function(textField, event, keyword) {

					var onlyFromSuggestions = self.options.exclusive;

					// If menu is not visible, stop.
					if (self.hidden) {

						// If we only accept suggested items,
						// don't let textboxlist add the keyword
						// by returning null.
						return (onlyFromSuggestions) ? null : keyword;
					}

					// Get active menu item
					var activeMenuItem = self.getActiveMenuItem();

					switch (event.keyCode) {

						// If up key is pressed
						case KEYCODE.UP:

							// Deactivate all menu item
							self.menuItem().removeClass("active");

							// If no menu items are activated,
							if (!activeMenuItem) {

								// activate the last one.
								self.menuItem(":last").addClass("active");

							// Else find the menu item before it,
							} else {

								// and activate it.
								activeMenuItem.prev(self.menuItem.selector)
									.addClass("active");
							}
							break;

						// If down key is pressed
						case KEYCODE.DOWN:

							// Deactivate all menu item
							self.menuItem().removeClass("active");

							// If no menu items are activated,
							if (!activeMenuItem) {

								// activate the first one.
								self.menuItem(":first").addClass("active");

							// Else find the menu item after it,
							} else {

								// and activate it.
								activeMenuItem.next(self.menuItem.selector)
									.addClass("active");
							}
							break;

						// If enter is pressed
						case KEYCODE.ENTER:

							// Get activated item.
							var activeMenuItem = self.getActiveMenuItem();

							// Hide the menu
							self.hide();

							// If there is an activated item,
							if (activeMenuItem) {

								// get the item data,
								var item = activeMenuItem.data("item");

								// and return the item data to the textboxlist.
								return item;

							} else if (onlyFromSuggestions) {

								return null;
							}
							break;

						// If escape is pressed,
						case KEYCODE.ESCAPE:

							// hide menu.
							self.hide();
							break;
					}

					return (onlyFromSuggestions) ? null : keyword;
				},

				textFieldKeyup: function(textField, event, keyword) {

					var onlyFromSuggestions = self.options.exclusive;

					switch (event.keyCode) {

						case KEYCODE.UP:
						case KEYCODE.DOWN:
						case KEYCODE.ENTER:
						case KEYCODE.ESCAPE:
							// Don't repopulate if these keys were pressed.
							break;

						default:

							// If no keyword given or keyword doesn't meet minimum query length, stop.
							if (keyword==="" || (keyword.length < self.options.minLength)) {

								self.hide();

							// Else populate suggestions.
							} else {

								self.populate(keyword);
							}
							break;
					}

					return (onlyFromSuggestions) ? null : keyword;
				},

				"{menuItem} click": function(menuItem) {

					// Hide context menu
					self.hide();

					// Add item
					var item = menuItem.data("item");
					self.textboxList.addItem(item);

					// Get text field & clear text field
					var textField = self.textboxList.textField().val("");

					// Refocus text field
					setTimeout(function(){

						// Due to event delegation, this needs to be slightly delayed.
						textField.focus();
					}, 150);
				}
			}}
			);

			module.resolve(TextboxList.Autocomplete);
		});
});
// Autocomplete ends

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

dispatch("textboxlist")
.containing(moduleFactory)
.to("Foundry/2.1 Modules");

}());