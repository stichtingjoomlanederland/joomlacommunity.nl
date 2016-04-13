ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {
    
    // moderator listing
    var moderatorLoader = $('[data-ed-post-moderator-listing]');
    
    // Loading spin
    var loader = $('[data-ed-post-moderator-loading]');

    // Bind the assign moderator buttons
    $(document)
        .on('click', '[data-ed-post-moderator-button]', function() {    

            var postId = $('[data-ed-post-id]').data('ed-post-id');
            var categoryId = $('[data-ed-post-category-id]').data('ed-post-category-id');
            var moderatorList = $('[data-ed-moderator-item]');

            // check how much for the moderator list 
            length = moderatorList.size();

            // no need load again if already loaded a list of moderator
            if (length > 0) return;

            // add loading icon into list when searching
            moderatorLoader.append(loader);

            EasyDiscuss.ajax('site/views/post/getModerators', {
                'id' : postId,
                'category_id' : categoryId
            })
            .done(function(msg) { 
                   //remove loader
                    moderatorLoader.empty();

                    moderatorLoader.append(msg);

                })
            .fail(function(msg) {
                   moderatorLoader.empty();

                   moderatorLoader.append(msg);
                })

        });


    var assignStatus = $('[data-ed-post-assign]');

    // assign moderator
    $(document)
        .on('click', '[data-ed-moderator-item]', function(e) {

            // this is to prevent event get triggered twice.
            e.stopImmediatePropagation();

            var moderatorId = $(this).data('ed-moderator-id');
            var postId = $(this).data('ed-post-id');

            EasyDiscuss.ajax('site/views/post/ajaxModeratorAssign', {
                    'moderatorId': moderatorId,
                    'postId': postId

                }).done(function(msg) {
                    assignStatus.html(msg);

                }).fail(function(msg) {
                    assignStatus.html(msg);
                });

        });

});