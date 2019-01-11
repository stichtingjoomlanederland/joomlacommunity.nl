ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	window.insertVideoCode = function(videoURL , caretPosition , elementId, contents, dialogRecipient) {

		if (videoURL.length == 0) {
			return false;
		}

		var tag = '[video]' + videoURL + '[/video]';

		// If this is coming from dialog composer, we need to reload back the dialog
		if (dialogRecipient > 0) {
			var newContents = tag;
			
			if (caretPosition != 0 || contents.length > 0) {
				newContents = contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length);
			}

			renderComposer(dialogRecipient, newContents);
			return true;
		}

		var textarea = $('textarea[name=' + elementId + ']');
		var contents = $(textarea).val();
		var contentsExist = contents.length;

		// If this is at the first position, we don't want to do anything here.
		// Avoid some cases if user insert these code at the first line, the rest content will went missing
		if (caretPosition == 0 && contentsExist == 0) {

			$(textarea).val(tag);
			EasyDiscuss.dialog().close();
			return true;
		}

		$(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
	};

	window.insertPhotoCode = function(photoURL , caretPosition , elementId, contents, dialogRecipient) {

		if (photoURL.length == 0) {
			return false;
		}
		
		var tag = '[img]' + photoURL + '[/img]';

		// If this is coming from dialog composer, we need to reload back the dialog
		if (dialogRecipient > 0) {
			var newContents = tag;
			
			if (caretPosition != 0 || contents.length > 0) {
				newContents = contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length);
			}

			renderComposer(dialogRecipient, newContents);
			return true;
		}

		var textarea = $('textarea[name=' + elementId + ']');
		var contents = $(textarea).val();
		var contentsExist = contents.length;

		// If this is at the first position, we don't want to do anything here.
		// Avoid some cases if user insert these code at the first line, the rest content will went missing
		if (caretPosition == 0 && contentsExist == 0) {

			$(textarea).val(tag);
			EasyDiscuss.dialog().close();
			return true;
		}

		$(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
	};

	window.insertLinkCode = function(linkURL , linkTitle, caretPosition , elementId, contents, dialogRecipient) {

		if (linkURL.length == 0) {
			return false;
		}

		if (linkTitle.length == 0) {
			linkTitle = 'Title';
		}

		var tag = '[url=' + linkURL + ']'+ linkTitle +'[/url]';

		// If this is coming from dialog composer, we need to reload back the dialog
		if (dialogRecipient > 0) {
			var newContents = tag;
			
			if (caretPosition != 0 || contents.length > 0) {
				newContents = contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length);
			}

			renderComposer(dialogRecipient, newContents);
			return true;
		}

		var textarea = $('textarea[name=' + elementId + ']');
		var contents = $(textarea).val();
		var contentsExist = contents.length;

		// If this is at the first position, we don't want to do anything here.
		// Avoid some cases if user insert these code at the first line, the rest content will went missing
		if (caretPosition == 0 && contentsExist == 0) {

			$(textarea).val(tag);
			EasyDiscuss.dialog().close();
			return true;
		}

		$(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
	};

	renderComposer = function(dialogRecipient, contents) {
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/conversation/compose', {
				"id": dialogRecipient,
				"contents": contents
			}),
			bindings: {
				"init": function() {
				}
			}
		});
	};
});
