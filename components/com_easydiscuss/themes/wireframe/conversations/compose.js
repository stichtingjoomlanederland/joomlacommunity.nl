ed.require(['edq', 'easydiscuss', 'markitup', 'jquery.expanding', 'selectize'], function($, EasyDiscuss) {

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
});
