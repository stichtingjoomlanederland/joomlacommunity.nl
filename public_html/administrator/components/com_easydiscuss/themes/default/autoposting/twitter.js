ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

    $('[data-twitter-login]').on('click', function() {
        var width = 650;
        var height = 500;

        // Get the top and left 
        var top = (screen.height / 2) - (height / 2);
        var left = (screen.width / 2) - (width / 2);
        
        var url = '<?php echo $authorizationURL;?>';

        window.open(url, '', 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
    });

    window.doneLogin = function() {
        window.location.reload();
    };
});