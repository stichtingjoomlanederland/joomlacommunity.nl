ed.define('site/src/label', ['edq', 'easydiscuss', 'abstract'], function($, Abstract){

    var App = new Abstract(function(self) { return {

            init: function(label) {

                var element = $(label);
                var postLabel = $('.discuss-post-label');

                // Implement click event on the label
                element.on('click', function() {
                    var labelId = $(this).data('labelid');
                    var postId = $(this).data('postid');

                    EasyDiscuss.ajax('site/views/post/ajaxSaveLabel', {
                        "labelId": labelId,
                        "postId": postId
                    }).done(function(message){
                        postLabel.html(message);
                    });
                });
            }

        }});

    return App;
});
