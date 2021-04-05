ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    var wrapper = $('[data-ed-telegram-messages-wrapper]');
    var discoverButton = $('[data-ed-telegram-discover]');
    var dropdownWrapper = $('[data-ed-telegram-messages]');
    var testButton = $('[data-ed-telegram-test]');
    var testButtonWrapper = $('[data-ed-telegram-test-wrapper]');
    var integrationIdWrapper = $('[data-ed-integration-chat-id]');

    // Discover button
    discoverButton.on('click', function() {

		EasyDiscuss.ajax('admin/views/telegram/discover', {

		}).done(function(contents) {

            // Display the section
            wrapper.removeClass('t-hidden');

            // Remove integration so new chat id can be set.
            integrationIdWrapper.remove();

            // Append the messages now
            dropdownWrapper.html(contents);
		})
	});

    // Send test message to chat.
    testButton.on('click', function() {
        EasyDiscuss.ajax('admin/views/telegram/test', {

        }).done(function(contents) {
            testButtonWrapper.append(contents);
        });
    });
});