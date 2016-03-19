/**
 * Akeeba Backup
 *
 * @package    akeeba
 * @copyright  Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license    GNU GPL version 3 or, at your option, any later version
 **/

if (typeof akeeba == 'undefined')
{
	var akeeba = {};
}

if (typeof akeeba.Restore == 'undefined')
{
	akeeba.Restore = {
		errorCallback: null,
		statistics:    {
			inbytes:  0,
			outbytes: 0,
			files:    0
		},
		factory:       null,
		password:      null,
		ajaxURL:       null,
		mainURL:       null,
		translations:  {}
	};

}

(function($){
/**
 * Callback script for AJAX errors
 * @param msg
 * @return
 */
akeeba.Restore.errorCallbackDefault = function (msg)
{
	$('#restoration-progress').hide();
	$('#restoration-error').show();
	$('#backup-error-message').html(msg);
};

/**
 * Performs an AJAX request to the restoration script (restore.php)
 * @param data
 * @param successCallback
 * @param errorCallback
 * @return
 */
akeeba.Restore.doAjax = function (data, successCallback, errorCallback)
{
	json = JSON.stringify(data);

	if (akeeba.Restore.password.length > 0)
	{
		json = AesCtr.encrypt(json, akeeba.Restore.password, 128);
	}

	var post_data = { json: json };

	// Make the request uncacheable
	var now = new Date().getTime() / 1000;
	var s = parseInt(now, 10);
	var microtime = Math.round((now - s) * 1000) / 1000;
	post_data._cacheBustingJunk = microtime;

	var structure =
	{
		type:    "POST",
		url:     akeeba.Restore.ajaxURL,
		cache:   false,
		data:    post_data,
		timeout: 600000,
		success: function (msg)
		{
			// Initialize
			var junk = null;
			var message = "";

			// Get rid of junk before the data
			var valid_pos = msg.indexOf('###');

			if (valid_pos == -1)
			{
				// Valid data not found in the response
				msg = 'Invalid AJAX data: ' + msg;

				if (errorCallback == null)
				{
					if (akeeba.Restore.errorCallback != null)
					{
						akeeba.Restore.errorCallback(msg);
					}
				}
				else
				{
					errorCallback(msg);
				}

				return;
			}
			else if (valid_pos != 0)
			{
				// Data is prefixed with junk
				junk = msg.substr(0, valid_pos);
				message = msg.substr(valid_pos);
			}
			else
			{
				message = msg;
			}

			message = message.substr(3); // Remove triple hash in the beginning

			// Get of rid of junk after the data
			valid_pos = message.lastIndexOf('###');
			message = message.substr(0, valid_pos); // Remove triple hash in the end

			// Decrypt if required
			if (akeeba.Restore.password.length > 0)
			{
				message = AesCtr.decrypt(message, akeeba.Restore.password, 128);
			}

			try
			{
				var data = JSON.parse(message);
			}
			catch (err)
			{
				errorMessage = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";
				if (errorCallback == null)
				{
					if (akeeba.Restore.errorCallback != null)
					{
						akeeba.Restore.errorCallback(errorMessage);
					}
				}
				else
				{
					errorCallback(errorMessage);
				}
				return;
			}

			// Call the callback function
			successCallback(data);
		},
		error:   function (Request, textStatus, errorThrown)
		{
			var message = 'AJAX Loading Error: ' + textStatus;
			if (errorCallback == null)
			{
				if (akeeba.Restore.errorCallback != null)
				{
					akeeba.Restore.errorCallback(message);
				}
			}
			else
			{
				errorCallback(message);
			}
		}
	};

	$.ajax(structure);
};

/**
 * Starts the timer for the last response timer
 *
 * @param   max_allowance  Maximum time allowance in seconds
 * @param   bias           Runtime bias in %
 */
akeeba.Restore.startTimeoutBar = function(max_allowance, bias)
{
	var lastResponseSeconds = 0;

	$('#response-timer div.text').everyTime(1000, 'lastReponse', function(){
		lastResponseSeconds++;
		var lastText = akeeba.Restore.translations['UI-LASTRESPONSE'].replace('%s', lastResponseSeconds.toFixed(0));
		$('#response-timer div.text').html(lastText);
	});
};

/**
 * Resets the last response timer bar
 */
akeeba.Restore.resetTimeoutBar = function()
{
	$('#response-timer div.text').stopTime();
	var lastText = akeeba.Restore.translations['UI-LASTRESPONSE'].replace('%s', '0');
	$('#response-timer div.text').html(lastText);
};

/**
 * Pings the restoration script (making sure its executable!!)
 * @return
 */
akeeba.Restore.pingRestoration = function()
{
	// Reset variables
	akeeba.Restore.statistics.inbytes = 0;
	akeeba.Restore.statistics.outbytes = 0;
	akeeba.Restore.statistics.files = 0;

	// Do AJAX post
	var post = { task: 'ping' };
	akeeba.Restore.startTimeoutBar(5000, 80);
	akeeba.Restore.doAjax(post, function (data)
	{
		akeeba.Restore.start(data);
	});
};

/**
 * Starts the restoration
 * @return
 */
akeeba.Restore.start = function()
{
	// Reset variables
	akeeba.Restore.statistics.inbytes = 0;
	akeeba.Restore.statistics.outbytes = 0;
	akeeba.Restore.statistics.files = 0;

	// Do AJAX post
	var post = { task: 'startRestore' };
	akeeba.Restore.startTimeoutBar(5000, 80);
	akeeba.Restore.doAjax(post, function (data)
	{
		akeeba.Restore.step(data);
	});
};

/**
 * Steps through the restoration
 * @param data
 * @return
 */
akeeba.Restore.step = function(data)
{
	akeeba.Restore.resetTimeoutBar();

	if (data.status == false)
	{
		// handle failure
		akeeba.Restore.errorCallbackDefault(data.message);
	}
	else
	{
		if (data.done)
		{
			akeeba.Restore.factory = data.factory;
			// handle finish
			$('#restoration-progress').hide();
			$('#restoration-extract-ok').show();
		}
		else
		{
			// Add data to variables
			akeeba.Restore.statistics.inbytes += data.bytesIn;
			akeeba.Restore.statistics.outbytes += data.bytesOut;
			akeeba.Restore.statistics.files += data.files;

			// Display data
			$('#extbytesin').html(akeeba.Restore.statistics.inbytes);
			$('#extbytesout').html(akeeba.Restore.statistics.outbytes);
			$('#extfiles').html(akeeba.Restore.statistics.files);

			// Do AJAX post
			post = {
				task:    'stepRestore',
				factory: data.factory
			};
			akeeba.Restore.startTimeoutBar(5000, 80);
			akeeba.Restore.doAjax(post, function (data)
			{
				akeeba.Restore.step(data);
			});
		}
	}
};

akeeba.Restore.finalize = function()
{
	// Do AJAX post
	var post = { task: 'finalizeRestore', factory: akeeba.Restore.factory };
	akeeba.Restore.startTimeoutBar(5000, 80);
	akeeba.Restore.doAjax(post, function (data)
	{
		akeeba.Restore.finished(data);
	});
};

akeeba.Restore.finished = function()
{
	// We're just finished - return to the back-end Control Panel
	window.location = akeeba.Restore.mainURL;
};

akeeba.Restore.runInstaller = function ()
{
    window.open('../installation/index.php','abiinstaller');
    (function($) {
        $('#restoration-finalize').show();
    })(akeeba.jQuery);
}
}(akeeba.jQuery));