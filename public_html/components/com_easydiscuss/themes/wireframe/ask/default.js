ed.require(['edq', 'easydiscuss', 'jquery.scrollto'], function($, EasyDiscuss) {

	// Form submission
	var askForm = $('[data-ed-ask-form]');
	var submitButton = $('[data-ed-submit-button]');
	var tncLink = $('[data-ed-ask-tnc-link]');
	var approveButton = $('[data-ed-approve-button]');
	var rejectButton = $('[data-ed-reject-button]');

	// show term and conditions
	tncLink.on('click', function() {
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/comment/showTnc', {
			})
		})
	});

	approveButton.on('click', function() {
		var button = $(this);
		var postId = button.data('id');

		// Popup the dialog
		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/dashboard/confirmApprovePost', {
				'id' : postId
			}),
			bindings: {
				"{approveButtonDialog} click": function() {

					// Trigger saving process
					submitPost(this, 'approve');
					EasyDiscuss.dialog().close();
				}
			}
		});      
	});

	rejectButton.on('click', function() {
		var button = $(this);
		var postId = button.data('id');

		EasyDiscuss.dialog({
			content: EasyDiscuss.ajax('site/views/dashboard/confirmRejectPost', {
				'id' : postId
			})
		});
	});

	var saveState = $.Deferred();

	submitButton.on('click', function() {
		var deferredObjects = [];
		var element = this;

		$(document).trigger('onSubmitPost', [deferredObjects]);

		if (deferredObjects.length <= 0) {
			saveState.resolve();
		} else {
			$.when.apply(null, deferredObjects) 
				.done(function() {
					saveState.resolve();
				})
				.fail(function() {
					saveState.reject();
				});
		}

		saveState.done(function() {
			submitPost(element);
		});
	});

	function submitPost(element, action) {

		var button = $(element);

		// Check if the button was disabled
		var disabled = button.attr('disabled');

		if (disabled) {
			return;
		}

		var alertError = $('[data-ed-post-alert]');
		alertError.attr('hidden', '');
	  
		// Perform validations
		var title = $('[data-ed-post-title]');
		var minimumTitle = title.data('minimum-title');

		// re-check again if required title field already fill in
		title.parent().removeClass('has-error');

		if (title.val() == '' || title.val().length < minimumTitle) {
			title.parent().addClass('has-error');
			alertError.removeAttr('hidden');

			if (title.val() == '') {
				alertError.html('<?php echo JText::_("COM_EASYDISCUSS_POST_TITLE_EMPTY"); ?>');
			} else {
				alertError.html('<?php echo JText::_("COM_EASYDISCUSS_POST_TITLE_TOO_SHORT"); ?>');
			}

			$(document).scrollTo(alertError);

			return false;
		}

		<?php if ($this->config->get('main_post_title_limit')) { ?>
		var maxCharacters = <?php echo $this->config->get('main_post_title_chars'); ?>;
			
		if (title.val().length > maxCharacters) {
			title.parent().addClass('has-error');
			alertError.removeAttr('hidden');

			alertError.html('<?php echo JText::sprintf("COM_ED_POST_TITLE_EXCEEDED_LIMIT", $this->config->get('main_post_title_chars')); ?>');

			$(document).scrollTo(alertError);

			return false;
		}
		<?php } ?>
		

		var category = $('#category_id');

		// re-check again if required category field already selected
		category.parent().removeClass('has-error');

		if (category.val() == 0 || category.val().length == 0) {

			category.parent().addClass('has-error');

			alertError.removeAttr('hidden');
			alertError.html('<?php echo JText::_("COM_EASYDISCUSS_POST_CATEGORY_EMPTY"); ?>');

			$(document).scrollTo(alertError);

			return false;
		}

		// Get the content from the composer
		var content = $('[data-ed-editor]');

		// re-check again if required content textarea already fill in
		content.parent().removeClass('has-error');

		if (content.val() == "") {
			content.parent().addClass('has-error');

			alertError.removeAttr('hidden');
			alertError.html('<?php echo JText::_("COM_EASYDISCUSS_POST_CONTENT_EMPTY"); ?>');

			$(document).scrollTo(alertError);
			return false;
		}

		// Get custom field data attribute
		var textbox = $('[data-ed-textbox-fields]');
		var textarea = $('[data-ed-textarea-fields]');
		var radio = $('[data-ed-radio-fields]');
		var checkbox = $('[data-ed-checkbox-fields]');
		var selectList = $('[data-ed-select-fields]');
		var selectMultipleList = $('[data-ed-select-multiple-fields]');

		// Check whether custom fields is it set as required
		var fields = $('[data-ed-custom-fields-required]');
		var fieldsWrapper = fields.parents('[data-ed-custom-fields]');
		var fieldTab = fieldsWrapper.children('[data-ed-custom-fields-tab]');
		// Get the field required state
		var fieldsRequiredWrapper = fieldTab.siblings().find('[data-ed-custom-fields-required]');
		// Get all the custom field form groups
		var fieldGroup = fieldsRequiredWrapper.parents('[data-ed-custom-fields-required-group]');

		var inputRequired = false;

		// highlight the field tab
		var tabField = $('[data-ed-ask-tabs]');
		var tabFieldError = tabField.children('[data-ed-tab-field-heading]');

		// re-check again if required field value already fill in
		fieldGroup.removeClass('has-error');

		if (fieldGroup.length > 0) {

			// Get each of the existing required custom fields
			fieldGroup.each(function(idx, el) {

				var field = $(el);
				var fType = field.data('field-type');

				if (fType == 'text' && textbox.val() == '') {
					inputRequired = true;
					field.addClass('has-error');
				}

				if (fType == 'area' && textarea.val() == '') {
					inputRequired = true;
					 field.addClass('has-error');
				}

				if (fType == 'radio' && !radio.is(':checked')) {
					inputRequired = true;
					 field.addClass('has-error');
				}

				if (fType == 'check' && !checkbox.is(':checked')) {
					inputRequired = true;
					 field.addClass('has-error');
				}

				if (fType == 'select' && !selectList.is(':selected')) {
					inputRequired = true;
					 field.addClass('has-error');
				}

				if (fType == 'multiple' && !selectMultipleList.is(':selected')) {
					inputRequired = true;
					field.addClass('has-error');
				}
			});

			// if there are some required field still empty value
			if (inputRequired) {
				tabFieldError.addClass('has-error');
				return false;
			}
		}

		// Check for t&c if the system is enabled.
		<?php if ($this->config->get('main_tnc_question')) { ?>
		var tnc = $('[data-ed-ask-tnc-checkbox]');

		if (!tnc.is(':checked')) {
			EasyDiscuss.dialog({
				"content": '<?php echo addslashes(JText::_("COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT")); ?>'
			});

			return false;
		};
		<?php } ?>

		// Disable the button to avoid posting multiple times
		button.attr('disabled', true);

		// Change the task to approve
		if (action == 'approve') {
			$('[data-ed-form-task]').val('approvePending');
		}

		// Submit the form
		askForm.submit();

		return false;
	};

	<?php if ($this->config->get('main_similartopic')) { ?>
	// Similar questions
	var title = $('[data-ed-post-title]');
	var sQuestion = $('[data-ed-similar-questions]');
	var listing = $('[data-ed-similar-questions] [data-ed-similar-list]');
	var loader = $('[data-ed-similar-questions-loader]');


	var queryJob = null;
	var menuLock = false;
	var sqLock = false;

	// Bind event on the post title
	title.on('keydown', function(event) {
		var key;

		if (window.event) {
			key = event.keyCode;
		} else if (event.which) {
			key = event.which;
		} else {
			key = 0;
		}

		if (key == 9 || key == 27) {
			sQuestion.addClass('t-hidden');
			return;
		}

		if (sqLock) {
			return;
		}

		clearTimeout(queryJob);

		queryJob = setTimeout(function() {

			sqLock = true;

			// Only search for sugggestions when title is more than 3 characters
			if (title.val().length <= 3) {
				return;
			}

			sQuestion.removeClass('t-hidden');
			sQuestion.addClass('is-loading');

			// clear listing
			listing.html('');

			// Perform an ajax to search now
			EasyDiscuss.ajax('site/views/post/similarQuestion', {
				query: title.val()

			})
			.done(function(data) {

				if (!data) {
					sQuestion.addClass('t-hidden');
					return;
				}

				listing
					.html(data);

				// @TODO: Please update this function with a proper fix.
				// temporary fix to close the post suggestion popup
				similarClose(sQuestion);
			})
			.always(function() {
				// Hide the loader
				sQuestion.removeClass('is-loading');

				sqLock = false;
			})

		}, 1500);

	});

	function similarClose(sQuestion) {
		$(document).click(function (e) {
			// if the target of the click isn't the container nor a descendant of the container
			if (!listing.is(e.target) && listing.has(e.target).length === 0) {
				listing.html('');
				sQuestion.addClass('t-hidden');
			}
		});
	}

	// Bind the click event on closing similar questions
	$(document)
		.on('click.similar.questions.close', '[data-ed-similar-questions-close]', function() {
			sQuestion.addClass('t-hidden');
		});

	$(document)
		.on('mousemove.similar.questions click.similar.questions', '[data-ed-similar-questions]', function() {

			// Focus on the title
			title.trigger('focus');

			// Lock the meu
			menuLock = true;
		})
		.off('mouseout.similar.questions', function() {
			menuLock = false;
		});

	title.on('blur', function() {

		if (menuLock) {
			return;
		}

		sQuestion.addClass('t-hidden');
	});

	<?php } ?>

	// Post types mapping
	$('#category_id').on('change', function() {
		var categoryId = $(this).val();

		<?php if (!$post->id && $this->config->get('main_private_post', false) && $this->my->id) { ?>
		EasyDiscuss.ajax('site/views/post/getPrivateState', {
			"categoryId": categoryId
		}).done(function(checked, enforced) {
			checked = checked == 1 ? true : false;
			enforced = enforced == 1 ? true : false;
				
			if (enforced) {
				$('[data-private-post]').hide();
				return;
			}

			$('[data-private-post]').show();
			$('#private').prop('checked', checked).trigger('change');
		});
		<?php } ?>

		EasyDiscuss.ajax('site/views/post/getPostTypes', {
			"categoryId": categoryId
		}).done(function(html) {
			$('[data-post-types-wrapper]').html(html);
		});
	});

});
