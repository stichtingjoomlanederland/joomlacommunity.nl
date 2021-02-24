ed.require(['edq', 'easydiscuss', 'jquery.scrollto'], function($, EasyDiscuss) {

var Alert = {
	element: '[data-ed-alert]',
	getElement: function() {
		var alert = $(this.element);

		return alert;
	},
	
	focus: function() {
		$(document).scrollTo(this.getElement());
	},

	hide: function() {
		var element = this.getElement();
		element.html('').addClass('t-d--none');
	},

	show: function(message) {
		var element = this.getElement();

		element
			.html(message)
			.removeClass('t-d--none');

		this.focus();
	}
};

/**
 * Approve posts
 */
$(document).on('click.post.approve', '[data-ed-approve-button]', function() {
	var element = $(this);
	var postId = element.data('id');

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

/**
 * Rejects a post
 */
$(document).on('click.post.reject', '[data-ed-reject-button]', function() {
	var element = $(this);
	var postId = element.data('id');

	EasyDiscuss.dialog({
		content: EasyDiscuss.ajax('site/views/dashboard/confirmRejectPost', {
			'id' : postId
		})
	});
});


var saveState = $.Deferred();

/**
 * Submit button
 */
$(document).on('click.post.submit', '[data-ed-submit-button]', function() {

	var deferredObjects = [];
	var element = this;

	$(document).trigger('onSubmitPost', [deferredObjects]);

	if (deferredObjects.length <= 0) {
		saveState.resolve();
	} 

	if (deferredObjects.length > 0) {
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


	// Perform validations
	var title = $('[data-ed-post-title]');
	var minimumTitle = title.data('minimum-title');

	// re-check again if required title field already fill in
	title.parent().removeClass('has-error');

	if (title.val() == '' || title.val().length < minimumTitle) {
		title.parent().addClass('has-error');

		var message = '<?php echo JText::_("COM_EASYDISCUSS_POST_TITLE_TOO_SHORT"); ?>';

		if (title.val() == '') {
			message = '<?php echo JText::_("COM_EASYDISCUSS_POST_TITLE_EMPTY"); ?>';
		}

		Alert.show(message);

		return false;
	}

	<?php if ($this->config->get('main_post_title_limit')) { ?>
	var maxCharacters = <?php echo $this->config->get('main_post_title_chars'); ?>;
		
	if (title.val().length > maxCharacters) {
		title.parent().addClass('has-error');

		Alert.show('<?php echo JText::sprintf("COM_ED_POST_TITLE_EXCEEDED_LIMIT", $this->config->get('main_post_title_chars')); ?>');

		return false;
	}
	<?php } ?>

	var category = $('#category_id');

	// re-check again if required category field already selected
	category.parent().removeClass('has-error');

	if (category.val() == 0 || category.val().length == 0) {

		category.parent().addClass('has-error');

		Alert.show('<?php echo JText::_("COM_EASYDISCUSS_POST_CATEGORY_EMPTY"); ?>');

		return false;
	}

	// Get the content from the composer
	var content = $('[data-ed-editor]');

	// re-check again if required content textarea already fill in
	content.parent().removeClass('has-error');

	if (content.val() == "") {
		content.parent().addClass('has-error');

		Alert.show('<?php echo JText::_("COM_EASYDISCUSS_POST_CONTENT_EMPTY"); ?>');

		return false;
	}


	// Check for t&c if the system is enabled.
	<?php if ($this->config->get('main_tnc_question')) { ?>
	var tnc = $('[data-ed-tnc-checkbox]');

	if (!tnc.is(':checked')) {
		Alert.show('<?php echo addslashes(JText::_("COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT")); ?>');

		return false;
	};
	<?php } ?>

  	
  	// Other validations
  	var validateState = $.Deferred();
  	var tasks = [];
  	
  	$(document).trigger('onValidatePost', [tasks, button]);


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

		// Disable the button to avoid posting multiple times
		button.attr('disabled', true);

		// Change the task to approve
		if (action == 'approve') {
			$('[data-ed-form-task]').val('approvePending');
		}

		// Submit the form
		$('[data-ed-ask-form]').submit();

		return false;

	})
	.fail(function() {
		
	});

};

var toHide = function(elements) {
	elements.forEach(function(el) {
		el.addClass('t-d--none');
	});
};

var toShow = function(elements) {
	elements.forEach(function(el) {
		el.removeClass('t-d--none');
	});
};

<?php if (!$post->isNew()) { ?>
var editAliasButton = $('[data-alias-edit]');
var updateAliasButton = $('[data-alias-update]');
var cancelAliasButton = $('[data-alias-cancel]');
var inputWrapper = $('[data-alias-input-wrapper]');
var inputAlias = inputWrapper.find('[data-alias-input]');
var aliasPreview = $('[data-alias-preview]');
var currentAlias = inputAlias.val();

editAliasButton.on('click', function() {
	toHide([$(this), aliasPreview]);

	toShow([updateAliasButton, cancelAliasButton, inputWrapper]);
});

cancelAliasButton.on('click', function() {
	toShow([editAliasButton, aliasPreview]);

	var elToHide = [$(this), updateAliasButton, inputWrapper];

	// Hide back the alias preview if there is no new input
	if (!inputAlias.val()) {
		elToHide.push(aliasPreview);
	}

	// Reset back to the current alias
	inputAlias.val(currentAlias);

	toHide(elToHide);
});

updateAliasButton.on('click', function() {
	var newAlias = inputAlias.val();

	// Get and display the correct alias that will be used if there is new alias
	if (currentAlias != newAlias) {
		currentAlias = newAlias;

		EasyDiscuss.ajax('site/views/post/getAlias', {
			"id": <?php echo $post->id; ?>,
			"alias": newAlias
		}).done(function(alias) {
			// Update the alias preview link
			aliasPreview.html(alias);
		});
	}

	toShow([editAliasButton, aliasPreview]);

	var elToHide = [$(this), cancelAliasButton, inputWrapper];

	// Hide back the alias preview if there is no new input
	if (!newAlias) {
		elToHide.push(aliasPreview);
	}

	toHide(elToHide);
});
<?php } ?>

/**
 * Triggered when category is updated
 */
$(document).on('change.post.category', '#category_id', function() {
	var categoryId = $(this).val();
	var postId = "<?php echo $post->id; ?>";
	var operation = "<?php echo $operation; ?>";

	<?php if (!$post->id && $this->config->get('main_private_post', false) && $this->my->id) { ?>
	// Get the private state for the post
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

	// Populate the post types
	EasyDiscuss.ajax('site/views/post/getPostTypes', {
		"categoryId": categoryId
	}).done(function(html) {
		$('[data-post-types-wrapper]').html(html);
	});


	var editorUuid = $("[data-ed-editor-wrapper]").data('ed-editor-uuid');

	// Populate tabs
	EasyDiscuss.ajax('site/views/post/updateAllowedTabs', {
		"categoryId": categoryId,
		"operation": operation,
		"postId": postId,
		"editorUuid": editorUuid,
		"view": "ask"
	}).done(function(html) {

		// Update the contents of the tabs
		$('[data-ed-editor-tabs]').html(html);
	});
});



});
