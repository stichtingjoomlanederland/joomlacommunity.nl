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

if (typeof akeeba.Multidb == 'undefined')
{
    akeeba.Multidb = {
        translations: {},
        loadingGif: ''
    }
}

(function($){
/**
 * Render the additional databases interface
 *
 * @param data
 */
akeeba.Multidb.render = function(data)
{
    var tbody = $('#ak_list_contents');
    tbody.html('');
    $.each(data, function(rootname, def){
        akeeba.Multidb.addRow(rootname, def, tbody);
    });
    akeeba.Multidb.addNewRecordButton( tbody );
};

/**
 * Add a single row to the additional databases interface
 *
 * @param root
 * @param def
 * @param append_to_here
 */
akeeba.Multidb.addRow = function (root, def, append_to_here)
{
    $(document.createElement('tr'))
        .addClass('ak_filter_row')
        .data('root', root) // Cache the root name of this definition
        .data('def', def) // Cache the definition data
        // Delete button
        .append(
            $(document.createElement('td'))
                .append(
                    $(document.createElement('span'))
                        .addClass('ak_filter_tab_icon_container')
                        .click(function(){
                            // If an editor is already showing we shouldn't try to delete anything
                            var editor = $('#ak-editor');
                            if(editor.dialog('isOpen')) return;

                            var new_data = {
                                root:   $(this).parent().parent().data('root'),
                                verb:   'remove'
                            };

                            akeeba.Fsfilters.toggle(new_data, $(this), function(response, caller){
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
                .css('width', '2em')
                .append(
                    $(document.createElement('span'))
                        .addClass('ak_filter_tab_icon_container')
                        .click(function(){
                            var cache_element = $(this).parent().parent();
                            var cache_data = $(this).parent().parent().data('def');
                            var cache_root = $(this).parent().parent().data('root');
                            var editor = $('#ak-editor');
                            // If an editor is already showing we can't re-show it :)
                            if(editor.dialog('isOpen')) return;
                            // Select the correct driver
                            if(cache_data.driver == '') cache_data.driver = 'mysqli';
                            // Set the parameters
                            $('#ake_driver').val(cache_data.driver);
                            $('#ake_host').val(cache_data.host);
                            $('#ake_username').val(cache_data.username);
                            $('#ake_password').val(cache_data.password);
                            $('#ake_database').val(cache_data.database);
                            $('#ake_prefix').val(cache_data.prefix);
                            // Remove any leftover notifier
                            try {
                                $('#ak_editor_notifier').remove();
                            } catch (e) {}
                            // Set editor's buttons
                            var strTest = akeeba.Multidb.translations['UI-MULTIDB-TEST'];
                            var strSave = akeeba.Multidb.translations['UI-MULTIDB-SAVE'];
                            var strCancel = akeeba.Multidb.translations['UI-MULTIDB-CANCEL'];
                            var buttons = {};
                            buttons[strTest] = function() {
                                // Create the placeholder div and show a loading message
                                $('#ak_editor_notifier').remove();
                                $('#ak_editor_table')
                                    .before(
                                    $(document.createElement('div'))
                                        .addClass('ak_notifier')
                                        .attr('id', 'ak_editor_notifier')
                                        .append(
                                        // Close button
                                        $(document.createElement('span'))
                                            .addClass('ui-icon')
                                            .addClass('ui-icon-closethick')
                                            .addClass('ak-toggle-button')
                                            .click(function(){
                                                $(this).parent().remove();
                                            })
                                    )
                                        .append(
                                        $(document.createElement('span'))
                                            .attr('id','ak_editor_notifier_content')
                                            .append(
                                            // Loading animation
                                            $(document.createElement('img'))
                                                .attr({
                                                    border: 0,
                                                    src: akeeba.Multidb.loadingGif
                                                })
                                        )
                                            .append(
                                            // Loading text
                                            $(document.createElement('span'))
                                                .html(akeeba.Multidb.translations['UI-MULTIDB-LOADING'])
                                        )
                                    )
                                );
                                // Test the connection via AJAX
                                var req = {
                                    verb : 'test',
                                    root : root,
                                    data : {
                                        driver : $('#ake_driver').val(),
                                        host : $('#ake_host').val(),
                                        port : $('#ake_port').val(),
                                        user : $('#ake_username').val(),
                                        password : $('#ake_password').val(),
                                        database : $('#ake_database').val(),
                                        prefix : $('#ake_prefix').val()
                                    }
                                };

                                var json = JSON.stringify(req);
                                var query = {};
                                query.action = json;
                                akeeba.System.doAjax(query, function(response){
                                    if( response.status == true )
                                    {
                                        $('#ak_editor_notifier_content').html(akeeba.Multidb.translations['UI-MULTIDB-CONNECTOK']);
                                    }
                                    else
                                    {
                                        $('#ak_editor_notifier_content').html(
                                            akeeba.Multidb.translations['UI-MULTIDB-CONNECTFAIL'] +
                                            '<br/>' +
                                            '<tt>' + response.message + '</tt>'
                                        );
                                    }
                                }, function(message) {
                                    $('#ak_editor_notifier_content').html(
                                        akeeba.Multidb.translations['UI-MULTIDB-CONNECTFAIL']
                                    );
                                    akeeba.System.params.errorCallback(message);
                                });
                            };
                            buttons[strSave] = function() {
                                // Create the placeholder div and show a loading message
                                $('#ak_editor_notifier').remove();
                                $('#ak_editor_table')
                                    .before(
                                    $(document.createElement('div'))
                                        .addClass('ak_notifier')
                                        .attr('id', 'ak_editor_notifier')
                                        .append(
                                        // Close button
                                        $(document.createElement('span'))
                                            .addClass('ui-icon')
                                            .addClass('ui-icon-closethick')
                                            .addClass('ak-toggle-button')
                                            .click(function(){
                                                $(this).parent().remove();
                                            })
                                    )
                                        .append(
                                        $(document.createElement('span'))
                                            .attr('id','ak_editor_notifier_content')
                                            .append(
                                            // Loading animation
                                            $(document.createElement('img'))
                                                .attr({
                                                    border: 0,
                                                    src: akeeba.Multidb.loadingGif
                                                })
                                        )
                                            .append(
                                            // Loading text
                                            $(document.createElement('span'))
                                                .html(akeeba.Multidb.translations['UI-MULTIDB-LOADING'])
                                        )
                                    )
                                );
                                // Send AJAX save request
                                var req = {
                                    verb : 'set',
                                    root : root,
                                    data : {
                                        driver : $('#ake_driver').val(),
                                        host : $('#ake_host').val(),
                                        port : $('#ake_port').val(),
                                        username : $('#ake_username').val(),
                                        password : $('#ake_password').val(),
                                        database : $('#ake_database').val(),
                                        prefix : $('#ake_prefix').val(),
                                        dumpFile : String(root).substr(0,9) + $('#ake_database').val() + '.sql'
                                    }
                                };

                                var json = JSON.stringify(req);
                                var query = {
                                    action : json
                                };
                                akeeba.System.doAjax(query, function(response){
                                    if( response == true )
                                    {
                                        // Cache new data
                                        cache_element.data('def', req.data);
                                        // Update grid cells (host & db)
                                        var cells = cache_element.children('td');
                                        cells.find('span.ak_dbhost').html(req.data.host);
                                        cells.find('span.ak_dbname').html(req.data.database);
                                        // Handle new row case
                                        if(!cells.find('span.editbutton').hasClass('ui-icon-pencil'))
                                        {
                                            // This was a new row. Add the normal buttons...
                                            cells.find('span.deletebutton').parent().show();
                                            cells.find('span.editbutton')
                                                .removeClass('ui-icon-circle-plus')
                                                .addClass('ui-icon-pencil')
                                                .addClass('ak-toggle-button');
                                            // ...then add a new "add new row" at the bottom.
                                            akeeba.Multidb.addNewRecordButton(cache_element.parent());
                                        }
                                        // Finally close the dialog
                                        $('#ak-editor').dialog("close");
                                    }
                                    else
                                    {
                                        $('#ak_editor_notifier_content')
                                            .html( akeeba.Multidb.translations['UI-MULTIDB-SAVEFAIL'] );
                                    }
                                }, function(message) {
                                    $('#ak_editor_notifier_content')
                                        .html( akeeba.Multidb.translations['UI-MULTIDB-SAVEFAIL'] );
                                    akeeba.System.params.errorCallback(message);
                                });
                            };
                            buttons[strCancel] = function() {
                                // Cancel edits; just close the dialog
                                $(this).dialog("close");
                            };
                            editor.dialog('option', 'buttons', buttons);
                            // Show editor
                            editor.dialog('open');
                            editor.find('span').focus();
                        })
                        .append(
                            $(document.createElement('span'))
                                .addClass('ui-icon ui-icon-pencil editbutton')
                                .addClass('ak-toggle-button')
                        )
                )
        )
        .append(
            // Database host
            $(document.createElement('td'))
                .addClass('ak_filter_item')
                .append(
                    $(document.createElement('span'))
                        .addClass('ak_filter_name')
                        .addClass('ak_dbhost')
                        .html(def.host)
                )
        )
        .append(
            // Database name
            $(document.createElement('td'))
                .addClass('ak_filter_item')
                .append(
                    $(document.createElement('span'))
                        .addClass('ak_filter_name')
                        .addClass('ak_dbname')
                        .html(def.database)
                )
        )
        .appendTo( $(append_to_here) );
};

akeeba.Multidb.addNewRecordButton = function( append_to_here )
{
    var root = Math.uuid();
    var dummyData = {
        host:       '',
        port:       '',
        username:   '',
        password:   '',
        database:   '',
        prefix:     ''
    };
    akeeba.Multidb.addRow(root, dummyData, append_to_here);

    $('#ak_list_contents tr:last-child td:first-child span:first').hide();
    $('#ak_list_contents tr:last-child td:nth-child(2) span:last')
        .removeClass('ui-icon-pencil')
        .addClass('ui-icon-circle-plus')
        .addClass('ak-toggle-button');
}

}(akeeba.jQuery));