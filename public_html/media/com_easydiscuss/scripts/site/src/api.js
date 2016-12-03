ed.define('api', ['edjquery', 'easydiscuss', 'markitup'], function($, EasyDiscuss) {

    // This is where we define all the api calls that is needed.
    $(document)
        .on('click.conversation', '[data-ed-conversations-api]', function() {

            // It needs to contain the user id otherwise this will not work
            var id = $(this).data('userid');

            // Displays the dialog to start a conversation.
            EasyDiscuss.dialog({
                content: EasyDiscuss.ajax('site/views/conversation/compose', {
                    "id": id
                }),
                bindings: {
                    "init": function() {
                        console.log('initializing');
                    }
                }
            });

        });

});
