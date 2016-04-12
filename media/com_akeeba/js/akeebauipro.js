/**
 * Akeeba Backup Pro
 * The modular PHP5 site backup software solution
 * This file contains the jQuery-based client-side user interface logic for
 * the features available only in the Pro version
 * @package akeebaui
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @version 1.0
 **/

/**
 * Setup (required for Joomla! 3)
 */
if(typeof(akeeba) == 'undefined') {
	var akeeba = {};
}
if(typeof(akeeba.jQuery) == 'undefined') {
	akeeba.jQuery = jQuery.noConflict();
}

//=============================================================================
//Akeeba Backup Pro - Regular expression based files and folders filters
//=============================================================================

function escapeHTML(rawData)
{
	return rawData.split("&").join("&amp;").split( "<").join("&lt;").split(">").join("&gt;");
}

//=============================================================================
//Akeeba Backup Pro - Import arbitrary archives from S3
//=============================================================================

function ak_s3import_resetroot()
{
	(function($) {
		$('#ak_s3import_folder').val('');
	})(akeeba.jQuery);

	return true;
}

function ak_s3import_chdir(prefix)
{
	(function($) {
		$('#ak_s3import_folder').val(prefix);
		$('#adminForm').submit();
	})(akeeba.jQuery);
}

//=============================================================================
//Akeeba Backup Pro - Box.net integration
//=============================================================================

function akconfig_box_openoauth()
{
	(function($) {
		window.open('index.php?option=com_akeeba&view=config&task=dpeoauthopen&engine=box','akeeba_box_window','width=1010,height=500');
	})(akeeba.jQuery);
}

function akconfig_box_gettoken()
{
	(function($) {
        akeeba.System.params.AjaxURL = 'index.php?option=com_akeeba&view=config&task=dpecustomapi';
		var data = new Object();
		data['engine'] = 'box';
		data['method'] = 'getauth';
        akeeba.System.doAjax(data, function(res){
			if(res['error'] != '') {
				alert('ERROR: Could not complete authentication; please retry');
			} else {
				$(document.getElementById('var[engine.postproc.box.token]')).val(res['token']);
				alert('OK!');
			}
		});
	})(akeeba.jQuery);
}

function akeeba_render_boxfolders(config_key, defdata, label, row_div)
{
	(function($) {
		var current_id = 'var['+config_key+']';
		var editor = $(document.createElement('select')).attr({
			id:			'akeeba_box_folders',
			name:		current_id
		});

		var button = $(document.createElement('button')).html(akeeba_translations['UI-REFRESH']);
		button.bind('click', function(e){
			e.preventDefault();

            akeeba.System.params.AjaxURL = 'index.php?option=com_akeeba&view=config&task=dpecustomapi';
			var data = new Object();
			data['engine'] = 'box';
			data['method'] = 'gettree';
            akeeba.System.doAjax(data, function(res){
				$('#akeeba_box_folders').html('');
				option = $(document.createElement('option')).attr('value',0).html('- Folder -');
				option.appendTo('#akeeba_box_folders');
				$.each(res, function(id, name){
					option = $(document.createElement('option')).attr('value',id).html(name);
					if(id == defdata['default']) option.attr('selected','selected');
					option.appendTo('#akeeba_box_folders');
				});
			});

			return false;
		})

		editor.appendTo( row_div );
		button.appendTo( row_div );
		button.click();
	})(akeeba.jQuery);
}

//=============================================================================
//Akeeba Backup Pro - DropBox API v1 integration
//=============================================================================

function akconfig_dropbox_openoauth()
{
	(function($) {
		window.open('index.php?option=com_akeeba&view=config&task=dpeoauthopen&engine=dropbox','akeeba_dropbox_window','width=1010,height=500');
	})(akeeba.jQuery);
}

function akconfig_dropbox_gettoken()
{
	(function($) {
        akeeba.System.params.AjaxURL = 'index.php?option=com_akeeba&view=config&task=dpecustomapi';
		var data = new Object();
		data['engine'] = 'dropbox';
		data['method'] = 'getauth';
        akeeba.System.doAjax(data, function(res){
			if(res['error'] != '') {
				alert('ERROR: Could not complete authentication; please retry');
			} else {
				$(document.getElementById('var[engine.postproc.dropbox.token]')).val(res.token.oauth_token);
				$(document.getElementById('var[engine.postproc.dropbox.token_secret]')).val(res.token.oauth_token_secret);
				$(document.getElementById('var[engine.postproc.dropbox.uid]')).val(res.token.uid);
				alert('OK!');
			}
		});
	})(akeeba.jQuery);
}

//=============================================================================
//Akeeba Backup Pro - DropBox API v2 integration
//=============================================================================
function akconfig_dropbox2_openoauth()
{
	(function($) {
		window.open('index.php?option=com_akeeba&view=config&task=dpeoauthopen&engine=dropbox2','akeeba_dropbox2_window','width=1010,height=500');
	})(akeeba.jQuery);
}

function akeeba_dropbox2_oauth_callback(data)
{
	(function($) {
		// Update the tokens
		$(document.getElementById('var[engine.postproc.dropbox2.access_token]')).val(data.access_token);

		// Close the window
		myWindow = window.open("", "akeeba_dropbox2_window");
		myWindow.close();
	})(akeeba.jQuery);
}

//=============================================================================
//Akeeba Backup Pro - OneDrive integration
//=============================================================================
function akconfig_onedrive_openoauth()
{
	(function($) {
		window.open('index.php?option=com_akeeba&view=config&task=dpeoauthopen&engine=onedrive','akeeba_onedrive_window','width=1010,height=500');
	})(akeeba.jQuery);
}

function akeeba_onedrive_oauth_callback(data)
{
	(function($) {
		// Update the tokens
		$(document.getElementById('var[engine.postproc.onedrive.access_token]')).val(data.access_token);
		$(document.getElementById('var[engine.postproc.onedrive.refresh_token]')).val(data.refresh_token);

		// Close the window
		myWindow = window.open("", "akeeba_onedrive_window");
		myWindow.close();
	})(akeeba.jQuery);
}

//=============================================================================
//Akeeba Backup Pro - Google Drive integration
//=============================================================================
function akconfig_googledrive_openoauth()
{
	(function($) {
		window.open('index.php?option=com_akeeba&view=config&task=dpeoauthopen&engine=googledrive','akeeba_googledrive_window','width=1010,height=500');
	})(akeeba.jQuery);
}

function akeeba_googledrive_oauth_callback(data)
{
	(function($) {
		// Update the tokens
		$(document.getElementById('var[engine.postproc.googledrive.access_token]')).val(data.access_token);
		$(document.getElementById('var[engine.postproc.googledrive.refresh_token]')).val(data.refresh_token);

		// Close the window
		myWindow = window.open("", "akeeba_googledrive_window");
		myWindow.close();
	})(akeeba.jQuery);
}