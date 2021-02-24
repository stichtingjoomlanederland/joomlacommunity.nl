ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

var resultElement = $('[data-test-result]');

var resetTestResult = function() {
	
	resultElement
		.addClass('t-hidden')
		.html('');
};

var setMessage = function(message, className) {

	resultElement.removeClass('t-hidden')
		.html(message);

	if (className) {
		resultElement.addClass(className);
	}
}

$('[data-test]').on('click', function() {
	var element = $(this);
	
	element.addClass('is-loading');

	resetTestResult();

	var opts = [
		'server',
		'port',
		'service',
		'ssl',
		'username',
		'password',
		'validate'
	];

	var data = {};

	$(opts).each(function(i, key) {
		var input = $('input[name=main_email_parser_' + key + ']');
		var value = input.val();

		data[key] = value;
	});

	EasyDiscuss.ajax('admin/views/settings/testParser', data)
	.done(function(msg) {
		setMessage(msg);
	})
	.fail(function(msg) {
		setMessage(msg, 'alert');
	})
	.always(function() {
		element.removeClass('is-loading');
	});

});

});
