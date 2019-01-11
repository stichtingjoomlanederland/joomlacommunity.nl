kQuery(function($){
    var grid = $('.k-js-grid-controller'),
        controller = grid.data('controller'),
        delete_button = $('#toolbar-delete'),
        message = Koowa.translate('You cannot delete a category while it still has documents'),
        countDocuments = function() {
            var count = 0;

            Koowa.Grid.getAllSelected().each(function() {
                count += parseInt($(this).data('document-count'), 10);
            });

            return count;
        };

    controller.toolbar.find('a.toolbar').ktooltip({
        placement: 'bottom'
    });

    grid.on('k:afterValidate', function() {
        if (countDocuments()) {
            delete_button.addClass('k-is-disabled');
            delete_button.ktooltip('destroy');
            delete_button.ktooltip({title: message, placement: 'bottom'});
        }
    });

});