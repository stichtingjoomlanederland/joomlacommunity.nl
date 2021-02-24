ed.require(['edq', 'easydiscuss', 'markitup', 'jquery.expanding', 'jquery.atwho', 'site/vendors/prism'], function($, EasyDiscuss) {

	var wrapper = $('[data-ed-editor-wrapper]');

	// We'll need to loop through to get all editor available.
	// Example: in edit profile page, we'll 2 editor rendered.
	$.each(wrapper, function(i, item) {
		var editor = $(this).find('[data-ed-editor]');

		// Apply markitup for each editor.
		// Make sure only apply to new editor.
		if (editor.parents('.markItUp').length > 0) {
			return;
		}

		editor
			.markItUp({
				onTab: {
					keepDefault: false,
					replaceWith: '    '
				},
				previewParserVar: 'data',
				markupSet: EasyDiscuss.bbcode
			})
			.expandingTextarea();

		// Apply mentions
		<?php if ($this->config->get('main_mentions') && $this->my->id) { ?>
			editor
				.atwho({
					at: "@",
					highlightFirst: false,
					minLen: 2,
					delay: 200,
					data: [],
					limit: 10,
					callbacks: {
						remoteFilter: function(query, callback) {

							EasyDiscuss.ajax('site/views/users/search', {
								"query": query
							}).done(function(result) {

								var users = [];

								$.each(result, function(i, item) {
									users.push(item.title);
								});

								callback(users);
							});

						},

						beforeInsert: function(value, $li) {

							// console.log(value, $li);

							value = value + '#'

							return value;
						},

						afterMatchFailed: function(at, el) {
							// 32 is spacebar
							if (at == '#') {
								tags.push(el.text().trim().slice(1));
								this.model.save(tags);
								this.insert(el.text().trim());
								return false;
							}
						}
					}
				});
		<?php } ?>

		// Only allow admins/moderators to use slash commands
		<?php if ($canSlashCommand) { ?>
			var slashActions = <?php echo json_encode($commands['actions']); ?>;
			editor
				.atwho({
					at: "/",
					highlightFirst: false,
					minLen: 0,
					delay: 200,
					data: slashActions,
					insertTpl: function(val) {

							if (val.name == 'label') {
								return '/label ~';
							}

							return '${atwho-at}${name}';
						},
					limit: 10
				});

			var slashLabels = <?php echo json_encode($commands['labels']); ?>;
			
			editor
				.atwho({
					at: "~",
					highlightFirst: false,
					minLen: 0,
					delay: null,
					data: slashLabels,
					limit: 10
				});
		<?php } ?>
		
		// this is to trigger atwho for /label ~
		editor.on("inserted.atwho", function($li, query) {
			var e = jQuery.Event("keyup", { keyCode: 39 });
			editor.trigger(e);
		});
	});

	var previewButton = $('[data-ed-preview]');

	previewButton.on('click', function(e) {
		e.preventDefault();

		var wrapper = $(this).parents('[data-ed-editor-wrapper]');

		var previewContent = wrapper.find('[data-editor-preview-content]');
		var previewId = wrapper.find('[data-editor-preview-wrapper]');
		var previewNotice = wrapper.find('[data-editor-preview-notice]');
		var editor = wrapper.find('[data-ed-editor]');

		// Remove existing contents.
		previewContent.html('');
		previewId.addClass('is-loading');
		previewNotice.parent().removeClass('has-error');

		// Find the wrapper for the reply form.
		var content = editor.val();

		if (content == '') {
			previewId.removeClass('is-loading');
			previewContent.html("<?php echo JText::_('COM_ED_NOTHING_TO_PREVIEW');?>");
			return;
		}

		EasyDiscuss.ajax('site/views/ask/preview', {
			'content': content
		}).done(function(formatted, notice = '') {
			if (notice.length) {
				previewNotice.parent().addClass('has-error');
				previewNotice.html(notice);
			}

			previewId.removeClass('is-loading');
			previewContent.html(formatted);
			Prism.highlightAll();
		});
	});
});
