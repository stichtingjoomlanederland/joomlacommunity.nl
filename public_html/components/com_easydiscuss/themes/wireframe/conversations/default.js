ed.require(['edq', 'easydiscuss', 'site/src/conversations', 'jquery.atwho'], function($, EasyDiscuss) {

	var editor = $('[data-ed-conversation-reply-textarea]');

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
