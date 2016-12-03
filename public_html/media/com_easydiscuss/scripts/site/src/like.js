ed.define('site/src/like', ['edq', 'easydiscuss'], function($, EasyDiscuss){

    var button = $('[data-ed-likes-button]');

    button.live('click', function() {

        var self = $(this);
        var wrapper = self.parents('[data-ed-post-likes]');

        // Determines if we should be liking / unliking
        var task = self.data('task');

        // Get the post id
        var postId = self.data('id');

        // Get the like counter 
        var counter = wrapper.find('[data-ed-like-count]');
        var count = parseInt(counter.html());

        // Get the loader
        var loader = wrapper.find('[data-ed-like-loader]');

        // Get the buttons
        var likeButton = wrapper.find(button.selector + '[data-task=like]');
        var unlikeButton = wrapper.find(button.selector + '[data-task=unlike]');


        // Hide the button
        self.hide();

        // Display loading indicator
        loader
            .removeClass('t-hidden');

        // Run an ajax call now 
        EasyDiscuss.ajax('site/views/likes/' + task, {
            "postid": postId
        }).always(function() {

            // Hide the loader
            loader.addClass('t-hidden');
            wrapper.removeClass('has-counter');

        }).done(function(message) {

            // When a person likes a post
            if (task == 'like') {
                count += 1;

                // Hide the like button
                likeButton.hide();
                unlikeButton.show();

                // Add the active and has-counter for the wrapper
                wrapper.addClass('is-active');
            }

            // When a person unlikes a post
            if (task == 'unlike') {
                count -= 1;

                // Hide the like button
                unlikeButton.hide();
                likeButton.show();

                // Remove the is-active class
                wrapper.removeClass('is-active');
            }

            // Update the counter
            counter.html(count.toString());

            if (count > 0) {
                wrapper.addClass('has-counter');
            }

        });
    });
    
    var buttonCounter = $('[data-ed-counter-like]');

    buttonCounter.live('click', function() {

        var self = $(this);
        var wrapper = self.parents('[data-ed-post-likes]');

        // Retrieve the post id
        var postId = self.data('id');

        EasyDiscuss.dialog({
            content: EasyDiscuss.ajax('site/views/likes/popbox',{
                'postid' : postId
            })
        });
    });
});
