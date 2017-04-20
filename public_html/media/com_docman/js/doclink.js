/**
 * DOCman Doclink
 *
 * Customized instance of jqTree to render a list of menu items, sometimes with categories in a tree structure.
 * It renders a split view, sidebar to the left and a table layout of documents to the right.
 * It lets you select 3 different kind of links in a dialog, menu item, category or a document link.
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @requires    DOCman.Tree.Categories, Koowa.Class, jqTree, kQuery
 */

var DOCman = DOCman || {};

(function($) {

DOCman.Doclink = Koowa.Class.extend({

    options: {
        editor: null,
        ajax: {
            url: '',
            data: {
                option: 'com_docman',
                view: 'documents',
                format: 'json',
                category: 0,
                limit: 100,
                offset: 0,
                enabled: 1,
                sort: 'created_on',
                direction: 'desc',
                fields: 'title,alias,category_slug,publish_date,storage_path,itemid,access_title,icon'
            }
        }
    },

    initialize: function(options){

        // Setting the selectNode event as an option, binding the context to 'this'
        this.options.onSelectNode = $.proxy(this.selectNode, this);

        this.setOptions(options);

        this.breadcrumbs = $('div.k-breadcrumb');

        // shortcut to tbody, used in several methods and event handlers
        this.tbody = $('#document_list tbody');
        this.search = $('.js-search-container input');
        // another shortcut, needed as the table is emptied now and then and we can't have this el dissappear
        this.initial_row = this.tbody.find('.initial-row').clone();

        var self = this;
        $('#insert-image').on('click', function(event) {
            event.preventDefault();

            window.parent.jInsertEditorText(self.getLinkString(), self.options.editor);

            if (window.parent.SqueezeBox) {
                window.parent.SqueezeBox.close();
            }
        });

        var previous_value = this.search.val(),
            filter = function(event) {
                var $this = $(this),
                    value = $this.val();

                if (event.which === 13 || event.type === 'blur') {
                    if (self.active) {
                        if (previous_value != value) {
                            self.selectNode(self.active);
                        }

                        if (event.type !== 'blur') {
                            // Unbind our event first to not run this method again
                            $this.off('blur.doclink');
                            $this.blur();
                            $this.on('blur.doclink', filter);
                        }
                    }
                }

                previous_value = value;
            };

        this.search.on('keypress.doclink', filter)
                   .on('blur.doclink', filter);

        this.search.siblings('.search_button--empty').click(function() {
            if (self.search.val()) {
                self.search.val('');
                self.search.trigger('blur');
            }
        });

        if (window.parent.tinyMCE) {
            var text = window.parent.tinyMCE.activeEditor.selection.getContent({format:'raw'});
            if (text) {
                this.caption_from_editor = true;
                $('#caption').attr('value', text);
            }
        }

        new DOCman.Doclink.Tree('#documents-sidebar', this.options);

        this.spinner = $('.k-js-doclink-spinner');

        // Sortable
        $("#document_list").footable().bind("footable_sorted", function(event) {
            self.setSort(event.column.name, event.direction.toLowerCase());
        });
    },
    setSort: function(column, direction) {
        var th    = $('.footable-sortable'),
            icons = $('.footable-sort-indicator');

        icons.hide();

        var icon = th.filter('[data-name="'+column+'"]').find('.footable-sort-indicator');

        icon.removeClass('k-icon-sort-ascending').removeClass('k-icon-sort-descending')
            .addClass('k-icon-sort-'+direction+'ending')
            .show();

        this.options.ajax.data.sort = column;
        this.options.ajax.data.direction = direction;
    },
    startSpinner: function() {
        this.spinner.removeClass('k-is-hidden');
    },
    stopSpinner: function() {
        this.spinner.addClass('k-is-hidden');
    },
    updateProperties: function(data) {
        if(data.type === 'Document') {
            $('#documents-sidebar').removeClass('focus');
        } else {
            $('#documents-sidebar').addClass('focus');
        }

        if (data.type === 'Menu' || data.type === 'Category') {
            this.link_target = data.target;
            this.language_tag = data.tag;
        }

        if (this.language_tag && data.url) {
            data.url += '&lang='+this.language_tag;
        }

        this.link_type = data.type;

        $('#insert-image').text(this.options.lang['insert_'+data.type.toLowerCase()]);
        $('#url').attr('value', data.url || '');

        if (this.caption_from_editor !== true) {
            $('#caption').attr('value', data.title || '');
        }

    },

    getLinkString: function() {
        var href = $('#url').attr('value'),
            caption = $('#caption').attr('value'),
            target = (this.link_type === 'Document' && this.link_target === 'blank') ? ' target="_blank"' : '',
            str = '';

        str += ' <a class="doclink" href="'+href+'"'+target+'>';
        str += caption;
        str += '</a>';

        return str;
    },

    request: function(data){
        var url = this.options.ajax.url,
            count = 0,
            deferred = $.Deferred(),
            fail = function(xhr){
                if (xhr.status === 0 || xhr.readyState === 0) {
                    return;
                }

                deferred.reject(xhr.responseText);
            },
            done = function(response) {
                if (typeof response.entities === 'undefined' || typeof response.meta === 'undefined') {
                    deferred.reject('');

                    return;
                }

                count += response.entities.length;

                deferred.notify(response.entities);

                if (response.meta.offset + response.entities.length < response.meta.total) {
                    $.getJSON(url, $.extend(data, {offset: response.meta.offset+response.meta.limit})).done(done).fail(fail);
                } else {
                    deferred.resolve(count);
                }
            };

        data = $.extend(true, {}, this.options.ajax.data, data);

        this.startSpinner();
        this.setSort(data.sort, data.direction);

        $.getJSON(url, data).fail(fail).done(done);

        return deferred.promise();
    },

    selectNode: function(node) {
        var self = this,
            tbody = this.tbody,
            initial_row = this.initial_row,
            renderRow = function(index, item) {
                item.itemid = node.itemid;

                var icon = '';

                if (item.links && item.links.icon && item.links.icon.href) {
                    icon = '<span class="koowa_header__image_container">' +
                        '<img src="'+item.links.icon.href+'" class="koowa_header__image" />' +
                        '</span> ';
                }
                else if (item.icon) {
                    icon = '<span class="k-icon-document-'+item.icon+'"></span> ';
                }

                // Split timestamp into [ Y, M, D, h, m, s ]
                var parts = item.publish_date.split(/[- :]/),
                    date  = parts[2]+' '+Koowa.Date.getMonthName(parts[1], true)+' '+parts[0],
                    row = $('<tr><td class="k-table-data--ellipsis" data-value="'+item.title+'">'+icon+'<a href="#">'+item.title+'</a> <small>'+item.storage_path+'</small></td>' +
                        '<td>'+item.access_title+'</td>' +
                        '<td class="k-table-data--nowrap" data-value="'+item.publish_date+'">'+date+'</td></tr>');

                row.on('click', 'a', function(event){
                    event.preventDefault();

                    row.siblings('.k-is-selected').removeClass('k-is-selected');
                    row.addClass('k-is-selected');

                    self.selectDocument(item);
                });

                tbody.append(row);

                self.setTableState('documents');

            },
            onProgress = function(entities) {
                $.each(entities, renderRow);
            },
            onFail = function(response) {
                self.stopSpinner();

                try {
                    var resp = $.parseJSON(response),
                        error = resp && resp.error ? resp.error : 'An error occurred during request';
                    alert(error);
                } catch(e) {
                    alert('JSON parse error'); // if the response code is 200 and response is an error html page, lets alert
                }
            },
            onFinished = function(count) {
                self.stopSpinner();

                // No results
                if (!count) {
                    self.setTableState('nodocs');

                    tbody.empty();
                }
            };

        this.active = node;

        var request = null;

        if(node.getLevel() < 2) {
            this.selectMenu(node);

            this.setTableState('empty');

            if (node.view === 'flat') {
                request = {
                    'Itemid': node.itemid, 'search': this.search.val(), 'category': '',
                    'sort': this.sort, 'direction': this.direction
                };
            }
        } else {
            this.selectCategory(node);

            request = {
                'Itemid': node.itemid, 'search': this.search.val(), 'category': node.category_id,
                'sort': this.sort, 'direction': this.direction
            };
        }

        if (request) {
            tbody.empty();

            this.request(request).progress(onProgress).done(onFinished).fail(onFail);
        }
    },
    setTableState: function(active) {
        $('.k-js-doclink-table-state').hide();
        $('.k-js-doclink-table-'+active).show();
    },

    selectMenu: function(data){
        var properties = {
            type: 'Menu',
            url:'index.php?Itemid='+data.itemid,
            tag: data.tag,
            title: data.name,
            target: data.target
        };

        this.updateProperties(properties);
    },

    selectCategory: function(node) {
        var data = {
            type: 'Category',
            url:'index.php?option=com_docman&view=list&slug='+node.slug+'&Itemid='+node.itemid,
            tag: node.tag,
            title: node.name,
            target: node.target
        };

        this.updateProperties(data);

        this.breadcrumbs.find('.js-breadcrumb-category').show()
            .find('span').text(node.name);
    },

    selectDocument: function(row) {
        var data = {
            type: 'Document',
            url:'index.php?option=com_docman&view=document&alias='+row.alias+'&category_slug='+row.category_slug+'&Itemid='+row.itemid,
            title: row.title
        };
        this.updateProperties(data);
    }
});

DOCman.Doclink.Tree = DOCman.Tree.Categories.extend({

    getDefaults: function(){
        var defaults = {
            autoOpen: 0 //Auto open level 0 nodes, which are menu items
        };

        return $.extend(true, {}, this.supr(), defaults); // get the defaults from the parent and merge them
    },

    /* Transforms flat children arrays with parent ids into an hierarchial object structure supported by jqTree */
    parseData: function(list){

        var self = this, data = [];

        $.each(list, function(key,item){

            if(item.children.length > 0) {
                var children = self._parseData(item.children); //Parse the children into an hierarchy
                item.children.length = 0; //Empties the array, freeing up memory
                item.children = children; //Setting the children property again, with the hierarchial data
            }
            data.push(item);
        });

        return data;
    },

    attachHandlers: function(){

        this._attachHandlers(); // Attach needed events from Koowa.Tree._attachHandlers

        var options = this.options,
            self = this,
            getIconElement = function(node) {
                var folder = $(node.element).find('.jqtree-icon');
                return $(folder.get(0));
            };

        this.element.bind({
            'tree.init': function() {
                // Add menu icons
                self.tree('getTree').iterate(function(node) {
                    var icon = 'k-icon-menu' + (node.is_open ? '-opened' : '-closed');
                    getIconElement(node)
                        .removeClass('k-icon-folder-closed')
                        .removeClass('k-icon-folder-opened')
                        .addClass(icon);
                });
            },
            'tree.select': function(event) {
                // The select event happens when a node is clicked
                var element;
                if(event.node) { // When event.node is null, it's actually a deselect event
                    element = $(event.node.element);

                    getIconElement(event.node)
                        .removeClass('k-icon-folder-closed')
                        .addClass('k-icon-folder-opened');

                    //Fire custom select node handler
                    options.onSelectNode(event.node);
                }

                // Removing active styling from previous and deselected nodes
                if(event.previous_node || event.deselected_node) {
                    var deselected = event.previous_node || event.deselected_node;

                    getIconElement(deselected)
                        .removeClass('k-icon-folder-opened')
                        .addClass('k-icon-folder-closed');
                }
            },
            'tree.open': function(event) {
                // Animate a scroll to the node being opened so child elements scroll into view
                self.scrollIntoView(event.node, self.element.closest('.k-flex-wrapper'), 300);

                // Add open menu icon
                if (!event.node.parent.parent) { // Top level
                    getIconElement(event.node)
                        .removeClass('k-icon-folder-closed')
                        .removeClass('k-icon-folder-opened')
                        .removeClass('k-icon-menu-closed')
                        .addClass('k-icon-menu-opened');
                }
            },
            'tree.close': function(event) {
                // Add closed menu icon
                if (!event.node.parent.parent) { // Top level
                    getIconElement(event.node)
                        .removeClass('k-icon-folder-closed')
                        .removeClass('k-icon-folder-opened')
                        .removeClass('k-icon-menu-opened')
                        .addClass('k-icon-menu-closed');
                }
            }
        });

    }
});

})(kQuery);
