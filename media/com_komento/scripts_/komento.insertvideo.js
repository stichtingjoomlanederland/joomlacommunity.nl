Komento.module('komento.insertvideo', function($) {
	var module = this;

	Komento.Controller(
		'InsertVideo',
	{
		defaults: {
			caretPosition: '',
			element: '',
			'{videoUrl}': '.videoUrl',
			'{insertVideo}': '.insertVideo',
			'{cancelVideo}': '.cancelVideo'
		},
	},
	function(self) { return {
		init: function() {
		},

		'{insertVideo} click': function() {
			self.addVideoLink();
		},

		'{cancelVideo} click': function() {
			self.closeDialog();
		},

		'{videoUrl} keypress': function(element, event) {
			if(event.keyCode == 13) {
				event.preventDefault();
				self.addVideoLink();
			}
		},

		addVideoLink: function() {
			var videoUrl = self.videoUrl().val();

			if(videoUrl === '') {
				return;
			}

			var textarea = $('#' + self.options.element);
			var tag = '[video]' + videoUrl + '[/video]';

			var contents = textarea.val();

			if(self.options.caretPosition == 0) {
				textarea.val(tag + contents);

				$('.commentInput').focus();
				$('.commentForm').controller().commentLengthCheck();

				self.closeDialog();
				return true;
			}

			var contents = textarea.val();

			textarea.val(contents.substring(0, self.options.caretPosition) + tag + contents.substring(self.options.caretPosition, contents.length));

			$('.commentInput').focus();
			$('.commentForm').controller().commentLengthCheck();

			self.closeDialog();
		},

		closeDialog: function() {
			$('.foundryDialog').controller().close();
		}
	} });

	module.resolve();
});
