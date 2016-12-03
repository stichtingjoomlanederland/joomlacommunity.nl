ed.require(['edq'], function($) {

    $(document)
        .on('click', '[data-ed-subscribe-category]', function() {

            var id = $(this).data('category-id');
            
            EasyDiscuss.dialog({
                content: EasyDiscuss.ajax('site/views/index/subscribe', { 
                    "type": "category",
                    "id": id
                })
            })
        }); 
});
