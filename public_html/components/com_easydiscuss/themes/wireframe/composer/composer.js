ed.require(['edq', 'easydiscuss', 'jquery.fancybox', 'jquery.atwho'], function($, EasyDiscuss) {
	
	this.saveState = $.Deferred();

	var self = this;

	// Cancel button
	var wrapper = $('[<?php echo $editorId;?>]');
	var cancelButton = wrapper.find('[data-ed-reply-cancel]');

	if (cancelButton.length > 0) {
		cancelButton.live('click', function() {

			var container = wrapper.parents('[data-ed-reply-editor]');

			// Remove the html codes
			container.html('');
		});
	}

	// Reply counter
	var replyCounter = $('[data-ed-post-reply-counter]');

	// content highlighter syntax
	var syntaxHighlighter = <?php echo $this->config->get('main_syntax_highlighter') ? 'true' : 'false'; ?>;

	// create a function namespace attachments.initGallery()
	var attachments = {
		// reload attachment section
		initGallery: function(options) {
			$('.attachment-image-link').fancybox(options);
		},
		clear: function(el, ev) {
		}
	};

	var reinitAtwho = function(editor, commands) {
		editor
			.atwho({
				at: "/",
				highlightFirst: false,
				minLen: 0,
				delay: 200,
				data: commands['actions'],
				insertTpl: function(val) {

						if (val.name == 'label') {
							return '/label ~';
						}

						return '${atwho-at}${name}';
					},
				limit: 10
			});

		editor
			.atwho({
				at: "~",
				highlightFirst: false,
				minLen: 0,
				delay: null,
				data: commands['labels'],
				limit: 10
			});
	};

	var resetForm = function(form) {
		self.saveState = $.Deferred();

		// Trigger reset form
		$(form).trigger('composer.form.reset');

		// Empty contents of editor
		form.find('textarea[name=dc_content]').val('');

		// Close back the GIPHY browser
		form.find('.markitup-giphy').removeClass('is-active');
		form.find('[data-giphy-browser]').removeClass('is-open');

		// Clear out CKEditor's content
		if (window.CKEDITOR) {
			try {
				window.CKEDITOR.instances['content'].setData('');
			} catch (e) {}
		}
	};

	var increaseReplyCount = function() {
		// Update the counter.
		var count = replyCounter.html();

		count = parseInt(count) + 1;

		replyCounter.html(count);
	};

	var setAlert = function(alert, message, state) {

		var newClass = 'o-alert--success';

		if (state == 'error') {
			newClass = 'o-alert--danger';
		}

		if (state == 'info') {
			newClass = 'o-alert--info';
		}

		alert
			.html(message)
			.removeClass('o-alert--success')
			.removeClass('o-alert--danger')
			.removeClass('o-alert--info')
			.addClass(newClass);
	};

	var setLabelAlert = function(labelAlert, message) {

		labelAlert
			.html(message)
			.removeClass('o-alert--success')
			.removeClass('o-alert--danger')
			.removeClass('o-alert--info')
			.addClass('o-alert--info');
	};

	function activateReplyButton(replyButton)
	{
		replyButton.removeAttr('disabled');
		replyButton.removeClass('is-loading');
	}

	function formValidateInputs(wrapper)
	{
		var notification = wrapper.find('[data-ed-composer-alert]');
		var content = wrapper.find('[data-ed-editor]');

		if (content.val() == '') {
			notification.removeClass('t-hidden');
			setAlert(notification, '<?php echo JText::_('COM_EASYDISCUSS_ERROR_REPLY_EMPTY'); ?>', 'error');
			return false;
		}

		// Check for t&c if the system is enabled.
		<?php if ($this->config->get('main_tnc_reply')) { ?>
		var tnc = $('[data-ed-tnc-checkbox]');

		if (!tnc.is(':checked')) {
			EasyDiscuss.dialog({
				"content": '<?php echo addslashes(JText::_("COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT")); ?>'
			});

			return false;
		};
		<?php } ?>

		return true;
	};

	/**
	 * Bind text editor to handle cmd + ctrl + enter to submit
	 */
	$(document).on('keydown.reply.submit', '[<?php echo $editorId;?>] [data-ed-editor]', function(event) {

		if (event.keyCode == 13 && event.metaKey) {
			var element = $(this);
			var parent = element.parents('[<?php echo $editorId;?>]');
			var button = parent.find('[data-ed-reply-submit]');

			// Simulate submission of comment
			button.click();
		}
	});

	/**
	 * Bind submit reply button
	 */
	$(document).on('click.reply.submit', '[<?php echo $editorId;?>] [data-ed-reply-submit]', function() {

		var deferredObjects = [];
		var element = this;

		$(document).trigger('onSubmitPost', [deferredObjects]);

		if (deferredObjects.length <= 0) {
			self.saveState.resolve();
		} else {
			
			$.when.apply(null, deferredObjects)
				.done(function() {
					self.saveState.resolve();
				})
				.fail(function() {
					self.saveState.reject();
				});
		}

		self.saveState.done(function() {
			submitReply(element);
		});
	});

	// Click reply submit
	var submitReply = function(element) {

		// This is the reply button
		var replyButton = $(element);

		// Find the wrapper for the reply form
		var wrapper = replyButton.parents('[data-ed-composer-wrapper]');

		var repliesOrder = wrapper.data('replies-order');

		// Get the session token from the site to prevent CSRF attacks
		var token = $('[data-ed-token]').val();
		var target = '<?php echo JURI::root() . $appendAjaxIndex;?>?option=com_easydiscuss&view=post&layout=<?php echo $operation == 'editing' ? 'update' : 'reply';?>&format=ajax&tmpl=component&' + token + '=1';

		var isFromAnswer = $(element).parents('[data-ed-post-answer-wrapper]').length > 0 ? true : false;

		// Determine if this is editing from the accepted answer
		if (isFromAnswer) {
			target += '&fromAnswer=1';
		}

		// Given the wrapper, we need to find the form now
		var form = wrapper.find('[data-ed-composer-form]');
		var formDom = form[0];

		var editor = wrapper.find('[data-ed-editor]');

		// Get the notification bar
		var notification = wrapper.find('[data-ed-composer-alert]');
		var notificationLabel = wrapper.find('[data-ed-composer-labelalert]');

		// Always hide alerts when they submit
		notification.addClass('t-hidden');
		notificationLabel.addClass('t-hidden');

		// Validate inputs
		var isValid = formValidateInputs(wrapper);
		
		if (!isValid) {
			return;
		}

		// Perform other validation
		var validateState = $.Deferred();
		var tasks = [];

		$(document).trigger('onValidatePost', [tasks, wrapper]);

		if (tasks.length <= 0) {
			validateState.resolve();
		} 

		if (tasks.length > 0) {
			$.when.apply(null, tasks) 
				.done(function() {
					validateState.resolve();
				})
				.fail(function() {
					validateState.reject();
				});
		}

		validateState
		.done(function() {
			// Create a temporary iframe to simulate postings as we have attachments
			var iframe = document.createElement('iframe');
			iframe.setAttribute('id', 'upload_iframe');
			iframe.setAttribute('name', 'upload_iframe');
			iframe.setAttribute('width', '0');
			iframe.setAttribute('height', '0');
			iframe.setAttribute('border', '0');
			iframe.setAttribute('style', 'width: 0; height: 0; border: none;');

			// Append the iframe into the <form>
			formDom.parentNode.appendChild(iframe);

			// assign name/id of the iframe
			window.frames['upload_iframe'].name = 'upload_iframe';
			var iframeId = document.getElementById('upload_iframe');

			// Update the submit reply button
			replyButton.addClass('is-loading');
			replyButton.prop('disabled', true);

			// Get the replies wrapper
			var repliesWrapper = $('[data-ed-post-replies-wrapper]');
			var replies = $('[data-ed-post-replies]');

			// Get the post wrapper
			var post = $('[data-ed-post-wrapper]');

			// Add event handling for the iframe
			var eventHandler = function() {

				var content;

				if (iframeId.detachEvent) {
					iframeId.detachEvent('onload', eventHandler);
				} else {
					// load event
					iframeId.removeEventListener('load', eventHandler, false);
				}

				// Message from server...
				if (iframeId.contentDocument) {
					// assign the document into content variable
					content = iframeId.contentDocument;

				} else if (iframeId.contentWindow) {
					content = iframeId.contentWindow.document;

				} else if (iframeId.document) {
					content = iframeId.document;
				}

				// Search inside the document is it got 'ajaxResponse' = id="ajaxResponse"
				content = $(content).find('script#ajaxResponse').html();

				var result = $.parseJSON(content);

				// Update the alert
				setAlert(notification, result.message, result.type);

				var hasSlashText = result.slashText !== undefined ? true : false;

				if (hasSlashText) {
					setLabelAlert(notificationLabel, result.slashText);

					if (result.commands !== undefined) {
						// re-initialize atwho with new data
						reinitAtwho(editor, result.commands);
					}

					// if this submit data only has slash command, we directly finish the submission
					if (result.noContent) {
						// Reset the form
						formDom.reset();

						// Clear the form.
						resetForm(form);

						notificationLabel.removeClass('t-hidden');
						activateReplyButton(replyButton);

						if (repliesOrder == 'latest') {
							replies.prepend(result.slashHtml);
						} else {
							replies.append(result.slashHtml);
						}

						// Delete the iframe...
						setTimeout(function() {
							$(iframeId).remove();
						}, 250);

						return;
					}

				}

				if (result.type == 'success' || result.type == 'success.edit') {

					// first we replace the gist script tag with normal div with an identifier.
					var tmp = $(result.html);
					var count = 0;
					$(tmp).find('[data-ed-scripts-gist]').each(function() {

						var url = $(this).attr('src');

						var div = document.createElement("div");
						$(div).attr('data-gist-div', "");
						$(div).attr('data-url', url);
						$(div).attr('style', 'border:0; padding:0;');

						$(this).replaceWith(div);
						count++;
					});

					if (count > 0) {
						// update the result.html
						result.html = tmp[0];
					}
				}


				// For editing success
				if (result.type == 'success.edit') {
					var parent = wrapper.parent();

					// Remove the editor
					parent.remove();

					// Now we need to replace the html contents back
					var replyItem = post.find('[data-ed-reply-item][data-id=' + result.id + ']');

					if (isFromAnswer) {
						replyItem = post.find('[data-ed-post-answer-item]').find('[data-ed-reply-item][data-id=' + result.id + ']');
					}

					replyItem.replaceWith(result.html);
				}

				$(document).trigger('reloadCaptcha');

				// For success/info cases. info means its in pending state
				if (result.type == 'success' || result.type == 'info') {

					// Remove any empty classes
					repliesWrapper.removeClass('is-empty');

					// Update the counter.
					increaseReplyCount();

					// Append the result to the list.
					if (repliesOrder == 'latest') {
						replies.prepend(result.html);
					} else {
						replies.append(result.html);
					}

					// Reload the syntax highlighter.
					if (result.script != 'undefined') {
						eval(result.script);
					}

					// Reset the form
					formDom.reset();

					// Prevent tnc from being reset.
					var tnc = $('[data-ed-reply-tnc-checkbox]');
					tnc.removeAttr('checked');
					tnc.attr('checked', 'checked');

					// Clear the form.
					resetForm(form);
				};

				if (result.type == 'success' || result.type == 'success.edit') {
					// now the contents are append into the doc.
					// we need to replade the div back to proper script tag and use iframe to load.
					$('[data-gist-div]').each(function() {

						var url = $(this).data('url');

						// Create an iframe, append it to this document where specified
						var iframe = document.createElement('iframe');

						// Set the iframe attributes
						iframe.setAttribute('width', '100%');
						iframe.setAttribute('style', 'border:0; padding:0;');
						iframe.id = 'gistFrame';

						$(this).html(iframe);

						var callback = $.callback(function(height) {
							var i = document.getElementById("gistFrame");
							i.style.height = parseInt(height) + "px";
							i.style.border = 0;
							i.style.padding = 0;
						});

						// Create the iframe's document
						var html = '<h' + 'tml>' + '<' + 'b' + 'ody onload="parent.' + callback + '(document.body.scrollHeight);"><scr' + 'ipt src="' + url + '"></sc'+'ript></'+'b' + 'ody>' + '<' + '/h' + 'tml>';

						// Set iframe's document with a trigger for this document to adjust the height
						var doc = iframe.document;

						if (iframe.contentDocument) {
							doc = iframe.contentDocument;
						} else if (iframe.contentWindow) {
							doc = iframe.contentWindow.document;
						}

						doc.open();
						doc.writeln(html);
						doc.close();

					});
				}

				if (result.type == 'error.captcha') {
					setAlert(notification, result.message, 'error');
				};

				// Reload the lightbox for new contents
				attachments.initGallery({
					type: 'image',
					helpers: {
						overlay: null
					}
				});

				// Check the syntax highlighter is it enable or not
				if (syntaxHighlighter) {
					Prism.highlightAll();
				}

				// Display the notification now
				notification.removeClass('t-hidden');

				if (hasSlashText) {
					notificationLabel.removeClass('t-hidden');
				}

				// Activate the reply button
				activateReplyButton(replyButton);

				// Delete the iframe...
				setTimeout(function() {
					$(iframeId).remove();

					if (result.postId) {
						var anchorLink = document.getElementById("reply-" + result.postId);
						anchorLink.scrollIntoView({inline: "nearest", behavior: "smooth"});
					}
				}, 250);
			};

			$(iframeId).load(eventHandler);

			// Set properties of form...
			formDom.setAttribute('target', 'upload_iframe');
			formDom.setAttribute('action', target);
			formDom.setAttribute('method', 'post');
			formDom.setAttribute('enctype', 'multipart/form-data');
			formDom.setAttribute('encoding', 'multipart/form-data');

			// Submit the form...
			formDom.submit();


		})
		.fail(function() {
		});
	};
});
