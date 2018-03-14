EasyDiscuss.module('avatar', function($) {

	var module = this;

  EasyDiscuss
.require()
.library('imgareaselect')
.done(function($) {

	EasyDiscuss.Controller('Avatar',
		{
			defaultOptions: {

				'{avatarContainer}': '.avatarContainer',
				'{avatar}' : '.avatarContainer img',

				'{avatarPreviewContainer}' : '.avatarPreviewContainer',
				'{avatarPreviewPlaceholder}': '.avatarPreviewPlaceholder',
				'{avatarPreview}' : '.avatarPreview',

				'{startCropButton}' : '.startCropButton',
				'{saveCropButton}' : '.saveCropButton',
				'{stopCropButton}' : '.stopCropButton',

				'{alertMessage}': '.alertMessage',

				selection: {
					disable: false,
					handles: true,
					show: true,
					minWidth: 160,
					minHeight: 160,
					x1: 0,
					y1: 0,
					x2: 160,
					y2: 160,
					previewWidth: 160,
					previewHeight: 160,
					aspectRatio: '1:1'
				}
			}
		},
		function(self) { return {

			init: function() {

				self.avatarPreviewContainer().css({
					width: self.options.selection.previewWidth,
					height: self.options.selection.previewHeight,
					position: 'relative',
					overflow: 'hidden'
				});

				// Some templates may have max-width: 100% on img tag.
				self.avatarPreview().css({
					maxWidth: 'none'
				});
			},

			start: function() {

				self.alertMessage().hide();

				self.element.addClass('cropping');

				var options = self.options.selection,
					avatar = self.avatar(),
					preview = self.avatarPreview(),
					imageWidth = avatar.width(),
					imageHeight = avatar.height(),
					x1, x2, y1, y2;

				self.selector = avatar.imgAreaSelect(
					$.extend({}, options, {

						parent: self.avatarContainer(),

						instance: true,

						x1: x1 = (imageWidth / 2) - (options.minWidth / 2),
						y1: y1 = (imageHeight / 2) - (options.minHeight / 2),
						x2: x1 + options.minWidth,
						y2: y1 + options.minHeight,

						onSelectChange: function(img, selection) {

							var scaleX = options.previewWidth / (selection.width || 1),
								scaleY = options.previewHeight / (selection.height || 1);

							preview.css({
								width: Math.round(scaleX * imageWidth) + 'px',
								height: Math.round(scaleY * imageHeight) + 'px',
								marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
								marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
							});
						}
					})
				);

				self.avatarPreviewPlaceholder().hide();

				self.avatarPreview().show();
			},

			stop: function() {

				self.alertMessage().remove();

				self.element.removeClass('cropping');

				self.avatarPreview().hide();

				self.avatarPreviewPlaceholder().show();

				// Disable selection
				self.avatar().imgAreaSelect({
					hide: true,
					disable: true
				});

				delete self.selector;
			},

			save: function() {

				if (self.selector == undefined) {
					self.stop();
					return;
				}

				var selection = self.selector.getSelection();

				self.saveCropButton()
					.addClass('btn-loading');

				EasyDiscuss.ajax('site.views.profile.cropphoto', selection)
					.done(function(imageUrl, message) {

						self.stop();

						self.avatarPreviewPlaceholder()
							.attr('src', imageUrl + '?' + $.uid());

						self.alert(message || 'Avatar successfully cropped!', 'success');
					})
					.fail(function(message) {

						self.alert(message || 'Unable to crop avatar.', 'error');
					})
					.always(function() {

						self.saveCropButton()
							.removeClass('btn-loading');
					});
			},

			alert: function(message, type) {

				if (type === undefined) type = 'info';

				self.alertMessage().remove();

				$('<div class="alert alertMessage"></div>')
					.addClass('alert-' + type)
					.html(message)
					.prependTo(self.element);
			},

			'{startCropButton} click': function() {

				self.start();
			},

			'{saveCropButton} click': function() {

				self.save();
			},

			'{stopCropButton} click': function() {

				self.stop();
			}
		}}
	);

	module.resolve();

      });

});
