ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$('[data-eparser-test]').on('click', function() {

		// clear message
		$('#test-result')
			.removeClass('alert')
			.html('');

		var server      = $('input[name=main_email_parser_server]').val();
		var port        = $('input[name=main_email_parser_port]').val();
		var service     = $('#main_email_parser_service').val();
		var ssl         = $('input[name=main_email_parser_ssl]').val();
		var user        = $('input[name=main_email_parser_username]').val();
		var pass        = $('input[name=main_email_parser_password]').val();
		var validate    = $('input[name=main_email_parser_validate]').val();

		EasyDiscuss.ajax('admin/views/settings/testParser', {
			"server": server,
			"port": port,
			"service": service,
			"ssl": ssl,
			"user": user,
			"pass": pass,
			"validate": validate
		})
		.done(function(msg){

			$('#test-result').html(msg);

			// alert 'hello';

		})
		.fail(function(msg) {

			$('#test-result')
				.addClass('alert')
				.html(msg);

		});

	});

});
