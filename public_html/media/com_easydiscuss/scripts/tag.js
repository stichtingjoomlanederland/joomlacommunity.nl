EasyDiscuss.module('tag', function($) {

  var module = this;

  EasyDiscuss.Controller(

	'Tag.Form',
	{
		defaultOptions: {

			tags: [],

			tagSelections: [],

			tagSelectionLimit: 25,

			'{tagList}'	: '.discuss-tag-list.creation',
			'{tagItems}'	: '.discuss-tag-list.creation .tag-item',
			'{tagItemRemoveButton}'	: '.remove-tag',

			'{tagSelection}'	: '.discuss-tag-list.selection',
			'{tagSelectionItems}'	: '.discuss-tag-list.selection .tag-item',
			'{tagSelectionMoreButton}'	: '.discuss-tag-list.selection .more-tags',

			'{totalTags}'	: '.total-tags',

			'{tagCreate}'	: '.new-tag-item',
			'{tagInput}'	: '.tag-input',
			'{tagCreateButton}'	: '.tag-create',

			view: {
				// FIX ME: it can't find the ejs path.
				'tagItem': 'tags.item'
			}
		}
	},

	function(self) { return {

		init: function() {

			self.generateTagSelections(self.options.tagSelections);

			// Generate tags
			$.each(self.options.tags, function(i, tag) {
				self.tagMap[tag.title] = tag;
				self.createTag(tag.title);
			});
		},

		tagItem: {},

		tagMap: {},

		tagSelectionMap: {},

		sanitizeTitle: function(title) {

			return $.trim(title).replace(/[,\'\"\#\<\>]/gi, '');
		},

		getTagItem: function(title) {

			return self.tagItems(function() {
				return $(this).data('title') == title;
			});
		},

		createTag: function(title) {

			if (!self.checkTagLimit()) {
				return;
			}

			var title = self.sanitizeTitle(title);

			var tag;
			if (Object.prototype.hasOwnProperty.call(self.tagMap, title)) {
				tag = self.tagMap[title];
			}
			tag = tag || {title: title};

			var tagItem = $(self.getTagItem(title)[0] || self.view('tagItem', tag));

			tagItem
				.data({
					title: tag.title,
					title_filter: tag.title.toUpperCase()
				})
				.css({opacity: 0});

			if (self.tagItems().length > 0) {

				var lastTagItem = self.tagItems(':last');

				// Don't move tag if it's already the last one.
				if (lastTagItem[0] != tagItem[0]) {
					lastTagItem.after(tagItem);
				}

			} else {

				self.tagList()
					.prepend(tagItem);
			}

			tagItem.animate({opacity: 1});

			// Remove it from tag selection if exists
			self.useTagSelection(tag.title);

			self.checkTagLimit();

			return tagItem;
		},

		removeTag: function(title) {

			var tagItem = self.getTagItem(title);

			tagItem.remove();

			// If it is an existing tag that we're removing
			// (only applies to tag which are created previously before, e.g. when editing an article)
			if (self.tagMap[title]) {

				// then put it back to tag selection.
				self.createTagSelection(title);
			}

			self.discardTagSelection(title);

			self.checkTagLimit();
		},

		checkTagLimit: function() {
			var totalTags = self.tagItems().length;

			// update tag count
			self.totalTags().text(totalTags);

			if (self.options.tagLimit != 0) {
				if (totalTags > self.options.tagLimit) {
					return false;
				} else if (totalTags == self.options.tagLimit) {
					self.tagCreate().hide();
					return false;
				} else {
					self.tagCreate().show();
					return true;
				}
			}
		},

		generateTagSelections: function(tagSelections) {

			var tagSelectionLimit = self.options.tagSelectionLimit;

			if (tagSelections.length <= tagSelectionLimit) {
				self.tagSelectionMoreButton().remove();
			}

			if (tagSelections.length < 1) {
				self.element.addClass('no-selection');
				return;
			}

			self.options.tagSelections.reverse();

			var max = self.options.tagSelections.length;

			// Generate tag selections
			$.each(self.options.tagSelections, function(i, tag) {

				self.tagSelectionMap[tag.title] = tag;

				var tagSelectionItem = self.createTagSelection(tag.title);

				if (i == tagSelectionLimit + 1) {
					self.tagSelectionMoreButton().show();
				}

				if ((max - i) > tagSelectionLimit) {
					tagSelectionItem.addClass('extras');
				}
			});

		},

		'{tagSelectionMoreButton} click': function(tagSelectionMoreButton) {

			tagSelectionMoreButton.remove();

			self.tagSelectionItems('.extras')
				.css({opacity: 0})
				.removeClass('extras')
				.animate({opacity: 1});
		},

		getTagSelectionItem: function(title) {

			if (Object.prototype.hasOwnProperty.call(self.tagItem, title)) {
				return self.tagItem[title];
			}
		},

		createTagSelection: function(title) {

			var tagSelectionItem = self.getTagSelectionItem(title);

			if (tagSelectionItem) {
				tagSelectionItem.show();
				return tagSelectionItem;
			}

			var tagSelectionItem =
				$('<li>')
					.addClass('tag-item')
					.data({
						title: title,
						title_filter: title.toUpperCase()
					})
					.html(title);

			self.tagItem[title] = tagSelectionItem;

			self.tagSelection()
				.prepend(tagSelectionItem);

			return tagSelectionItem;
		},

		useTagSelection: function(title) {

			var tagSelection = self.getTagSelectionItem(title);

			if (tagSelection) {
				tagSelection
					.addClass('used')
					.hide();
			}
		},

		discardTagSelection: function(title) {

			var tagSelection = self.getTagSelectionItem(title);

			if (tagSelection) {

				self.getTagSelectionItem(title)
					.removeClass('used')
					.show();

			}
		},

		createTagFromInput: function() {

			var title = self.sanitizeTitle(self.tagInput().val());

			if (title != '') {
				self.createTag(title);
				self.tagInput().val('');
			}

			// Reset filtered tags to original state
			self.filterTagSelectionItems('');
		},

		filterTagSelectionItems: function(title) {

			title = self.sanitizeTitle(title).toUpperCase();

			if (title == '') {

				self.tagSelectionItems(':not(.used)').show();

				self.element.removeClass('no-selection');

				self.tagSelectionMoreButton().show();

			} else {

				self.tagSelectionMoreButton().hide();

				var tagSelectionItems = self.tagSelectionItems()
					.filter(function(i, tagSelectionItem) {

						var tagSelectionItem = $(this);

						if (tagSelectionItem.hasClass('.used')) {
							tagSelectionItem.hide();
							return false;
						}

						if (tagSelectionItem.data('title_filter').indexOf(title) < 0) {
							tagSelectionItem.hide();
							return false;
						}

						tagSelectionItem.show();
						return true;
					});

				if (tagSelectionItems.length < 1) {
					self.element.addClass('no-selection');
				}
			}
		},

		'{tagInput} keydown': function(tagInput, event) {

			event.stopPropagation();

			self.realEnterKey = (event.keyCode == 13);
		},

		'{tagInput} keypress': function(tagInput, event) {

			event.stopPropagation();

			// We need to verify whether or not the user is actually entering
			// an ENTER key or exiting from an IME context menu.
			self.realEnterKey = self.realEnterKey && (event.keyCode == 13);
		},

		'{tagInput} keyup': function(tagInput, event) {

			event.stopPropagation();

			switch (event.keyCode) {

				case 27: // escape
					tagInput.val('');
					break;

				case 13: // enter
					if (self.realEnterKey) {
						self.createTagFromInput();
					}
					break;
			}

			self.filterTagSelectionItems(tagInput.val());
		},

		'{tagCreateButton} click': function() {

			self.createTagFromInput();
		},

		'{tagItemRemoveButton} click': function(tagItemRemoveButton) {

			var title = tagItemRemoveButton.parents('.tag-item').data('title');

			self.removeTag(title);
		},

		'{tagSelectionItems} click': function(tagSelectionItem) {

			self.createTag(tagSelectionItem.data('title'));
		}
	}}
  );

  module.resolve();

});
