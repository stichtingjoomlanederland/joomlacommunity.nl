ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    window.testParser = function() {
        var server      = EasyDiscuss.$('input[name=main_email_parser_server]').val();
        var port        = EasyDiscuss.$('input[name=main_email_parser_port]').val();
        var service     = EasyDiscuss.$('#main_email_parser_service').val();
        var ssl         = EasyDiscuss.$('input[name=main_email_parser_ssl]').val();
        var user        = EasyDiscuss.$('input[name=main_email_parser_username]').val();
        var pass        = EasyDiscuss.$('input[name=main_email_parser_password]').val();
        var validate    = EasyDiscuss.$('input[name=main_email_parser_validate]').val();

        disjax.load( 'settings' , 'testParser' , server , port , service , ssl , user , pass , validate );
    }

});