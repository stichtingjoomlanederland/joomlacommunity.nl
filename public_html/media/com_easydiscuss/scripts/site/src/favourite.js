ed.define('site/src/favourite', ['edq', 'easydiscuss'], function($, EasyDiscuss){

    var button = $('[data-ed-fav-button]');

    button.live('click', function() { 

        var self = $(this);
        var wrapper = self.parents('[data-ed-post-favourite]');

        // Determine the current task
        var task = self.data('task');

        // Retrieve the post id
        var postId = self.data('id');

        // Get the favourite counter
        var counter = wrapper.find('[data-ed-fav-count]');
        var count = parseInt(counter.html());

        // Get the loader
        var loader = wrapper.find('[data-ed-fav-loader]');

        // Get the like text
        var text = wrapper.find('[data-original-title]');

        // Get the buttons
        var favButton = wrapper.find(button.selector + '[data-task=favourite]');
        var unfavButton = wrapper.find(button.selector + '[data-task=unfavourite]');

         // Hide the button
        self.hide();

        // Display loading indicator
        loader.removeClass('t-hidden');

        // Ajax call
        EasyDiscuss.ajax('site/views/favourites/' + task, {
            'postid' : postId

        }).always(function() {

            // Hide the loader
            loader.addClass('t-hidden');
            wrapper.removeClass('has-counter');
            
        }).done(function(message) {

            // When an user favourite a post
            if (task == 'favourite') {
                count += 1;

                // Hides the fav button
                favButton.hide();
                unfavButton.show();

                // Add the active and has-counter for the wrapper
                wrapper.addClass('is-active');
            }

            if (task == 'unfavourite') {
                count -= 1;

                // shows the fav button
                favButton.show();
                unfavButton.hide();

                // Add the active and has-counter for the wrapper
                wrapper.removeClass('is-active');
            }

            // Update the counter
            counter.html(count.toString());

            if (count > 0) {
                wrapper.addClass('has-counter');
            }

        });
    });

    var buttonCounter = $('[data-ed-counter-fav]');

    buttonCounter.live('click', function() {

        var self = $(this);
        var wrapper = self.parents('[data-ed-post-favourite]');

        // Retrieve the post id
        var postId = self.data('id');

        EasyDiscuss.dialog({
            content: EasyDiscuss.ajax('site/views/favourites/popbox',{
                'postid' : postId
            })
        });
    });
});