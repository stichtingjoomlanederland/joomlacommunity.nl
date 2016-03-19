/**
 * Akeeba Backup
 *
 * @package    akeeba
 * @copyright  Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license    GNU GPL version 3 or, at your option, any later version
 **/

// Object initialisation
if (typeof akeeba == 'undefined')
{
    var akeeba = {};
}

if (typeof akeeba.Fsfilters == 'undefined')
{
    akeeba.Fsfilters = {
        translations: {},
        currentRoot: null,
        loadingGif: ''
    }
}

(function($){
    akeeba.Fsfilters.activeRootChanged = function()
    {
        var data = {};
        data.root = $('#active_root').val();
        data.crumbs = [];
        data.node = '';
        akeeba.Fsfilters.load(data);
    };

    /**
     * Loads the contents of a directory
     *
     * @param  data
     */
    akeeba.Fsfilters.load = function(data)
    {
        // Add the verb to the data
        data.verb = 'list';

        // Convert to JSON
        var json = JSON.stringify(data);

        // Assemble the data array and send the AJAX request
        var new_data = {};
        new_data.action = json;

        akeeba.System.doAjax(new_data, function(response)
        {
            akeeba.Fsfilters.render(response);
        }, null, false, 15000);
    };

    /**
     * Toggles a filesystem filter
     */
    akeeba.Fsfilters.toggle = function (data, caller, callback, use_inner_child)
    {
        if (use_inner_child == null)
        {
            use_inner_child = true;
        }

        // Make the icon spin
        if (caller != null)
        {
            // Do not allow multiple simultaneous AJAX requests on the same object
            if (caller.data('loading') == true)
            {
                return;
            }

            caller.data('loading', true);

            if(use_inner_child)
            {
                var icon_span = caller.children('span:first');
            }
            else
            {
                var icon_span = caller;
            }

            caller.data('icon', icon_span.attr('class') );

            icon_span.removeClass(caller.data('icon'));
            icon_span.addClass('ui-icon');
            icon_span.addClass('ak-toggle-button');
            icon_span.addClass('ak-toggle-button-spinning');
            icon_span.addClass('ui-icon-arrowrefresh-1-w');
            icon_span.everyTime(100, 'spinner', function()
            {
                if(icon_span.hasClass('ui-icon-arrowrefresh-1-w'))
                {
                    icon_span.removeClass('ui-icon-arrowrefresh-1-w');
                    icon_span.addClass('ui-icon-arrowrefresh-1-n');
                } else
                if(icon_span.hasClass('ui-icon-arrowrefresh-1-n'))
                {
                    icon_span.removeClass('ui-icon-arrowrefresh-1-n');
                    icon_span.addClass('ui-icon-arrowrefresh-1-e');
                } else
                if(icon_span.hasClass('ui-icon-arrowrefresh-1-e'))
                {
                    icon_span.removeClass('ui-icon-arrowrefresh-1-e');
                    icon_span.addClass('ui-icon-arrowrefresh-1-s');
                } else
                {
                    icon_span.removeClass('ui-icon-arrowrefresh-1-s');
                    icon_span.addClass('ui-icon-arrowrefresh-1-w');
                }
            });
        }


        // Convert to JSON
        var json = JSON.stringify(data);
        // Assemble the data array and send the AJAX request
        var new_data = {
            action: json
        };

        akeeba.System.doAjax(new_data, function(response)
        {
            if(caller != null)
            {
                icon_span.stopTime();
                icon_span.attr('class', caller.data('icon'));
                caller.removeData('icon');
                caller.removeData('loading');
            }

            if( response.success == true )
            {
                if(caller != null)
                {
                    if(use_inner_child)
                    {
                        // Update the on-screen filter state
                        if(response.newstate == true)
                        {
                            caller.removeClass('ui-state-normal');
                            caller.addClass('ui-state-highlight');
                        }
                        else
                        {
                            caller.addClass('ui-state-normal');
                            caller.removeClass('ui-state-highlight');
                        }
                    }
                }

                if(!(callback == null))
                {
                    callback(response, caller);
                }
            }
            else
            {
                if(!(callback == null))
                {
                    callback(response, caller);
                }

                var dialog_element = $("#dialog");
                dialog_element.html(''); // Clear the dialog's contents
                $(document.createElement('p')).html(akeeba.Fsfilters.translations['UI-ERROR-FILTER'].replace('%s', data.node)).appendTo(dialog_element);
                dialog_element.dialog('open');
            }
        }, function(msg)
        {
            // Error handler
            if(caller != null)
            {
                icon_span.stopTime();
                icon_span.attr('class', caller.data('icon'));
                caller.removeData('icon');
                caller.removeData('loading');
            }

            akeeba.System.params.errorCallback(msg);
        }, true, 15000);
    };

    /**
     * Renders the Filesystem Filters page
     * @param data
     * @return
     */
    akeeba.Fsfilters.render = function (data)
    {
        akeeba.Fsfilters.currentRoot = data.root;

        // ----- Render the crumbs bar
        // Create a new crumbs data array
        var crumbsdata = [];
        // Push the "navigate to root" element
        var newCrumb = [
            akeeba.Fsfilters.translations['UI-ROOT'], // [0] : UI Label
            data.root,                              // [1] : Root node
            [],                                     // [2] : Crumbs to current directory
            ''                                      // [3] : Node element
        ];

        crumbsdata.push(newCrumb);

        // Iterate existing crumbs
        if (data.crumbs.length > 0)
        {
            var crumbs = [];

            $.each(data.crumbs,function(counter, crumb){
                var newCrumb = [
                    crumb,
                    data.root,
                    crumbs.slice(0), // Otherwise it is copied by reference
                    crumb
                ];
                crumbsdata.push(newCrumb);
                crumbs.push(crumb); // Push this dir into the crumb list
            });
        }

        // Render the UI crumbs elements
        var akcrumbs = $('#ak_crumbs');
        akcrumbs.html('');

        $.each(crumbsdata, function(counter, def){
            var myLi = $(document.createElement('li'));

            $(document.createElement('a'))
                .attr('href','javascript:')
                .html(def[0])
                .click(function(){
                    $(this).append(
                        $(document.createElement('img'))
                            .attr({
                                src:    akeeba.Fsfilters.loadingGif,
                                width:  16,
                                height: 11,
                                border: 0,
                                alt:    'Loading...'
                            })
                            .css({
                                marginTop: '5px',
                                marginLeft: '5px'
                            })
                    );

                    var new_data = {
                        root:   def[1],
                        crumbs: def[2],
                        node:   def[3]
                    };
                    akeeba.Fsfilters.load(new_data);
                })
                .appendTo(myLi);
            $(document.createElement('span'))
                .addClass('divider')
                .text('/')
                .appendTo(myLi);
            myLi.appendTo(akcrumbs);
        });

        // ----- Render the subdirectories
        var akfolders = $('#folders');
        akfolders.html('');

        if(data.crumbs.length > 0)
        {
            // The parent directory element
            var uielement = $(document.createElement('div'))
                .addClass('folder-container');
            uielement
                .append($(document.createElement('span')).addClass('folder-padding'))
                .append($(document.createElement('span')).addClass('folder-padding'))
                .append($(document.createElement('span')).addClass('folder-padding'))
                .append(
                $(document.createElement('span'))
                    .addClass('folder-name folder-up')
                    .html('('+akcrumbs.find('li:last').prev().find('a').html()+')')
                    .prepend(
                    $(document.createElement('span'))
                        .addClass('ui-icon ui-icon-arrowreturnthick-1-w')
                )
                    .click(function(){
                        akcrumbs.find('li:last').prev().find('a').click();
                    })
            )
                .appendTo(akfolders);
        }

        // Append the "Apply to all" buttons
        if(Object.keys(data.folders).length > 0)
        {
            var headerFilters = ['directories_all', 'skipdirs_all', 'skipfiles_all'];
            var headerDirs    = $(document.createElement('div')).addClass('folder-header folder-container');

            $.each(headerFilters, function(index, filter)
            {
                var ui_icon = $(document.createElement('span')).addClass('folder-icon-container')
                    .attr('title', '<div class="tooltip-arrow-up-leftaligned"></div><div>'+akeeba.Fsfilters.translations['UI-FILTERTYPE-'+filter.toUpperCase()]+'</div>')
                    .tooltip({
                        html: true,
                        placement: 'top'
                    });

                var applyTo = '';

                switch(filter)
                {
                    case 'directories_all':
                        applyTo = 'ui-icon-cancel';
                        ui_icon.append('<span class="ak-toggle-button ui-icon ui-icon-cancel"></span>');
                        break;
                    case 'skipdirs_all':
                        applyTo = 'ui-icon-folder-open';
                        ui_icon.append('<span class="ak-toggle-button ui-icon ui-icon-folder-open"></span>');
                        break;
                    case 'skipfiles_all':
                        applyTo = 'ui-icon-document';
                        ui_icon.append('<span class="ak-toggle-button ui-icon ui-icon-document"></span>');
                        break;
                }

                ui_icon.click(function(){
                    var selected;

                    if($(this).hasClass('ui-state-highlight')){
                        $(this).removeClass('ui-state-highlight');
                        selected = false;
                    }
                    else{
                        $(this).addClass('ui-state-highlight');
                        selected = true;
                    }

                    $.each(akfolders.find('.folder-container').not('.folder-header').find('span.'+applyTo), function(index, item){
                        var hasClass = $(item).parent().hasClass('ui-state-highlight');

                        // I have to exclude items that have the same state of the desidered one, otherwise I'll toggle it
                        if((!selected && !hasClass) || (selected && hasClass))
                        {
                            return;
                        }

                        $(item).click();
                    });
                });

                ui_icon.appendTo(headerDirs);
            });

            $(document.createElement('span')).addClass('folder-name')
                .html('<span class="pull-left ui-icon ui-icon-arrowthick-1-w"></span>' + akeeba.Fsfilters.translations['UI-FILTERTYPE-APPLYTOALLDIRS'])
                .appendTo(headerDirs);

            headerDirs.appendTo(akfolders);
        }

        $.each(data.folders, function(folder, def){
            var uielement = $(document.createElement('div'))
                .addClass('folder-container');

            var available_filters = ['directories', 'skipdirs', 'skipfiles'];
            $.each(available_filters, function(counter, filter){
                var ui_icon = $(document.createElement('span')).addClass('folder-icon-container')
                    .attr('title', '<div class="tooltip-arrow-up-leftaligned"></div><div>' + akeeba.Fsfilters.translations['UI-FILTERTYPE-'+filter.toUpperCase()] + '</div>');
                switch(filter)
                {
                    case 'directories':
                        ui_icon.append('<span class="ak-toggle-button ui-icon ui-icon-cancel"></span>');
                        break;
                    case 'skipdirs':
                        ui_icon.append('<span class="ak-toggle-button ui-icon ui-icon-folder-open"></span>');
                        break;
                    case 'skipfiles':
                        ui_icon.append('<span class="ak-toggle-button ui-icon ui-icon-document"></span>');
                        break;
                }
                ui_icon.tooltip({
                    placement: 'bottom',
                    delay: 0
                });

                switch(def[filter])
                {
                    case 2:
                        ui_icon.addClass('ui-state-error');
                        break;

                    case 1:
                        ui_icon.addClass('ui-state-highlight');
                    // Don't break; we have to add the handler!

                    case 0:
                        ui_icon.click(function(){
                            var new_data = {
                                root:       data.root,
                                crumbs:     crumbs,
                                node:       folder,
                                filter:     filter,
                                verb:       'toggle'
                            }

                            akeeba.Fsfilters.toggle(new_data, ui_icon);
                        });
                }
                ui_icon.appendTo(uielement);
            }); // filter loop
            // Add the folder label and make clicking on it load its listing
            $(document.createElement('span'))
                .html(folder)
                .addClass('folder-name')
                .click(function(){
                    // Show "loading" animation
                    $(this).append(
                        $(document.createElement('img'))
                            .attr({
                                src:    akeeba.Fsfilters.loadingGif,
                                width:  16,
                                height: 11,
                                border: 0,
                                alt:    'Loading...'
                            })
                            .css({
                                marginTop: '3px',
                                marginLeft: '5px'
                            })
                    );

                    var new_data = {
                        root:   data.root,
                        crumbs: crumbs,
                        node:   folder
                    };
                    akeeba.Fsfilters.load(new_data);
                })
                .appendTo(uielement);
            // Render
            uielement.appendTo(akfolders);
        });

        // ----- Render the files
        var akfiles = $('#files');
        akfiles.html('');

        // Append the "Apply to all" buttons
        if(Object.keys(data.files).length > 0)
        {
            var headerFiles = $(document.createElement('div')).addClass('file-header file-container');

            var ui_icon = $(document.createElement('span')).addClass('file-icon-container');
            ui_icon.append('<span class="ak-toggle-button ui-icon ui-icon-cancel"></span>');

            ui_icon.attr('title', '<div class="tooltip-arrow-up-leftaligned"></div><div>'+akeeba.Fsfilters.translations['UI-FILTERTYPE-FILES_ALL']+'</div>')
                .tooltip({
                    html: true,
                    placement: 'top'
                });

            ui_icon.click(function(){
                var selected;

                if($(this).hasClass('ui-state-highlight')){
                    $(this).removeClass('ui-state-highlight');
                    selected = false;
                }
                else{
                    $(this).addClass('ui-state-highlight');
                    selected = true;
                }

                $.each(akfiles.find('.file-container').not('.file-header').find('span.ui-icon-cancel'), function(index, item){
                    var hasClass = $(item).parent().hasClass('ui-state-highlight');

                    // I have to exclude items that have the same state of the desidered one, otherwise I'll toggle it
                    if((!selected && !hasClass) || (selected && hasClass))
                    {
                        return;
                    }

                    $(item).click();
                });
            });

            ui_icon.appendTo(headerFiles);

            $(document.createElement('span')).addClass('file-name')
                .html('<span class="pull-left ui-icon ui-icon-arrowthick-1-w"></span>' + akeeba.Fsfilters.translations['UI-FILTERTYPE-APPLYTOALLFILES'])
                .appendTo(headerFiles);

            headerFiles.appendTo(akfiles);
        }

        $.each(data.files, function(file, def){
            var uielement = $(document.createElement('div'))
                .addClass('file-container');

            var available_filters = ['files'];
            $.each(available_filters, function(counter, filter){
                var ui_icon = $(document.createElement('span')).addClass('file-icon-container');

                switch(filter)
                {
                    case 'files':
                        ui_icon.append('<span class="ak-toggle-button ui-icon ui-icon-cancel"></span>');
                        break;
                }

                ui_icon.attr('title', '<div class="tooltip-arrow-up-leftaligned"></div><div>'+akeeba.Fsfilters.translations['UI-FILTERTYPE-'+filter.toUpperCase()]+'</div>')
                    .tooltip({
                        html: true,
                        placement: 'top'
                    });

                switch(def[filter])
                {
                    case 2:
                        ui_icon.addClass('ui-state-error');
                        break;

                    case 1:
                        ui_icon.addClass('ui-state-highlight');
                    // Don't break; we have to add the handler!

                    case 0:
                        ui_icon.click(function(){
                            var new_data = {
                                root:   data.root,
                                crumbs: crumbs,
                                node:   file,
                                filter: filter,
                                verb:   'toggle'
                            };
                            akeeba.Fsfilters.toggle(new_data, ui_icon);
                        });
                }
                ui_icon.appendTo(uielement);
            }); // filter loop
            // Add the file label
            uielement
                .append(
                $(document.createElement('span'))
                    .addClass('file-name')
                    .html(file)
            )
                .append(
                $(document.createElement('span'))
                    .addClass('file-size')
                    .html(size_format(def['size']))
            );
            // Render
            uielement.appendTo(akfiles);
        });
    };


    /**
     * Wipes out the filesystem filters
     * @return
     */
    akeeba.Fsfilters.nuke = function()
    {
        var data = {
            root:   akeeba.Fsfilters.currentRoot,
            verb:   'reset'
        };
        // Assemble the data array and send the AJAX request
        var new_data = {
            action: JSON.stringify(data)
        };
        akeeba.System.doAjax(new_data, function(response){
            akeeba.Fsfilters.render(response);
        }, null, false, 15000);
    };

    /**
     * Loads the tabular view of the Filesystems Filter for a given root
     * @param root
     * @return
     */
    akeeba.Fsfilters.loadTab = function(root)
    {
        var data = {
            verb:   'tab',
            root:   root
        };
        // Assemble the data array and send the AJAX request
        var new_data = {
            action: JSON.stringify(data)
        };
        akeeba.System.doAjax(new_data, function(response){
            akeeba.Fsfilters.renderTab(response);
        }, null, false, 15000);
    };

    /**
     * Add a row in the tabular view of the Filesystems Filter
     * @param def
     * @param append_to_here
     * @return
     */
    akeeba.Fsfilters.addRow = function(def, append_to_here)
    {
        // Turn def.type into something human readable
        var type_text = akeeba.Fsfilters.translations['UI-FILTERTYPE-'+def.type.toUpperCase()];

        if (type_text == null)
        {
            type_text = def.type;
        }

        $(document.createElement('tr'))
            .addClass('ak_filter_row')
            .append(
            // Filter title
            $(document.createElement('td'))
                .addClass('ak_filter_type')
                .append(type_text)
        )
            .append(
            $(document.createElement('td'))
                .addClass('ak_filter_item')
                .append(
                $(document.createElement('span'))
                    .addClass('ak_filter_tab_icon_container')
                    .click(function(){
                        if( def.node == '' )
                        {
                            // An empty filter is normally not saved to the database; it's a new record row which has to be removed...
                            $(this).parent().parent().remove();
                            return;
                        }

                        var new_data = {
                            root:   $('#active_root').val(),
                            crumbs: [],
                            node:   def.node,
                            filter: def.type,
                            verb:   'toggle'
                        };

                        akeeba.Fsfilters.toggle(new_data, $(this), function(response, caller){
                            if(response.success)
                            {
                                caller.parent().parent().remove();
                            }
                        });
                    })
                    .append(
                    $(document.createElement('span'))
                        .addClass('ak-toggle-button ui-icon ui-icon-trash deletebutton')
                )
            )
                .append(
                $(document.createElement('span'))
                    .addClass('ak_filter_tab_icon_container')
                    .click(function(){
                        if( $(this).siblings('span.ak_filter_tab_icon_container:first').next().data('editing') ) return;
                        $(this).siblings('span.ak_filter_tab_icon_container:first').next().data('editing',true);
                        $(this).next().hide();
                        $(document.createElement('input'))
                            .attr({
                                type: 'text',
                                size: 60
                            })
                            .val( $(this).next().html() )
                            .appendTo( $(this).parent() )
                            .blur(function(){
                                var new_value = $(this).val();
                                if(new_value == '')
                                {
                                    // Well, if the user meant to remove the filter, let's help him!
                                    $(this).parent().children('span.ak_filter_name').show();
                                    $(this).siblings('span.ak_filter_tab_icon_container').find('span.deletebutton').click();
                                    $(this).remove();
                                    return;
                                }

                                // First, remove the old filter
                                var new_data = {
                                    root:       $('#active_root').val(),
                                    crumbs:     [],
                                    old_node:   def.node,
                                    new_node:   new_value,
                                    filter:     def.type,
                                    verb:       'swap'
                                };

                                var input_box = $(this);

                                akeeba.Fsfilters.toggle(new_data,
                                    input_box.siblings('span.ak_filter_tab_icon_container:first').next(),
                                    function(response, caller){
                                        // Remove the editor
                                        input_box.siblings('span.ak_filter_tab_icon_container:first').next().removeData('editing');
                                        input_box.parent().find('span.ak_filter_name').show();
                                        input_box.siblings('span.ak_filter_tab_icon_container:first').next().removeClass('ui-state-highlight');
                                        input_box.parent().find('span.ak_filter_name').html( new_value );
                                        input_box.remove();
                                        def.node = new_value;
                                    }
                                );
                            })
                            .focus();
                    })
                    .append(
                    $(document.createElement('span'))
                        .addClass('ak-toggle-button ui-icon ui-icon-pencil editbutton')
                )
            )
                .append(
                $(document.createElement('span'))
                    .addClass('ak_filter_name')
                    .html(def.node)
            )
        )
            .appendTo( $(append_to_here) );
    };

    akeeba.Fsfilters.addNew = function(filtertype)
    {
        // Add a row below ourselves
        var new_def = {
            type:   filtertype,
            node:   ''
        };
        akeeba.Fsfilters.addRow(new_def, $('#ak_list_table'));
        $('#ak_list_table tr:last').children('td:last').children('span.ak_filter_tab_icon_container:last').click();
    };

    /**
     * Renders the tabular view of the Filesystems Filter
     * @param data
     * @return
     */
    akeeba.Fsfilters.renderTab = function(data)
    {
        var tbody = $('#ak_list_contents');
        tbody.html('');
        $.each(data, function(counter, def){
            akeeba.Fsfilters.addRow(def, tbody);
        });
    }

}(akeeba.jQuery));