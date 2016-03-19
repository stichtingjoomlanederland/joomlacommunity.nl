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

if (typeof akeeba.Extradirs == 'undefined')
{
    akeeba.Extradirs = {
        translations: {}
    }
}

(function($){
akeeba.Extradirs.render = function(data)
{
    var tbody = $('#ak_list_contents');
    tbody.html('');
    $.each(data, function(rootname, def){
        akeeba.Extradirs.addRow(rootname, def, tbody);
    });
    akeeba.Extradirs.addNewRecordButton( tbody );
};

akeeba.Extradirs.addRow = function(rootuuid, def, append_to_here)
{
    $(document.createElement('tr'))
        .addClass('ak_filter_row')
        .data('rootuuid', rootuuid) // Cache UUID of this entry
        .data('def', def) // Cache the definition data (virtual directory)
        // Delete button
        .append(
            $(document.createElement('td'))
                .append(
                    $(document.createElement('span'))
                        .addClass('ak_filter_tab_icon_container delete')
                        .click(function(){
                            var new_data = {
                                uuid:   $(this).parent().parent().data('rootuuid'),
                                verb:   'remove'
                            };

                            akeeba.Fsfilters.toggle(new_data, $(this), function(response, caller)
                            {
                                if(response.success == true)
                                {
                                    caller.parent().parent().remove();
                                }
                            });
                        })
                        .append(
                            $(document.createElement('span'))
                                .addClass('ui-icon ui-icon-trash deletebutton ak-toggle-button')
                        )
                )
        )
        // Edit button
        .append(
            $(document.createElement('td'))
                .append(
                    $(document.createElement('span'))
                        .addClass('ak_filter_tab_icon_container')
                        .click(function(){
                            // Get reference to data root
                            var data_root = $(this).parent().parent();
                            // Hide pencil icon
                            $(this).hide();
                            // Hide delete icon
                            data_root.find('td:first').find('span.delete').hide();
                            // Add a disk icon (save)
                            $(this).parent().append(
                                $(document.createElement('span'))
                                    .addClass('ak_filter_tab_icon_container save')
                                    .addClass('ui-icon')
                                    .addClass('ui-icon-disk')
                                    .addClass('ak-toggle-button')
                                    .addClass('ak-stacked-button')
                                    .click(function(){
                                        var new_directory = data_root.find('.ak_filter_item:first').find('input.folder_editor').val();
                                        new_directory = $.trim(new_directory);
                                        var add_dir = data_root.find('.ak_filter_item:first').next().find('input.virtual_editor').val();
                                        add_dir = $.trim(add_dir);

                                        if(empty(add_dir))
                                        {
                                            add_dir = Math.uuid(8) + '-' + basename(new_directory);
                                        }

                                        if(new_directory == '')
                                        {
                                            var old_data = data_root.data('def');
                                            if( old_data[0] == '' )
                                            {
                                                // Tried to save empty data on new row. That's like Cancel...
                                                $(this).parent().find('span.cancel').click();
                                            }
                                            else
                                            {
                                                // Tried to save empty data on existing row. That's like Delete...
                                                data_root.find('td:first').find('span.delete').show();
                                                data_root.find('td:first').find('span.delete').click();
                                            }
                                        } else {
                                            // Save entry
                                            var old_data = data_root.data('def');
                                            var new_data = {
                                                uuid:   data_root.data('rootuuid'),
                                                root:   new_directory,
                                                data:   add_dir,
                                                verb:   'set'
                                            };

                                            akeeba.Fsfilters.toggle(new_data, $(this), function(response, caller){
                                                if(response.success == true)
                                                {
                                                    // Catch case of new row
                                                    if( old_data[0] == '' )
                                                    {
                                                        // Change icon to pencil
                                                        $(caller).parent().find('span.editbutton')
                                                            .removeClass('ui-icon-circle-plus')
                                                            .addClass('ui-icon-pencil')
                                                            .addClass('ak-toggle-button');
                                                        // Add new row
                                                        akeeba.Extradirs.addNewRecordButton(append_to_here);
                                                    }
                                                    // Update cached data
                                                    var new_cache_data = [new_directory, add_dir];
                                                    data_root.data('def', new_cache_data);
                                                    // Update values in table
                                                    data_root.find('.ak_filter_item:first').find('span.ak_directory').html(new_directory);
                                                    data_root.find('.ak_filter_item:first').next().find('span.ak_virtual').html(add_dir);
                                                    // Show pencil icon
                                                    $(caller).parent().find('span.ak_filter_tab_icon_container').show();
                                                    // Remove cancel icon
                                                    $(caller).parent().find('span.cancel').remove();
                                                    // Show the delete button
                                                    data_root.find('td:first').find('span.delete').show();
                                                    // Remove disk icon
                                                    $(caller).remove();
                                                    // Remove input boxes
                                                    data_root.find('.ak_filter_item:first').find('input.folder_editor').remove();
                                                    data_root.find('.ak_filter_item:first').next().find('input.virtual_editor').remove();
                                                    // Remove browser button
                                                    data_root.find('.ak_filter_item:first').find('span.browse').remove();
                                                    // Show values
                                                    data_root.find('.ak_filter_item:first').find('span.ak_directory').show();
                                                    data_root.find('.ak_filter_item:first').next().find('span.ak_virtual').show();
                                                }
                                            }, false);
                                        }
                                    })
                            );

                            // Add a Cancel icon
                            $(this).parent().append(
                                $(document.createElement('span'))
                                    .addClass('ak_filter_tab_icon_container cancel')
                                    .addClass('ui-icon')
                                    .addClass('ui-icon-circle-close')
                                    .addClass('ak-toggle-button')
                                    .click(function(){
                                        // Show pencil icon
                                        $(this).parent().find('span.ak_filter_tab_icon_container').show();
                                        // Remove disk icon
                                        $(this).parent().find('span.save').remove();
                                        // Remove cancel icon
                                        $(this).remove();
                                        // Remove input boxes
                                        data_root.find('.ak_filter_item:first').find('input.folder_editor').remove();
                                        data_root.find('.ak_filter_item:first').next().find('input.virtual_editor').remove();
                                        // Remove browser button
                                        data_root.find('.ak_filter_item:first').find('span.browse').remove();
                                        // Show values
                                        data_root.find('.ak_filter_item:first').find('span.ak_directory').show();
                                        data_root.find('.ak_filter_item:first').next().find('span.ak_virtual').show();
                                        // Show the delete button (if it's NOT a new row)
                                        var old_data = data_root.data('def');
                                        if( old_data[0] != '' ) data_root.find('td:first').find('span.delete').show();

                                    })
                            );
                            // Show edit box
                            var old_data = data_root.data('def');
                            $(this).parent().parent().find('.ak_filter_item:first')
                                .append(
                                    $(document.createElement('input'))
                                        .attr({
                                            type: 'text',
                                            size: 60
                                        })
                                        .addClass('folder_editor')
                                        .val( old_data[0] )
                                )
                                .append(
                                    // Show browser button
                                    $(document.createElement('span'))
                                        .addClass('ak_filter_tab_icon_container')
                                        .addClass('browse')
                                        .addClass('ui-icon')
                                        .addClass('ui-icon-folder-open')
                                        .addClass('ak-toggle-button')
                                        .click(function(){
                                            // Show folder open dialog
                                            var editor = $(this).parent().find('input.folder_editor');
                                            var val = $.trim( editor.val() );
                                            if( val == '' ) val = '[ROOTPARENT]';
                                            akeeba.Configuration.onBrowser(val, editor);
                                        })
                                );
                            $(this).parent().parent().find('.ak_filter_item:first').next()
                                .append(
                                    $(document.createElement('input'))
                                        .attr({
                                            type: 'text',
                                            size: 60
                                        })
                                        .addClass('virtual_editor')
                                        .val( old_data[1] )
                                );
                            // Hide existing value boxes
                            data_root.find('.ak_filter_item:first').find('span.ak_directory').hide();
                            data_root.find('.ak_filter_item:first').next().find('span.ak_virtual').hide();
                        })
                        .append(
                            $(document.createElement('span'))
                                .addClass('ui-icon ui-icon-pencil editbutton ak-toggle-button')
                        )
                )
        )
        .append(
            // Directory path
            $(document.createElement('td'))
                .addClass('ak_filter_item')
                .append(
                    $(document.createElement('span'))
                        .addClass('ak_filter_name')
                        .addClass('ak_directory')
                        .html(def[0])
                )
        )
        .append(
            // Virtual path
            $(document.createElement('td'))
                .addClass('ak_filter_item')
                .append(
                    $(document.createElement('span'))
                        .addClass('ak_filter_name')
                        .addClass('ak_virtual')
                        .html(def[1])
                )
        )
        .appendTo( $(append_to_here) );
};

akeeba.Extradirs.addNewRecordButton = function( append_to_here )
{
    var newUUID = Math.uuid();
    var dummyData = new Array;
    dummyData[0] = '';
    dummyData[1] = '';
    akeeba.Extradirs.addRow(newUUID, dummyData, append_to_here);

    $('#ak_list_contents tr:last-child td:first-child span:first').hide();
    $('#ak_list_contents tr:last-child td:nth-child(2) span:last')
        .removeClass('ui-icon-pencil')
        .addClass('ui-icon-circle-plus')
        .addClass('ak-toggle-button');
}
}(akeeba.jQuery));