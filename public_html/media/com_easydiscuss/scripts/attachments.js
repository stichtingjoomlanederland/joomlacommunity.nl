EasyDiscuss.module("attachments", function($) {

	var module = this;

	EasyDiscuss.require()
	.view("field.form.attachments.item")
	.done(function($) {

		EasyDiscuss.Controller("Attachments", {
			defaultOptions: {

				view: {
					item: "field.form.attachments.item"
				},

				hasAttachmentLimit: false,
				attachmentLimit: 0,
				editable: false,

				"{itemGroup}": "[data-attachment-itemgroup]",
				"{item}" : "[data-attachment-item]"
			}
		},
		function(self) { return {

			init: function() {

				var options = self.options,
					attachmentLimit = self.element.attr("data-attachment-limit");

				// Data API
				if (attachmentLimit!==undefined) {
					options.hasAttachmentLimit = true;
					options.attachmentLimit = parseInt(attachmentLimit) || 0;
				}

				if (options.attachmentLimit===0) {
					options.hasAttachmentLimit = false;
				}

				options.editable = self.element.hasClass("editable");

				// Add attachment item controller
				// to existing attachment items.
				self.item()
					.addController("EasyDiscuss.Controller.Attachments.Item");
			},

			setLayout: function() {

				var options = self.options,
					count = self.item(":not(.new)").length,
					exceeded = options.hasAttachmentLimit && (count >= options.attachmentLimit);

				// Toggle limit-exceeded class
				// This will show the limit exceed hint.
				self.element
					.toggleClass("limit-exceeded", exceeded)
					.toggleClass("no-attachment", count < 1);

				if (options.editable) {

					// Remove any new attachment form
					self.item(".new").remove();

					// If attachment limit is not exceeded,
					// append a new attachment form at the buttom.
					if (!exceeded) {
						self.view.item()
							.appendTo(self.itemGroup())
							.addController("EasyDiscuss.Controller.Attachments.Item");
					}
				}
			},

			"{item} itemAdded": function() {
				self.setLayout();
			},

			"{item} itemRemoved": function() {

				setTimeout(function(){
					self.setLayout();
				}, 1);
			}

		}});

		EasyDiscuss.Controller("Attachments.Item", {
			defaultOptions: {
				"{removeButton}": "[data-attachment-remove-button]",
				"{file}": "[data-attachment-file]",
				"{title}": "[data-attachment-title]",
			}
		},
		function(self) { return {

			init: function() {

				self.file().prop("disabled", false);
			},

			add: function() {

				var filename = self.file().val()
					type = self.getType(filename),
					item = self.element;

				if (filename.match(/fakepath/)) {
					filename = filename.replace(/C:\\fakepath\\/i, '');
				};

				item
					.removeClass("new")
					.addClass("attachment-type-" + type);

				self.title().html(filename);

				self.trigger("itemAdded");
			},

			remove: function() {

				var id = self.element.attr("id");

				// If there is an id
				if (id) {

					var id = id.replace("attachment-", "");

					// Run ajax call to delete attachment.
					disjax.loadingDialog();
					disjax.load('attachments', 'confirmDelete', id.toString());

				} else {

					self.trigger("itemRemoved");
					self.element.remove();
				}
			},

			getType: function(filename) {

				var extension = filename.substr((filename.lastIndexOf('.') + 1)),
					type = "default";

				switch (extension) {
					case 'jpg':
					case 'png':
					case 'gif':
						type = 'image';
						break;
					case 'zip':
					case 'rar':
						type = 'archive';
						break;
					case 'pdf':
						type = 'pdf';
						break;
				}

				return type;
			},

			"{file} change": function() {

				self.add();
			},

			"{removeButton} click": function() {

				self.remove();
			}

		}});


		$(document).on("click.ed.attachment.remove", "[data-attachment-remove-button]", function(){

			var button = $(this),
				parent = button.parents(".discuss-attachments"),
				controller = "EasyDiscuss.Controller.Attachments";

			if (parent.hasController(controller)) return;

			parent.addController(controller);

			// Trigger trigger remove event
			button.click();
		});

		module.resolve();

	});

});
