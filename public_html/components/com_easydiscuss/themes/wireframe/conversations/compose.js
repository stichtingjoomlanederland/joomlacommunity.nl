ed.require(['edq', 'easydiscuss', 'markitup', 'jquery.expanding', 'selectize', 'jquery.atwho'], function($, EasyDiscuss) {

	var message = $('[data-ed-conversation-message]');
	var recipient = $('[data-ed-conversation-recipient]');

	recipient.selectize({
		persist: false,
		createOnBlur: false,
		create: false,
		valueField: 'id',
		labelField: 'title',
		searchField: 'title',
		hideSelected: true,
		closeAfterSelect: true,
		selectOnTab: true,
		options: [],
		load: function(query, callback) {

			// If the query was empty, don't do anything here
			if (!query.length) {
				return callback();
			}

			// Search for users
			EasyDiscuss.ajax('site/views/users/search', {
				"query": query,
				"exclude": "<?php echo $this->my->id;?>"
			}).done(function(users) {

				callback(users);

			});
		},

		onDropdownOpen: function() {
			$('div.ed-messaging div.form-group').addClass('has-dropdown');
		},

		onDropdownClose: function(dropdown) {
			setTimeout(function () {
				$('div.ed-messaging div.form-group').removeClass('has-dropdown');
			}, 200);

		},


		render: {
			option: function(item, escape) {

				return '<div>' +
					'<img src="' + escape(item.avatar) + '" width="16" class="t-lg-mr--md">' +
					'<span class="title">' +
						'<span class="name">' + escape(item.title) + '</span>' +
					'</span>' +
				'</div>';
			}
		}
	});

	// Apply markitup
	message.markItUp({
		markupSet: EasyDiscuss.bbcode
	});

	// Apply textarea expanding
	message.expandingTextarea();

	message
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
});
