/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

if(!Files) var Files = {};

(function($) {

var CopyMoveDialog = Koowa.Class.extend({
    initialize: function(options) {
        this.supr();

        options = {
            view: $(options.view),
            tree: $(options.view).find('.k-js-tree-container'),
            button: $(options.button, options.view),
            open_button: $(options.open_button)
        };

        this.setOptions(options);
        this.attachEvents();
    },
    attachEvents: function() {
        var self = this;

        if (this.options.open_button) {
            this.options.open_button.click(function(event) {
                event.preventDefault();

                self.show();
            });
        }

        if (this.options.view.find('form')) {
            this.options.view.find('form').submit(function(event) {
                event.preventDefault();

                self.submit();
            });
        }
    },
    show: function() {
        var options = this.options,
            count = Object.getLength(this.getSelectedNodes());

        if (options.open_button.hasClass('unauthorized') || !count) {
            return;
        }

        var data = Files.app.tree.tree('toJson'),
            tree = new Koowa.Tree(options.view.find('.k-js-tree-container'), {
                onCanSelectNode: function(node) {
                    return (node.path != Files.app.getPath());
                }
            });

        tree.tree('loadData', $.parseJSON(data));

        this.getSelectedNodes().each(function(node) {
            var tree_node = tree.tree('getNodeById', node.path);
            if (tree_node) {
                tree.tree('removeNode', tree_node);
            }
        });

        $.magnificPopup.open({
            items: {
                src: $(options.view),
                type: 'inline'
            }
        });
    },
    hide: function() {
        if (this.options.tree instanceof $) {
            this.options.tree.empty();
        }

        $.magnificPopup.close();
    },
    getSelectedNodes: function() {
        return Files.app.grid.nodes.filter(function(row) { return row.checked });
    },
    handleError: function(xhr) {
        var response = JSON.decode(xhr.responseText, true);

        this.hide();

        if (response && response.error) {
            alert(response.error);
        }
    }
});

Files.CopyDialog = CopyMoveDialog.extend({
    submit: function() {
        var self  = this,
            nodes = this.getSelectedNodes(),
            names = Object.values(nodes.map(function(node) { return node.name; })),
            destination = this.options.view.find('.k-js-tree-container').tree('getSelectedNode').path,
            url = Files.app.createRoute({view: 'nodes', folder: Files.app.getPath()});

        if (!names.length) {
            return;
        }

        this.options.button.prop('disabled', true);

        Files.app.grid.fireEvent('beforeCopyNodes', {nodes: nodes});

        $.ajax(url, {
            type: 'POST',
            data: {
                'name' : names, // names are passed in POST to circumvent 2k characters rule in URL
                'destination_folder': destination || '',
                '_action': 'copy',
                'csrf_token': Files.token
            }
        }).done(function(response) {
            var tree = Files.app.tree,
                refresh_tree = false;

            nodes.each(function(node) {
                var tree_node = tree.tree('getNodeById', node.path);
                if (tree_node) {
                    refresh_tree = true;
                }
            });

            Files.app.grid.fireEvent('afterCopyNodes', {nodes: nodes});

            if (refresh_tree) {
                Files.app.tree.fromUrl(Files.app.createRoute({view: 'folders', 'tree': '1', 'limit': '2000'}));
            }

            self.hide();
        }).fail($.proxy(this.handleError, this))
        .always(function() {
            self.options.button.prop('disabled', false);
        });
    }
});

Files.MoveDialog = CopyMoveDialog.extend({
    submit: function() {
        var self  = this,
            nodes = this.getSelectedNodes(),
            names = Object.values(nodes.map(function(node) { return node.name; })),
            destination = this.options.view.find('.k-js-tree-container').tree('getSelectedNode').path,
            url = Files.app.createRoute({view: 'nodes', folder: Files.app.getPath()});

        if (!names.length) {
            return;
        }

        this.options.button.prop('disabled', true);

        Files.app.grid.fireEvent('beforeMoveNodes', {nodes: nodes});

        $.ajax(url, {
            type: 'POST',
            data: {
                'name' : names, // names are passed in POST to circumvent 2k characters rule in URL
                'destination_folder': destination || '',
                '_action': 'move',
                'csrf_token': Files.token
            }
        }).done(function(response) {
            var tree = Files.app.tree,
                refresh_tree = false;

            nodes.each(function(node) {
                if (node.element) {
                    node.element.dispose();
                }

                Files.app.grid.nodes.erase(node.path);

                var tree_node = tree.tree('getNodeById', node.path);
                if (tree_node) {
                    refresh_tree = true;
                }
            });

            Files.app.grid.fireEvent('afterMoveNodes', {nodes: nodes});

            if (refresh_tree) {
                Files.app.tree.fromUrl(Files.app.createRoute({view: 'folders', 'tree': '1', 'limit': '2000'}));
            }

            self.hide();
        }).fail($.proxy(this.handleError, this))
        .always(function() {
            self.options.button.prop('disabled', false);
        });
    }
});

})(window.kQuery);