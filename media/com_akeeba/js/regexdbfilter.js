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

if (typeof akeeba.Regexdbfilters == 'undefined')
{
    akeeba.Regexdbfilters = {
        translations: {},
        currentRoot: null
    }
}

(function($){
/**
 * Change the active root
 */
akeeba.Regexdbfilters.activeRootChanged = function()
{
    akeeba.Regexdbfilters.load($('#active_root').val());
};

/**
 * Load data from the server
 *
 * @param   new_root  The root to load data for
 */
akeeba.Regexdbfilters.load = function load(new_root)
{
    var data = {
        root:   new_root,
        verb:   'list'
    };

    var request = {
        akaction: JSON.stringify(data)
    };
    akeeba.System.doAjax(request, function(response)
    {
        akeeba.Regexdbfilters.render(response);
    }, null, false, 15000);
};

/**
 * Render the data in the GUI
 *
 * @param   data
 */
akeeba.Regexdbfilters.render = function(data)
{
    var tbody = $('#ak_list_contents');
    tbody.html('');

    $.each(data, function(counter, def){
        akeeba.Regexdbfilters.addRow(def, tbody);
    });

    var newdef = {
        type: '',
        item: ''
    };

    akeeba.Regexdbfilters.addNewRow( tbody );
};

/**
 * Adds a row to the GUI
 *
 * @param   def             Filter definition
 * @param   append_to_here  Element to append the row to
 */
akeeba.Regexdbfilters.addRow = function(def, append_to_here )
{
    var trow =
        $(document.createElement('tr'))
            .appendTo( append_to_here );

    // Is this an existing filter or a new one?
    var edit_icon_class = 'ui-icon-pencil';
    if( def.item == '' ) edit_icon_class = 'ui-icon-circle-plus';

    var td_buttons =
        $(document.createElement('td'))
            .append(
                // Edit/new/save button
                $(document.createElement('span'))
                    .addClass('ui-icon table-icon-container edit ak-toggle-button')
                    .addClass(edit_icon_class)
                    .click(function(){
                        // Create the drop down
                        var known_filters = ['regextables','regextabledata'];
                        var mySelect = $(document.createElement('select')).attr('name','type').addClass('type-select');
                        $.each(known_filters, function(i, filter_name){
                            var type_translation_key = 'UI-FILTERTYPE-' + String(filter_name).toUpperCase();
                            var type_localized = akeeba.Regexdbfilters.translations[type_translation_key];
                            var selected = false;
                            if( filter_name == def.type ) selected = true;
                            mySelect.append(
                                $(document.createElement('option'))
                                    .attr('value', filter_name)
                                    .html( type_localized )
                            );
                            if(selected)
                            {
                                mySelect.children(':last').attr('selected','selected');
                            }
                        });
                        // Switch the type span with the drop-down
                        trow.find('td.ak-type span').hide();
                        trow.find('td.ak-type').append(mySelect);
                        // Create the edit box
                        var myEditBox = $(document.createElement('input'))
                            .attr({
                                'type': 'text',
                                'name': 'item',
                                'size': '100'
                            }).val(def.item);
                        // Switch the item tt with the input box
                        trow.find('td.ak-item tt').hide();
                        trow.find('td.ak-item').append(myEditBox);
                        // Hide the edit/delete buttons, add save/cancel buttons
                        trow.find('td:first span.edit').hide();
                        trow.find('td:first span.delete').hide();
                        trow.find('td:first')
                            .append(
                                $(document.createElement('span'))
                                    .addClass('ui-icon ui-icon-disk table-icon-container save ak-toggle-button')
                                    .click(function(){
                                        trow.find('td:first span.delete').hide();
                                        var new_type = mySelect.val();
                                        var new_item = myEditBox.val();
                                        if( trim(new_item) == '' )
                                        {
                                            // Empty item detected. It is equivalent to delete or cancel.
                                            if(def.item == '')
                                            {
                                                trow.find('td:first span.cancel').click();
                                                return;
                                            }
                                            else
                                            {
                                                trow.find('td:first span.delete').click();
                                                return;
                                            }
                                        }
                                        var new_data = {
                                            verb:   'set',
                                            type:   new_type,
                                            node:   new_item,
                                            root:   $('#active_root').val()
                                        };
                                        akeeba.Fsfilters.toggle(new_data, $(this), function(response, caller){
                                            // Now that we saved the new filter, delete the old one
                                            var haveToDelete = (def.item != '') && (def.type != '') && ( (def.item != new_item) || (def.type != new_type) );
                                            var addedNewItem = (def.item == '') || (def.type == '');
                                            if(def.item == '') trow.find('td:first span.edit')
                                                .removeClass('ui-icon-circle-plus')
                                                .addClass('ui-icon-pencil');
                                            new_data.type = def.type;
                                            new_data.node = def.item;
                                            def.type = new_type;
                                            def.item = new_item;
                                            var type_translation_key = 'UI-FILTERTYPE-' + String(def.type).toUpperCase();
                                            var type_localized = akeeba.Regexdbfilters.translations[type_translation_key];
                                            trow.find('td.ak-type span').html(type_localized);
                                            trow.find('td.ak-item tt').html(escapeHTML(def.item));
                                            trow.find('td:first span.cancel').click();

                                            if( haveToDelete )
                                            {
                                                new_data.verb = 'remove';
                                                akeeba.Fsfilters.toggle(new_data, $(this), function(response, caller){
                                                }, false);
                                            }
                                            else if ((def.item != new_item) || (def.type != new_type) || addedNewItem)
                                            {
                                                akeeba.Regexdbfilters.addNewRow(append_to_here);
                                            }
                                        }, false);
                                    })
                            );
                        trow.find('td:first')
                            .append(
                                $(document.createElement('span'))
                                    .addClass('ui-icon ui-icon-close table-icon-container cancel ak-toggle-button')
                                    .click(function(){
                                        // Cancel changes; remove editing GUI elements, show the original elements
                                        trow.find('td:first span.save').remove();
                                        trow.find('td:first span.cancel').remove();
                                        trow.find('td:first span.edit').show();
                                        if(def.item != '') trow.find('td:first span.delete').show();
                                        mySelect.remove();
                                        trow.find('td.ak-type span').show();
                                        myEditBox.remove();
                                        trow.find('td.ak-item tt').show();
                                    })
                            );
                    })
            )
            .append(
                $(document.createElement('span'))
                    .addClass('ui-icon ui-icon-trash table-icon-container delete ak-toggle-button')
                    .click(function(){
                        var new_data = {
                            verb:   'remove',
                            type:   def.type,
                            node:   def.item,
                            root:   $('#active_root').val()
                        };
                        akeeba.Fsfilters.toggle(new_data, $(this), function(response, caller){
                            trow.remove();
                            if(def.item == '')
                            {
                                akeeba.Regexdbfilters.addNewRow( append_to_here );
                            }
                        }, false);
                    })
            )
            .appendTo(trow); // Append to the table row

    // Hide the delete button on new rows
    if( def.item == '' )
    {
        $(td_buttons).find('span.delete').hide();
    }

    // Filter type and filter item rows
    var type_translation_key = 'UI-FILTERTYPE-' + String(def.type).toUpperCase();
    var type_localized = akeeba.Regexdbfilters.translations[type_translation_key];
    if(def.type == '') type_localized = '';
    trow.append(
        $(document.createElement('td'))
            .addClass('ak-type')
            .html( '<span>'+type_localized+ '</span>' )
    );
    trow.append(
        $(document.createElement('td'))
            .addClass('ak-item')
            .html('<tt>' + ( (def.item == null) ? '' : escapeHTML(def.item) ) + '</tt>')
    );
};

/**
 * Add a new row to the GUI
 *
 * @param   append_to_here  Element where to append the row
 */
akeeba.Regexdbfilters.addNewRow = function(append_to_here )
{
    var newdef = {
        type:   '',
        item:   ''
    };
    akeeba.Regexdbfilters.addRow(newdef, append_to_here);
}

}(akeeba.jQuery));