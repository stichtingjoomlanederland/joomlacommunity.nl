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

if (typeof akeeba.Configuration == 'undefined')
{
    akeeba.Configuration = {};
    akeeba.Configuration.translations = {};
    akeeba.Configuration.engines = {};
    akeeba.Configuration.installers = {};
    akeeba.Configuration.URLs = {};
    akeeba.Configuration.FtpBrowser = {
        params: {}
    };
    akeeba.Configuration.SftpBrowser = {
        params: {}
    };
    akeeba.Configuration.FtpTest = {};
    akeeba.Configuration.SftpTest = {};
	akeeba.Configuration.passwordFields = {};
}

(function($){
/**
 * Parses the JSON decoded data object defining engine and GUI parameters for the
 * configuration page
 *
 * @param  data  The nested objects of engine and GUI definitions
 */
akeeba.Configuration.parseConfigData = function(data)
{
    akeeba.Configuration.engines = data.engines;
    akeeba.Configuration.installers = data.installers;
    akeeba.Configuration.parseGuiData(data.gui);
};

/**
 * Parses the main configuration GUI definition, generating the on-page widgets
 *
 * @param  data      The nested objects of the GUI definition ('gui' key of JSON data)
 * @param  rootnode  The jQuery extended root DOM element in which to create the widgets
 */
akeeba.Configuration.parseGuiData = function(data, rootnode)
{
    if(rootnode == null)
    {
        // The default root node is the form itself
        rootnode = $('#akeebagui');
    }

    // Begin by slashing contents of the akeebagui DIV
    rootnode.empty();

    // This is the workhorse, looping through groupdefs and creating HTML elements
    var group_id = 0;
    $.each(data,function(headertext, groupdef) {
        // Loop for each group definition
        group_id++;

        if (empty(groupdef))
        {
            return;
        }

        // Create a fieldset container
        var container = $( document.createElement('div') );
        container
            .addClass('well')
            //.addClass('well-sm')
            .appendTo( rootnode );

        // Create a group header
        var header = $( document.createElement('h4') );
        header.attr('id', 'auigrp_'+rootnode.attr('id')+'_'+group_id);
        header.html(headertext);
        header.appendTo(container);

        // Loop each element
        $.each(groupdef, function(config_key, defdata){
            // Parameter ID
            var current_id = 'var['+config_key+']';

            if( (defdata['type'] != 'hidden') && (defdata['type'] != 'none') )
            {
                // Option row DIV
                var row_div = $(document.createElement('div')).addClass('akeeba-ui-optionrow control-group');
                row_div.appendTo(container);

                // Create label
                var label = $(document.createElement('label'));
                label.addClass('control-label')
                    .attr('for', current_id)
                    .html( defdata['title'] )
                ;
                if(defdata['description']) {
                    label
                        .attr('rel', 'popover')
                        .attr('data-original-title', defdata['title'])
                        .attr('data-content', defdata['description'])
                }
                if(defdata['bold']) label.css('font-weight','bold');
                label.appendTo( row_div );
            }

            // Create GUI representation based on type
            var controlWrapper = $(document.createElement('div')).addClass('controls');

            switch( defdata['type'] )
            {
                // A do-not-display field
                case 'none':
                    break;

                // A hidden field
                case 'hidden':
                    var hiddenfield = $(document.createElement('input')).attr({
                        type:		'hidden',
                        id:			current_id,
                        name:		current_id,
                        size:		'40',
                        value:		defdata['default']
                    });
                    hiddenfield.appendTo( container );
                    break;

                // A separator
                case 'separator':
                    var separator = $(document.createElement('div')).addClass('akeeba_ui_separator');
                    separator.appendTo( container );
                    break;

                // Checks if the field data is empty and renders the data in a hidden field
                case 'checkandhide':
                    // Container for selection & button
                    var span = $(document.createElement('span'));
                    span.appendTo( controlWrapper );
                    controlWrapper.appendTo( row_div );

                    var hiddenfield = $(document.createElement('input')).attr({
                        type:		'hidden',
                        id:			current_id,
                        name:		current_id,
                        size:		'40',
                        value:		defdata['default']
                    });
                    hiddenfield.appendTo( span );

                    var myLabel = '';
                    if(defdata['default'] == '') {
                        myLabel = defdata['labelempty'];
                    } else {
                        myLabel = defdata['labelnotempty'];
                    }
                    var span2 = $(document.createElement('span'));
                    span2
                        .text(myLabel)
                        .appendTo(span)
                        .data('labelempty',defdata['labelempty'])
                        .data('labelnotempty', defdata['labelnotempty']);
                    break;

                // An installer selection
                case 'installer':
                    // Create the select element
                    var editor = $(document.createElement('select')).attr({
                        'class':      'form-control',
                        id:			current_id,
                        name:		current_id
                    });
                    $.each(akeeba.Configuration.installers, function(key, element){
                        var option = $(document.createElement('option')).attr('value', key).html(element.name);
                        if( defdata['default'] == key ) option.attr('selected',1);
                        option.appendTo( editor );
                    });

                    editor.appendTo( controlWrapper );
                    controlWrapper.appendTo( row_div );

                    break;

                // An engine selection
                case 'engine':
                    var engine_type = defdata['subtype'];
                    if( akeeba.Configuration.engines[engine_type] == null ) break;

                    // Container for engine parameters, initially hidden
                    var engine_config_container = $(document.createElement('div')).attr({
                        id:			config_key+'_config'
                    })
                        .addClass('ui-helper-hidden well')
                        .appendTo( controlWrapper );

                    // Create the select element
                    var editor = $(document.createElement('select')).attr({
                        id:			current_id,
                        name:		current_id
                    });
                    $.each(akeeba.Configuration.engines[engine_type], function(key, element){
                        var option = $(document.createElement('option')).attr('value', key).html(element.information.title);
                        if( defdata['default'] == key ) option.attr('selected',1);
                        option.appendTo( editor );
                    });
                    editor.bind("change",function(e){
                        // When the selection changes, we have to repopulate the config container
                        // First, save any changed values
                        var old_values = new Object;
                        $(engine_config_container).find('input').each(function(i){
                            if( $(this).attr('type') == 'checkbox' )
                            {
                                old_values[$(this).attr('id')] = $(this).is(':checked');
                            }
                            else
                            {
                                old_values[$(this).attr('id')] = $(this).val();
                            }
                        });
                        // Create the new interface
                        var new_engine = $(this).val();
                        var enginedef = akeeba.Configuration.engines[engine_type][new_engine];
                        var enginetitle = enginedef.information.title;
                        var new_data = new Object;
                        var engine_params = enginedef.parameters;
                        new_data[enginetitle] = engine_params;
                        akeeba.Configuration.parseGuiData(new_data, engine_config_container);
                        $(engine_config_container)
                            .find('legend:first')
                            .after(
                                $(document.createElement('p'))
                                    .addClass('alert alert-info')
                                    .html(enginedef.information.description)
                            );
                        // Reapply changed values
                        engine_config_container.find('input').each(function(i){
                            var old = old_values[$(this).attr('id')];
                            if( (old != null) && (old != undefined) )
                            {
                                if($(this).attr('type') == 'checkbox')
                                {
                                    $(this).attr('checked', old);
                                }
                                else if ( $(this).attr('type') == 'hidden' )
                                {
                                    $(this).next().next().slider( 'value' , old );
                                }
                                else
                                {
                                    $(this).val(old);
                                }
                            }
                        });
						// Enable popovers
						engine_config_container.find('[rel="popover"]').popover({
							trigger: 'manual',
							animate: false,
							html: true,
							placement: 'bottom',
							template: '<div class="popover akeeba-bootstrap-popover" onmouseover="akeeba.jQuery(this).mouseleave(function() {akeeba.jQuery(this).hide(); });"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
						})
							.click(function(e) {
								e.preventDefault();
							})
							.mouseenter(function(e) {
								akeeba.jQuery('div.popover').remove();
								akeeba.jQuery(this).popover('show');
							});
                    });

                    // Add a configuration show/hide button
                    var button = $(document.createElement('button'))
                        .html(akeeba.Configuration.translations['UI-CONFIG'])
                        .addClass('btn btn-mini');
                    var icon = $(document.createElement('span'))
                        .addClass('icon-wrench')
                        .prependTo(button);
                    button.bind('click', function(e){
                        engine_config_container.toggleClass('ui-helper-hidden');
                        e.preventDefault();
                    });

                    var spacerSpan = $(document.createElement('span')).html('&nbsp;');

                    button.prependTo( controlWrapper );
                    spacerSpan.prependTo( controlWrapper );
                    editor.prependTo( controlWrapper );

                    controlWrapper.appendTo( row_div );

                    // Populate config container with the default engine data
                    if(akeeba.Configuration.engines[engine_type][defdata['default']] != null)
                    {
                        var new_engine = defdata['default'];
                        var enginedef = akeeba.Configuration.engines[engine_type][new_engine];
                        var enginetitle = enginedef.information.title;
                        var new_data = new Object;
                        var engine_params = enginedef.parameters;
                        new_data[enginetitle] = engine_params;

                        // Is it a protected field?
                        if(defdata['protected'] != 0) {
                            var titleSpan = $(document.createElement('span'))
                                .text(enginetitle);
                            titleSpan.prependTo(span);
                            editor.css('display','none');
                        }

                        akeeba.Configuration.parseGuiData(new_data, engine_config_container);
                        $(engine_config_container)
                            .find('legend:first')
                            .after(
                                $(document.createElement('p'))
                                    .html(enginedef.information.description)
                            );
                    }
                    break;

                // A text box with an option to launch a browser
                case 'browsedir':
                    var editor = $(document.createElement('input')).attr({
                        type:		'text',
                        'class':    'form-control',
                        id:			current_id,
                        name:		current_id,
                        size:		'30',
                        value:		defdata['default']
                    });

                    var button = $(document.createElement('button'))
                        .attr('title',akeeba.Configuration.translations['UI-BROWSE'])
                        .html('&nbsp;')
                        .addClass('btn');

                    var icon = $(document.createElement('span'))
                        .addClass('icon-folder-open')
                        .prependTo(button);

                    button.bind('click',function(event){
                        event.preventDefault();
                        if( akeeba.Configuration.onBrowser != null )
                        {
                            akeeba.Configuration.onBrowser( editor.val(), editor );
                        }
                    });

                    var span = $(document.createElement('span')).addClass('input-append');

                    editor.appendTo( span );
                    button.appendTo( span );
                    span.appendTo( span );

                    span.appendTo( controlWrapper );

                    controlWrapper.appendTo( row_div );
                    break;

                // A text box with a button
                case 'buttonedit':
                    var editortype = defdata['editortype'] == 'hidden' ? 'hidden' : 'text';

                    var editor = $(document.createElement('input')).attr({
                        type:		editortype,
                        id:			current_id,
                        name:		current_id,
                        size:		'30',
                        value:		defdata['default']
                    });
                    if(defdata['editordisabled'] == '1') {
                        editor.attr('disabled', 'disabled');
                    }

					//var buttonWrapper = $(document.createElement('span')).addClass('input-group-btn');
                    var button = $(document.createElement('button'))
                        .html(akeeba.Configuration.translations[defdata['buttontitle']])
                        .addClass('btn');
                    button.bind('click',function(event){
                        event.preventDefault();
                        var hook = defdata['hook'];
                        try {
                            eval(hook+'()');
                        } catch(err) {}
                    });

                    var span = $(document.createElement('span')).addClass('input-append');
                    editor.appendTo( span );
					button.appendTo( span );

                    span.appendTo( controlWrapper );
                    controlWrapper.appendTo( row_div );
                    break;

                // A drop-down list
                case 'enum':
                    var editor = $(document.createElement('select')).attr({
                        id:			current_id,
                        name:		current_id
                    });
                    // Create and append options
                    var enumvalues = defdata['enumvalues'].split("|");
                    var enumkeys = defdata['enumkeys'].split("|");

                    $.each(enumvalues, function(counter, value){
                        var item_description = enumkeys[counter];
                        var option = $(document.createElement('option')).attr('value', value).html(item_description);
                        if(value == defdata['default']) option.attr('selected',1);
                        option.appendTo( editor );
                    });

                    editor.appendTo( controlWrapper );
                    controlWrapper.appendTo( row_div );
                    break;

                // A simple single-line, unvalidated text box
                case 'string':
                    var editor = $(document.createElement('input')).attr({
                        type:		'text',
                        id:			current_id,
                        name:		current_id,
                        size:		'40',
                        value:		defdata['default']
                    });
                    editor.appendTo( controlWrapper );
                    controlWrapper.appendTo( row_div );
                    break;

                // A simple single-line, unvalidated password box
                case 'password':
					akeeba.Configuration.passwordFields[current_id] = defdata['default'];

                    var editor = $(document.createElement('input')).attr({
                        type:			'password',
                        id:				current_id,
                        name:			current_id,
                        size:			'40',
                        value:			defdata['default'],
						autocomplete:	'off'
                    });
                    editor.appendTo( controlWrapper );
                    controlWrapper.appendTo( row_div );
                    break;

                case 'integer':
                    // Hidden form element with the real value
                    var hidden_input = $(document.createElement('input')).attr({
                        id:		config_key,
                        name:	current_id,
                        type:	'hidden'
                    }).val(defdata['default']);
                    // Hidden custom value element
                    var custom = $(document.createElement('input'))
                        .attr('type', 'text')
                        .attr('size', '10')
                        .attr('id',config_key+'_custom')
                        .css('display','none')
                        .css('margin-left', '6px')
                        .addClass('input-mini');
                    custom.blur(function(){
                        var value = parseFloat(custom.val());
                        value = value * defdata['scale'];
                        if(value < defdata['min']) {
                            value = defdata['min'];
                        } else if(value > defdata['max']) {
                            value = defdata['max'];
                        }
                        hidden_input.val(value);
                        var newValue = value / defdata['scale'];
                        custom.val(newValue.toFixed(2));
                    });
                    // Drop-down
                    var dropdown = $(document.createElement('select')).attr({
                        id:			config_key+'_dropdown',
                        name:		config_key+'_dropdown'
                    }).addClass('input-small');
                    // Create and append options
                    var enumvalues = defdata['shortcuts'].split("|");
                    var quantizer = defdata['scale'];
                    var isPresetOption = false;
                    $.each(enumvalues, function(counter, value){
                        var item_description = value / quantizer;
                        var option = $(document.createElement('option')).attr('value', value).html(item_description.toFixed(2));
                        if(value == defdata['default']) {
                            option.attr('selected',1);
                            isPresetOption = true;
                        }
                        option.appendTo( dropdown );
                    });
                    var option = $(document.createElement('option')).attr('value', -1).html('Custom...');
                    if(!isPresetOption) {
                        option.attr('selected',1);
                        custom
                            .val( (defdata['default']/defdata['scale']).toFixed(2) )
                            .show();
                    }
                    option.appendTo( dropdown );
                    // Rig the dropdown
                    dropdown.change(function(){
                        var value = dropdown.val();
                        if(value == -1) {
                            custom
                                .val( (defdata['default']/defdata['scale']).toFixed(2) )
                                .show()
                                .focus();
                            custom.next().addClass('add-on');
                        } else {
                            hidden_input.val(value);
                            custom.hide();
                            custom.next().removeClass('add-on');
                        }
                    });
                    // Label
                    var uom = defdata['uom'];
                    if( (typeof(uom) != 'string') || empty(uom) ) {
                        uom = '';

                        dropdown.appendTo(controlWrapper);
                        custom.appendTo(controlWrapper);
                    } else {
                        var inputAppendWrapper = $(document.createElement('div'))
                            .addClass('input-append');
                        var label = $(document.createElement('span')).
                            text(' '+uom);
                        if(!isPresetOption) {
                            label.addClass('add-on');
                        }
                        dropdown.appendTo(inputAppendWrapper);
                        custom.appendTo(inputAppendWrapper);
                        label.appendTo(inputAppendWrapper);
                        inputAppendWrapper.appendTo(controlWrapper);
                    }

                    hidden_input.appendTo(controlWrapper);

                    controlWrapper.appendTo( row_div );

                    break;

                // A toggle button
                case 'bool':
                    var wrap_div = $(document.createElement('div')).addClass('akeeba-ui-checkbox');
                    // Necessary hack: when the checkbox is unchecked, nothing gets submitted.
                    // We need the hidden input to submit a zero value.
                    $(document.createElement('input')).attr({
                        name:			current_id,
                        type:			'hidden',
                        value:			0
                    }).appendTo( wrap_div );
                    // Create a checkbox
                    var editor = $(document.createElement('input')).attr({
                        name:			current_id,
                        id:				current_id,
                        type:			'checkbox',
                        value:			1
                    });
                    if( defdata['default'] != 0 ) editor.attr('checked','checked');
                    editor.appendTo( wrap_div );
                    wrap_div.appendTo( controlWrapper );
                    controlWrapper.appendTo( row_div );
                    break;

                // Button with a custom hook function
                case 'button':
                    // Create the button
                    var hook = defdata['hook'];
                    var labeltext = label.html();
                    var editor = $(document.createElement('button'))
                        .attr('id', current_id).html(labeltext)
                        .addClass('btn');
                    label.html('&nbsp;');
                    editor.bind('click', function(e){
                        e.preventDefault();
                        try {
                            eval(hook+'()');
                        } catch(err) {}
                    });
                    editor.appendTo( controlWrapper );
                    controlWrapper.appendTo( row_div );
                    break;

                // An extension is being used
                default:
                    var method = 'akeeba_render_'+defdata['type'];
                    var fn = window[method];
                    fn(config_key, defdata, label, row_div);
            }
        });

    });
};

/**
 * Restores the contents of the password fields after brain-dead browsers with broken password managers try to auto-fill
 * the wrong password to the wrong field without warning you or asking you.
 */
akeeba.Configuration.restoreDefaultPasswords = function()
{
	$.each(akeeba.Configuration.passwordFields, function(curid, defvalue){
		myElement = document.getElementById(curid);
		try {
			console.debug(curid + ' => ' + defvalue);
		} catch(e) {
		}
		// Do not remove this line. It's required when defvalue is empty. Why? BECAUSE BROWSERS ARE BRAIN DEAD!
		$(myElement).val('BROWSERS ARE BRAIN DEAD');
		// This line finally sets the fields back to its default value.
		$(myElement).val(defvalue);
	});
};

/**
 * Opens a filesystem folder browser
 *
 * @param  folder   The folder to start browsing from
 * @param  element  The element whose value we'll modify when this browser returns
 */
akeeba.Configuration.onBrowser = function (folder, element) {
    // Close dialog callback (user confirmed the new folder)
    akeeba.Configuration.onBrowserCallback = function (myFolder)
    {
        $(element).val(myFolder);
        $('#dialog').modal('hide');
    };

    // URL to load the browser
    var browserSrc = akeeba.Configuration.URLs['browser'] + encodeURIComponent(folder);

    $('#dialogBody').html('');
    var iFrame = $(document.createElement('iframe')).attr({
        src: browserSrc,
        width: '100%',
        height: 400,
        frameborder: 0,
        allowtransparency: "true"
    });

    iFrame.appendTo($('#dialogBody'));
    $('#dialog').modal('show');
};

/**
 * FTP browser callback, used to set the FTP root directory in an element
 *
 * @param  path  The path returned by the browser
 */
akeeba.Configuration.FtpBrowser.callback = function (path)
{
    var charlist = ('/').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
    var re = new RegExp('^[' + charlist + ']+', 'g');
    path = '/' + (path + '').replace(re, '');
    $(document.getElementById('var[' + akeeba.Configuration.FtpBrowser.params.key + ']')).val(path);
};

/**
 * Initialises an FTP folder browser
 *
 * @param  key        The Akeeba Engine configuration key of the field holding the FTP directory we're outputting
 * @param  paramsKey  The Akeeba Engine configuration key prefix of the fields holding FTP connection information
 */
akeeba.Configuration.FtpBrowser.initialise = function(key, paramsKey)
{
    akeeba.Configuration.FtpBrowser.params.host = $(document.getElementById('var[' + paramsKey + '.host]')).val();
    akeeba.Configuration.FtpBrowser.params.port = $(document.getElementById('var[' + paramsKey + '.port]')).val();
    akeeba.Configuration.FtpBrowser.params.username = $(document.getElementById('var[' + paramsKey + '.user]')).val();
    akeeba.Configuration.FtpBrowser.params.password = $(document.getElementById('var[' + paramsKey + '.pass]')).val();
    akeeba.Configuration.FtpBrowser.params.passive = $(document.getElementById('var[' + paramsKey + '.passive_mode]')).is(':checked');
    akeeba.Configuration.FtpBrowser.params.ssl = $(document.getElementById('var[' + paramsKey + '.ftps]')).is(':checked');
    akeeba.Configuration.FtpBrowser.params.directory = $(document.getElementById('var[' + paramsKey + '.initial_directory]')).val();

    akeeba.Configuration.FtpBrowser.params.key = key;

    akeeba.Configuration.FtpBrowser.open();
};

/**
 * Opens the FTP directory browser
 */
akeeba.Configuration.FtpBrowser.open = function () {
    var ftp_dialog_element = $("#ftpdialog");

    ftp_dialog_element.css('display', 'block');
    ftp_dialog_element.removeClass('ui-state-error');

    $('#ftpdialogOkButton').click(function(e){
        akeeba.Configuration.FtpBrowser.callback(akeeba.Configuration.FtpBrowser.params.directory);
        $("#ftpdialog").modal('hide');
    });

    $("#ftpdialog").modal('show');

    $('#ftpBrowserErrorContainer').css('display', 'none');
    $('#ftpBrowserFolderList').html('');
    $('#ftpBrowserCrumbs').html('');

    // URL to load the browser
    akeeba.System.params.AjaxURL = akeeba.Configuration.URLs.ftpBrowser;

    if (empty(akeeba.Configuration.FtpBrowser.params.directory))
    {
        akeeba.Configuration.FtpBrowser.params.directory = '';
    }

    var data = {
        'host':         akeeba.Configuration.FtpBrowser.params.host,
        'username':     akeeba.Configuration.FtpBrowser.params.username,
        'password':     akeeba.Configuration.FtpBrowser.params.password,
        'passive':      (akeeba.Configuration.FtpBrowser.params.passive ? 1 : 0),
        'ssl':          (akeeba.Configuration.FtpBrowser.params.ssl ? 1 : 0),
        'directory':    akeeba.Configuration.FtpBrowser.params.directory
    };

    // Do AJAX call & Render results
    akeeba.System.doAjax(
        data,
        function (data)
        {
            if (data.error != false)
            {
                // An error occured
                $('#ftpBrowserError').html(data.error);
                $('#ftpBrowserErrorContainer').css('display', 'block');
                $('#ftpBrowserFolderList').css('display', 'none');
                $('#ak_crumbs').css('display', 'none');
            }
            else
            {
                // Create the interface
                $('#ftpBrowserErrorContainer').css('display', 'none');

                // Display the crumbs
                if (!empty(data.breadcrumbs)) {
                    $('#ak_crumbs').css('display', 'block');
                    $('#ak_crumbs').html('');
                    var relativePath = '/';

                    akeeba.Configuration.FtpBrowser.addCrumb(akeeba.Configuration.translations['UI-ROOT'], '/', $('#ak_crumbs'));

                    $.each(data.breadcrumbs, function (i, crumb) {
                        relativePath += '/' + crumb;

                        akeeba.Configuration.FtpBrowser.addCrumb(crumb, relativePath, $('#ak_crumbs'));
                    });
                } else {
                    $('#ftpBrowserCrumbs').css('display', 'none');
                }

                // Display the list of directories
                if (!empty(data.list)) {
                    $('#ftpBrowserFolderList').css('display', 'block');

                    // If the directory in the browser is empty, let's inject it with the parent dir, otherwise if the user immediately clicks on "Use" gets a wrong path
                    if(!akeeba.Configuration.FtpBrowser.params.directory)
                    {
                        akeeba.Configuration.FtpBrowser.params.directory = data.directory;
                    }

                    $.each(data.list, function (i, item) {
                        akeeba.Configuration.FtpBrowser.createLink(data.directory + '/' + item, item, $('#ftpBrowserFolderList'));
                    });
                } else {
                    $('#ftpBrowserFolderList').css('display', 'none');
                }
            }
        },
        function (message) {
            $('#ftpBrowserError').html(message);
            $('#ftpBrowserErrorContainer').css('display', 'block');
            $('#ftpBrowserFolderList').css('display', 'none');
            $('#ftpBrowserCrumbs').css('display', 'none');
        },
        false
    );
};

/**
 * Creates a directory link for the FTP browser UI
 *
 * @param  path       The directory to link to
 * @param  label      How to display it
 * @param  container  The containing element
 */
akeeba.Configuration.FtpBrowser.createLink = function(path, label, container, ftpObject)
{
    if (typeof ftpObject == 'undefined')
    {
        ftpObject = akeeba.Configuration.FtpBrowser;
    }

    var row = $(document.createElement('tr'));
    var cell = $(document.createElement('td')).appendTo(row);

    var myElement = $(document.createElement('a'))
        .text(label)
        .click(function () {
            ftpObject.params.directory = path;
            ftpObject.open();
        })
        .appendTo(cell);
    row.appendTo($(container));
};

/**
 * Adds a breadcrumb to the FTP browser
 *
 * @param  crumb         How to display it
 * @param  relativePath  The relative path to the current directory
 * @param  container     The containing element
 */
akeeba.Configuration.FtpBrowser.addCrumb = function (crumb, relativePath, container, ftpObject)
{
    if (typeof ftpObject == 'undefined')
    {
        ftpObject = akeeba.Configuration.FtpBrowser;
    }

    var li = $(document.createElement('li'));

    $(document.createElement('a'))
        .html(crumb)
        .click(function (e) {
            ftpObject.params.directory = relativePath;
            ftpObject.open();
            e.preventDefault();
        })
        .appendTo(li);

    li.appendTo(container);
};

/**
 * Initialises an SFTP folder browser
 *
 * @param  key        The Akeeba Engine configuration key of the field holding the SFTP directory we're outputting
 * @param  paramsKey  The Akeeba Engine configuration key prefix of the fields holding SFTP connection information
 */
akeeba.Configuration.SftpBrowser.initialise = function(key, paramsKey)
{
    akeeba.Configuration.SftpBrowser.params.host = $(document.getElementById('var[' + paramsKey + '.host]')).val();
    akeeba.Configuration.SftpBrowser.params.port = $(document.getElementById('var[' + paramsKey + '.port]')).val();
    akeeba.Configuration.SftpBrowser.params.username = $(document.getElementById('var[' + paramsKey + '.user]')).val();
    akeeba.Configuration.SftpBrowser.params.password = $(document.getElementById('var[' + paramsKey + '.pass]')).val();
    akeeba.Configuration.SftpBrowser.params.directory = $(document.getElementById('var[' + paramsKey + '.initial_directory]')).val();
    akeeba.Configuration.SftpBrowser.params.privKey = $(document.getElementById('var[' + paramsKey + '.privkey]')).val();
    akeeba.Configuration.SftpBrowser.params.pubKey = $(document.getElementById('var[' + paramsKey + '.pubkey]')).val();

    akeeba.Configuration.SftpBrowser.params.key = key;

    akeeba.Configuration.SftpBrowser.open();
};

/**
 * Opens the SFTP directory browser
 */
akeeba.Configuration.SftpBrowser.open = function ()
{
    var ftp_dialog_element = $("#sftpdialog");

    ftp_dialog_element.css('display', 'block');
    ftp_dialog_element.removeClass('ui-state-error');

    $('#sftpdialogOkButton').click(function(e){
        akeeba.Configuration.SftpBrowser.callback(akeeba.Configuration.SftpBrowser.params.directory);
        $("#sftpdialog").modal('hide');
    });

    $("#sftpdialog").modal('show');

    $('#sftpBrowserErrorContainer').css('display', 'none');
    $('#sftpBrowserFolderList').html('');
    $('#sftpBrowserCrumbs').html('');

    // URL to load the browser
    akeeba.System.params.AjaxURL = akeeba.Configuration.URLs.sftpBrowser;

    if (empty(akeeba.Configuration.SftpBrowser.params.directory))
    {
        akeeba.Configuration.SftpBrowser.params.directory = '';
    }

    var data = {
        'host':         akeeba.Configuration.SftpBrowser.params.host,
        'port':         akeeba.Configuration.SftpBrowser.params.port,
        'username':     akeeba.Configuration.SftpBrowser.params.username,
        'password':     akeeba.Configuration.SftpBrowser.params.password,
        'directory':    akeeba.Configuration.SftpBrowser.params.directory,
        'privkey':      akeeba.Configuration.SftpBrowser.params.privKey,
        'pubkey':       akeeba.Configuration.SftpBrowser.params.pubKey
    };

    // Do AJAX call & Render results
    akeeba.System.doAjax(
        data,
        function (data)
        {
            if (data.error != false)
            {
                // An error occured
                $('#sftpBrowserError').html(data.error);
                $('#sftpBrowserErrorContainer').css('display', 'block');
                $('#sftpBrowserFolderList').css('display', 'none');
                $('#ak_scrumbs').css('display', 'none');
            }
            else
            {
                // Create the interface
                $('#ftpBrowserErrorContainer').css('display', 'none');

                // Display the crumbs
                if (!empty(data.breadcrumbs)) {
                    $('#ak_scrumbs').css('display', 'block');
                    $('#ak_scrumbs').html('');
                    var relativePath = '/';

                    akeeba.Configuration.FtpBrowser.addCrumb(akeeba.Configuration.translations['UI-ROOT'], '/', $('#ak_scrumbs'), akeeba.Configuration.SftpBrowser);

                    $.each(data.breadcrumbs, function (i, crumb) {
                        relativePath += '/' + crumb;

                        akeeba.Configuration.FtpBrowser.addCrumb(crumb, relativePath, $('#ak_scrumbs'), akeeba.Configuration.SftpBrowser);
                    });
                } else {
                    $('#sftpBrowserCrumbs').css('display', 'none');
                }

                // Display the list of directories
                if (!empty(data.list)) {
                    $('#sftpBrowserFolderList').css('display', 'block');

                    // If the directory in the browser is empty, let's inject it with the parent dir, otherwise if the user immediately clicks on "Use" gets a wrong path
                    if(!akeeba.Configuration.SftpBrowser.params.directory)
                    {
                        akeeba.Configuration.SftpBrowser.params.directory = data.directory;
                    }

                    $.each(data.list, function (i, item) {
                        akeeba.Configuration.FtpBrowser.createLink(data.directory + '/' + item, item, $('#sftpBrowserFolderList'), akeeba.Configuration.SftpBrowser);
                    });
                } else {
                    $('#sftpBrowserFolderList').css('display', 'none');
                }
            }
        },
        function (message) {
            $('#sftpBrowserError').html(message);
            $('#sftpBrowserErrorContainer').css('display', 'block');
            $('#sftpBrowserFolderList').css('display', 'none');
            $('#sftpBrowserCrumbs').css('display', 'none');
        },
        false
    );
};

/**
 * SFTP browser callback, used to set the FTP root directory in an element
 *
 * @param  path  The path returned by the browser
 */
akeeba.Configuration.SftpBrowser.callback = function (path)
{
	var charlist = ('/').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
	var re = new RegExp('^[' + charlist + ']+', 'g');
	path = '/' + (path + '').replace(re, '');
	$(document.getElementById('var[' + akeeba.Configuration.SftpBrowser.params.key + ']')).val(path);
};

akeeba.Configuration.FtpTest.testConnection = function(buttonKey, configKey)
{
    var button = $(document.getElementById(buttonKey));
    akeeba.Configuration.FtpTest.buttonKey = buttonKey;

    button.attr('disabled', 'disabled');

    var data = {
        host:           $(document.getElementById('var[' + configKey + '.host]')).val(),
        port:           $(document.getElementById('var[' + configKey + '.port]')).val(),
        user:           $(document.getElementById('var[' + configKey + '.user]')).val(),
        pass:           $(document.getElementById('var[' + configKey + '.pass]')).val(),
        initdir:        $(document.getElementById('var[' + configKey + '.initial_directory]')).val(),
        usessl:         $(document.getElementById('var[' + configKey + '.ftps]')).is(':checked'),
        passive:        $(document.getElementById('var[' + configKey + '.passive_mode]')).is(':checked')
    };

    // Construct the query
    akeeba.System.params.AjaxURL = akeeba.Configuration.URLs.testFtp;

    akeeba.System.doAjax(
        data,
        function (res)
        {
            var button = $(document.getElementById(akeeba.Configuration.FtpTest.buttonKey));
            button.removeAttr('disabled');

            $('#testFtpDialogBodyOk').css('display', 'none');
            $('#testFtpDialogBodyFail').css('display', 'none');

            if (res === true) {
                $('#testFtpDialogLabel').html(akeeba.Configuration.translations['UI-TESTFTP-OK']);
                $('#testFtpDialogBodyOk').html(akeeba.Configuration.translations['UI-TESTFTP-OK']);
                $('#testFtpDialogBodyOk').css('display', 'block');
                $('#testFtpDialogBodyFail').css('display', 'none');
            }
            else
            {
                $('#testFtpDialogLabel').html(akeeba.Configuration.translations['UI-TESTFTP-FAIL']);
                $('#testFtpDialogBodyFail').html(res);
                $('#testFtpDialogBodyOk').css('display', 'none');
                $('#testFtpDialogBodyFail').css('display', 'block');
            }

            if ($('#testFtpDialog > div > div').length == 0)
            {
                // Joomla! 2.5
                $('#testFtpDialog').clone()
                    .attr('id', 'testFtpDialogClone')
                    .css('display', 'block')
                    .appendTo($('body'));

                SqueezeBox.open(document.getElementById('testFtpDialogClone'), {
                    handler: 'adopt',
                    size: {x: 400, y: 300}
                });
            }
            else
            {
                // Joomla! 3.x
                $('#testFtpDialog').modal('show');
            }

        }, null, false, 15000
    )
};

akeeba.Configuration.SftpTest.testConnection = function(buttonKey, configKey)
{
    var button = $(document.getElementById(buttonKey));
    akeeba.Configuration.SftpTest.buttonKey = buttonKey;

    button.attr('disabled', 'disabled');

    var data = {
        host:           $(document.getElementById('var[' + configKey + '.host]')).val(),
        port:           $(document.getElementById('var[' + configKey + '.port]')).val(),
        user:           $(document.getElementById('var[' + configKey + '.user]')).val(),
        pass:           $(document.getElementById('var[' + configKey + '.pass]')).val(),
        initdir:        $(document.getElementById('var[' + configKey + '.initial_directory]')).val(),
        privkey:        $(document.getElementById('var[' + configKey + '.privkey]')).val(),
        pubkey:         $(document.getElementById('var[' + configKey + '.pubkey]')).val()
    };

    // Construct the query
    akeeba.System.params.AjaxURL = akeeba.Configuration.URLs.testSftp;

    akeeba.System.doAjax(
        data,
        function (res)
        {
            var button = $(document.getElementById(akeeba.Configuration.SftpTest.buttonKey));
            button.removeAttr('disabled');

            $('#testFtpDialogBodyOk').css('display', 'none');
            $('#testFtpDialogBodyFail').css('display', 'none');

            if (res === true) {
                $('#testFtpDialogLabel').html(akeeba.Configuration.translations['UI-TESTSFTP-OK']);
                $('#testFtpDialogBodyOk').html(akeeba.Configuration.translations['UI-TESTSFTP-OK']);
                $('#testFtpDialogBodyOk').css('display', 'block');
                $('#testFtpDialogBodyFail').css('display', 'none');
            }
            else
            {
                $('#testFtpDialogLabel').html(akeeba.Configuration.translations['UI-TESTSFTP-FAIL']);
                $('#testFtpDialogBodyFail').html(res);
                $('#testFtpDialogBodyOk').css('display', 'none');
                $('#testFtpDialogBodyFail').css('display', 'block');
            }

            if ($('#testFtpDialog > div > div').length == 0)
            {
                // Joomla! 2.5
                $('#testFtpDialog').clone()
                    .attr('id', 'testFtpDialogClone')
                    .css('display', 'block')
                    .appendTo($('body'));

                SqueezeBox.open(document.getElementById('testFtpDialogClone'), {
                    handler: 'adopt',
                    size: {x: 400, y: 300}
                });
            }
            else
            {
                // Joomla! 3.x
                $('#testFtpDialog').modal('show');
            }
        }, null, false, 15000
    )
};

// =====================================================================================================================
// Initialise hooks used by the definition INI files
// =====================================================================================================================

akeeba_directftp_init_browser = function()
{
    akeeba.Configuration.FtpBrowser.initialise('engine.archiver.directftp.initial_directory', 'engine.archiver.directftp')
};

akeeba_postprocftp_init_browser = function()
{
    akeeba.Configuration.FtpBrowser.initialise('engine.postproc.ftp.initial_directory', 'engine.postproc.ftp')
};

akeeba_directsftp_init_browser = function()
{
    akeeba.Configuration.SftpBrowser.initialise('engine.archiver.directsftp.initial_directory', 'engine.archiver.directsftp')
};

akeeba_postprocsftp_init_browser = function()
{
    akeeba.Configuration.FtpBrowser.initialise('engine.postproc.sftp.initial_directory', 'engine.postproc.sftp')
};

directftp_test_connection = function()
{
    akeeba.Configuration.FtpTest.testConnection('engine.archiver.directftp.ftp_test','engine.archiver.directftp');
};

postprocftp_test_connection = function()
{
    akeeba.Configuration.FtpTest.testConnection('engine.postproc.ftp.ftp_test','engine.postproc.ftp');
};

directsftp_test_connection = function()
{
    akeeba.Configuration.SftpTest.testConnection('engine.archiver.directsftp.sftp_test','engine.archiver.directsftp');
};

postprocsftp_test_connection = function()
{
    akeeba.Configuration.SftpTest.testConnection('engine.postproc.sftp.sftp_test','engine.postproc.sftp');
};

akconfig_dropbox_openoauth = function()
{
    var url = akeeba.Configuration.URLs.dpeauthopen;

    if (url.indexOf("?") == -1)
    {
        url = url + '?';
    }
    else
    {
        url = url + '&';
    }

    window.open(url + 'engine=dropbox', 'akeeba_dropbox_window', 'width=1010,height=500');
};

akconfig_dropbox_gettoken = function()
{
    akeeba.System.AjaxURL = akeeba.Configuration.URLs['dpecustomapi'];

    var data = {
		engine:		"dropbox",
		method:		"getauth"
	};

    akeeba.System.doAjax(
        data,
        function(res)
        {
            if (res['error'] != '')
            {
                alert('ERROR: Could not complete authentication; please retry');
            }
            else
            {
                $(document.getElementById('var[engine.postproc.dropbox.token]')).val(res.token.oauth_token);
                $(document.getElementById('var[engine.postproc.dropbox.token_secret]')).val(res.token.oauth_token_secret);
                $(document.getElementById('var[engine.postproc.dropbox.uid]')).val(res.token.uid);
                alert('Authentication successful!');
            }
        }, function(errorMessage) {
			alert('ERROR: Could not complete authentication; please retry' + "\n" + errorMessage);
		}, false, 15000
    );
};

akconfig_onedrive_openoauth = function()
{
	var url = akeeba.Configuration.URLs.dpeauthopen;

	if (url.indexOf("?") == -1)
	{
		url = url + '?';
	}
	else
	{
		url = url + '&';
	}

	window.open(url + 'engine=onedrive', 'akeeba_onedrive_window', 'width=1010,height=500');
};

akeeba_onedrive_oauth_callback = function (data)
{
	// Update the tokens
	$(document.getElementById('var[engine.postproc.onedrive.access_token]')).val(data.access_token);
	$(document.getElementById('var[engine.postproc.onedrive.refresh_token]')).val(data.refresh_token);

	// Close the window
	myWindow = window.open("", "akeeba_onedrive_window");
	myWindow.close();
}

}(akeeba.jQuery));