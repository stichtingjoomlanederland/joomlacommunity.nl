ed.require(['edq'], function($) {

    // Apply event on category toggle
    $('[data-ed-category-toggle]')
        .on('click', function(){

            var id = $(this).data('id');
            var childs = $('[data-item][data-parent-id="' + id + '"]');

            // Display the childs
            childs.toggle();
        });

});
