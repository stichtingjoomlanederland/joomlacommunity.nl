
window.addEvent('domready', function(){
    var interval = setInterval(function () {
        if (typeof Files.app === 'undefined') {
            return;
        }

        clearInterval(interval);

        if (kQuery('#files-upload-multi').length === 0) {
            return;
        }

        Files.app.addEvent('uploadFile', function(row) {
            Files.app.selected = row.path;

            kQuery('#insert-document').trigger('click');
        });

        document.id('insert-button-container').adopt(document.id('document-insert-form'));

        Files.app.grid.removeEvents('clickImage');

        var evt = function(e) {
            var target = document.id(e.target).getParent('.files-node');
            var row = target.retrieve('row');

            Files.app.selected = row.path;
            document.id('insert-document').set('disabled', false)
                .getParent().setStyle('display', 'block');
        };
        Files.app.grid.addEvent('clickFile', evt);
        Files.app.grid.addEvent('clickImage', evt);

        Files.app.grid.addEvent('clickImage', function(e) {
            var target = document.id(e.target),
                node = target.getParent('.files-node-shadow') || target.getParent('.files-node');

            node.getParent().getChildren().removeClass('active');
            node.addClass('active');
            var row = node.retrieve('row');
            var copy = Object.append({}, row);
            copy.template = 'details_image';

            Files.app.preview.empty();

            copy.render('compact').inject(Files.app.preview);

            var url = Files.app.createRoute({option: 'com_docman', view: 'file', format: 'html', routed: 1,
                folder: copy.folder, name: copy.name});
            Files.app.preview.getElement('img').set('src', url);
        });

        // Select the initial file for preview
        var url = Files.app.getUrl();
        if (url.getData('file')) {
            var select = url.getData('file').replace(/\+/g, ' ');
            select = Files.app.active ? Files.app.active+'/'+select : select;
            var node = Files.app.grid.nodes.get(select);

            if (node && node.element) {
                var event = node.filetype === 'image' ? 'clickImage' : 'clickFile';
                Files.app.grid.fireEvent(event, [{target: node.element.getElement('a')}]);
            }
        }
    }, 100);
});