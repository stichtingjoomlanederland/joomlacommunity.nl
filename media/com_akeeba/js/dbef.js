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

if (typeof akeeba.Dbfilters == 'undefined')
{
    akeeba.Dbfilters = {
        translations: {},
        currentRoot: null,
        loadingGif: ''
    }
}

(function($){
akeeba.Dbfilters.activeRootChanged = function()
{
    var data = {
        root:   $('#active_root').val()
    };
    akeeba.Dbfilters.load(data);
};

/**
 * Loads the contents of a database
 *
 * @param data
 */
akeeba.Dbfilters.load = function(data) {
    // Add the verb to the data
    data.verb = 'list';

    // Assemble the data array and send the AJAX request
    var new_data = {
        action: JSON.stringify(data)
    };

    akeeba.System.doAjax(new_data, function (response) {
        akeeba.Dbfilters.render(response);
    }, null, false, 15000);
};

/**
 * Toggles a database filter
 * @param data
 * @param caller
 */
akeeba.Dbfilters.toggle = function (data, caller, callback)
{
    akeeba.Fsfilters.toggle(data, caller, callback);
};

/**
 * Renders the Database Filters page
 * @param data
 * @return
 */
akeeba.Dbfilters.render = function(data)
{
    akeeba.Dbfilters.currentRoot = data.root;

    // ----- Render the tables
    var aktables = $('#tables');
    aktables.html('');

    $.each(data.tables, function(table, dbef){
        var uielement = $(document.createElement('div'))
            .addClass('table-container');

        var available_filters = ['tables', 'tabledata'];
        $.each(available_filters, function(counter, filter){
            var ui_icon = $(document.createElement('span')).addClass('table-icon-container')
                .attr('title', '<div class="tooltip-arrow-up-leftaligned"></div><div>' + akeeba.Dbfilters.translations['UI-FILTERTYPE-'+filter.toUpperCase()] + '</div>');
            switch(filter)
            {
                case 'tables':
                    ui_icon.append('<span class="ak-toggle-button ui-icon ui-icon-cancel"></span>');
                    break;
                case 'tabledata':
                    ui_icon.append('<span class="ak-toggle-button ui-icon ui-icon-contact"></span>');
                    break;
            }
            ui_icon.tooltip({
                placement: 'bottom',
                delay: 0
            });

            switch(dbef[filter])
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
                            node:   table,
                            filter: filter,
                            verb:   'toggle'
                        };
                        akeeba.Dbfilters.toggle(new_data, ui_icon);
                    });
            }
            ui_icon.appendTo(uielement);
        }); // filter loop
        // Add the table label
        var iconclass = 'ui-icon-link';
        var icontip = 'UI-TABLETYPE-MISC';
        switch(dbef.type)
        {
            case 'table':
                iconclass = 'ui-icon-calculator';
                icontip = 'UI-TABLETYPE-TABLE';
                break;
            case 'view':
                iconclass = 'ui-icon-copy';
                icontip = 'UI-TABLETYPE-VIEW';
                break;
            case 'procedure':
                iconclass = 'ui-icon-script';
                icontip = 'UI-TABLETYPE-PROCEDURE';
                break;
            case 'function':
                iconclass = 'ui-icon-gear';
                icontip = 'UI-TABLETYPE-FUNCTION';
                break;
            case 'trigger':
                iconclass = 'ui-icon-video';
                icontip = 'UI-TABLETYPE-TRIGGER';
                break;
        }

        $(document.createElement('span'))
            .addClass('table-name')
            .html(table)
            .prepend(
                $(document.createElement('span'))
                    .addClass('table-icon-container table-icon-noclick table-icon-small')
                    .attr('title', akeeba.Dbfilters.translations[icontip])
                    .append(
                        $(document.createElement('span'))
                            .addClass('ui-icon')
                            .addClass(iconclass)
                    )
                    .tooltip({
                        placement: 'bottom',
                        delay: 0
                    })
            )
            .prepend(
                $(document.createElement('span'))
                    .addClass('table-icon-container table-icon-noclick table-icon-small')
                    .append(
                        $(document.createElement('span'))
                            .addClass('ui-icon')
                            .addClass('ui-icon-grip-dotted-vertical')
                    )
            )
            .appendTo(uielement);
        // Render
        uielement.appendTo(aktables);
    });
};

/**
 * Loads the tabular view of the Database Filter for a given root
 * @param root
 * @return
 */
akeeba.Dbfilters.loadTab = function(root)
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
        akeeba.Dbfilters.renderTab(response);
    }, null, false, 15000);
};

/**
 * Add a row in the tabular view of the Filesystems Filter
 * @param def
 * @param append_to_here
 * @return
 */
akeeba.Dbfilters.addRow = function(def, append_to_here)
{
    // Turn def.type into something human readable
    var type_text = akeeba.Dbfilters.translations['UI-FILTERTYPE-' + def.type.toUpperCase()];

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
                                node:   def.node,
                                filter: def.type,
                                verb:   'remove'
                            };
                            akeeba.Dbfilters.toggle(new_data, $(this), function(response, caller){
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
                                        old_node:   def.node,
                                        new_node:   new_value,
                                        filter:     def.type,
                                        verb:       'swap'
                                    };

                                    var input_box = $(this);

                                    akeeba.Dbfilters.toggle(new_data,
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

akeeba.Dbfilters.addNew = function(filtertype)
{
    // Add a row below ourselves
    var new_def = {
        type:   filtertype,
        node:   ''
    };
    akeeba.Dbfilters.addRow(new_def, $('#ak_list_table') );
    $('#ak_list_table tr:last').children('td:last').children('span.ak_filter_tab_icon_container:last').click();
};

/**
 * Renders the tabular view of the Database Filter
 * @param data
 * @return
 */
akeeba.Dbfilters.renderTab = function(data)
{
    var tbody = $('#ak_list_contents');
    tbody.html('');
    $.each(data, function(counter, def){
        akeeba.Dbfilters.addRow(def, tbody);
    });
};

/**
 * Activates the exclusion filters for non-CMS tables
 */
akeeba.Dbfilters.excludeNonCMS = function()
{
    $('#tables div').each(function(i, element){
        // Get the table name
        var tablename = $(element).find('span.table-name:first').text();
        var prefix = tablename.substr(0,3);
        // If the prefix is not #__ it's a core table and I have to exclude it
        if( prefix != '#__' )
        {
            var icon = $(element).find('span.table-icon-container span.ui-icon:first');
            if ( !($(icon).parent().hasClass('ui-state-highlight')) )
            {
                $(icon).click();
            }
        }
    });
};

/**
 * Wipes out the database filters
 * @return
 */
akeeba.Dbfilters.nuke = function()
{
    var data = {
        root:       akeeba.Dbfilters.currentRoot,
        verb:       'reset'
    };
    var new_data = {
        action: JSON.stringify(data)
    };
    akeeba.System.doAjax(new_data, function(response){
        akeeba.Dbfilters.render(response);
    }, null, false, 15000);
}
}(akeeba.jQuery));