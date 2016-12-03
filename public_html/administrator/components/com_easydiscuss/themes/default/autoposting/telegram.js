ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    var wrapper = $('[data-ed-telegram-messages-wrapper]');
    var discoverButton = $('[data-ed-telegram-discover]');
    var dropdownWrapper = $('[data-ed-telegram-messages]');

    // Discover button
    discoverButton.on('click', function() {

		EasyDiscuss.ajax('admin/views/telegram/discover', {

		}).done(function(contents) {

            // Display the section
            wrapper.removeClass('hide');

            // Append the messages now
            dropdownWrapper.html(contents);
		})
	});
});