/**
 * Akeeba Backup
 *
 * @package    akeeba
 * @copyright  Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license    GNU GPL version 3 or, at your option, any later version
 **/

if(typeof(akeeba) == 'undefined') {
	var akeeba = {};
}

if (typeof akeeba.Backup == 'undefined')
{
    akeeba.Backup = {
        URLs:               {},
        tag:                '',
		backupid:			null,
        translations:       {},
        currentDomain:      null,
        domains:            {},
        commentEditorSave:  null,
        maxExecutionTime:   14,
        runtimeBias:        75,
        srpInfo:            {},
        returnUrl:          '',
        isSTW:              false,
		resume:				{
			enabled: true,
			timeout: 10,
			maxRetries: 3,
			retry: 0
		}
    }
}

(function($){
/**
 * Start the timer which launches the next backup step. This allows us to prevent deep nesting of AJAX calls which could
 * lead to performance issues on long backups.
 *
 * @param   waitTime  How much time to wait before starting a backup step, in msec (default: 10)
 */
akeeba.Backup.timer = function(waitTime)
{
	if (waitTime <= 0)
	{
		waitTime = 10;
	}

    setTimeout('akeeba.Backup.timerTick()', waitTime);
};

/**
 * This is used by the timer() method to run the next backup step
 */
akeeba.Backup.timerTick = function()
{
    try
    {
        console.log('Timer tick');
    }
    catch(e) {
    }

	// Reset the timer
	akeeba.Backup.resetTimeoutBar();
	akeeba.Backup.startTimeoutBar(akeeba.Backup.maxExecutionTime, akeeba.Backup.runtimeBias);

	// Run the step
    akeeba.System.doAjax({
        ajax:   'step',
        tag:    akeeba.Backup.tag,
		backupid: akeeba.Backup.backupid
    }, akeeba.Backup.onStep, akeeba.Backup.onError, false);
};

/**
 * Starts the timer for the last response timer
 *
 * @param   max_allowance  Maximum time allowance in seconds
 * @param   bias           Runtime bias in %
 */
akeeba.Backup.startTimeoutBar = function(max_allowance, bias)
{
    var lastResponseSeconds = 0;

    $('#response-timer div.text').everyTime(1000, 'lastResponse', function(){
        lastResponseSeconds++;
        var lastText = akeeba.Backup.translations['UI-LASTRESPONSE'].replace('%s', lastResponseSeconds.toFixed(0));

        $('#response-timer div.text').html(lastText);
    });
};

/**
 * Resets the last response timer bar
 */
akeeba.Backup.resetTimeoutBar = function()
{
	Piecon.setOptions({
		color: '#333333',
		background: '#e0e0e0',
		shadow: '#000000',
		fallback: 'force'
	});
    $('#response-timer div.text').stopTime();
    var lastText = akeeba.Backup.translations['UI-LASTRESPONSE'].replace('%s', '0');
    $('#response-timer div.text').html(lastText);
};

/**
 * Starts the timer for the last response timer
 */
akeeba.Backup.startRetryTimeoutBar = function()
{
	var remainingSeconds = akeeba.Backup.resume.timeout;

    $('#akeeba-retry-timeout').everyTime(1000, 'retryTimeout', function(){
		remainingSeconds--;
		$('#akeeba-retry-timeout').html(remainingSeconds.toFixed(0));

		if (remainingSeconds == 0)
		{
			$('#akeeba-retry-timeout').stopTime();
            akeeba.Backup.resumeBackup();
		}
    });
};

/**
 * Resets the last response timer bar
 */
akeeba.Backup.resetRetryTimeoutBar = function()
{
    $('#akeeba-retry-timeout').stopTime();
	$('#akeeba-retry-timeout').html(akeeba.Backup.resume.timeout.toFixed(0));
};

/**
 * Renders the list of the backup steps
 *
 * @param   active_step  Which is the active step?
 */
akeeba.Backup.renderBackupSteps = function(active_step)
{
    var normal_class = 'label-success';
    if( active_step == '' ) normal_class = 'label-default';

    $('#backup-steps').html('');
    $.each(akeeba.Backup.domains, function(counter, element){
        var step = $(document.createElement('div'))
            .addClass('label')
            .html(element[1])
            .data('domain',element[0])
            .appendTo('#backup-steps');

        if(step.data('domain') == active_step )
        {
            normal_class = 'label-default';
            this_class = 'label-info';
        }
        else
        {
            this_class = normal_class;
        }

        step.addClass(this_class);
    });
};

/**
 * Start the backup
 */
akeeba.Backup.start = function()
{
    try
    {
        console.log('Starting backup');
        console.log(data);
    }
    catch(e) {
    }

    // Check for AVG Link Scanner
    if(window.AVGRUN)
    {
        try
        {
            console.warn('AVG Antivirus with Link Checker detected. The backup WILL fail!');
        }
        catch(e) {
        }


        var r = confirm(akeeba.Backup.translations['SOLO_BACKUP_AVGWARNING']);

        if (!r)
        {
            return;
        }
    }

    // Save the editor contents
    try
    {
        if (akeeba.Backup.commentEditorSave != null)
        {
            akeeba.Backup.commentEditorSave();
        }
    }
    catch(err)
    {
        // If the editor failed to save its content, just move on and ignore the error
        $('#comment').val("");
    }

    // Get encryption key (if applicable)
    var jpskey = '';

    try {
        jpskey = $('#jpskey').val();
    } catch(err) {
        jpskey = '';
    }

    var angiekey = '';

    try {
        angiekey = $('#angiekey').val();
    } catch(err) {
        angiekey = '';
    }

    // Hide the backup setup
    $('#backup-setup').hide("fast");
    // Show the backup progress
    $('#backup-progress-pane').show("fast");

    // Let's check if we have a password even if we didn't set it in the profile (maybe a password manager filled it?)
    if (angiekey && (akeeba.Backup.config_angiekey == ''))
    {
        $('#angie-password-warning').show();
    }

    // Show desktop notification
    var rightNow = new Date();
    akeeba.System.notification.notify(akeeba.Backup.translations['UI-BACKUPSTARTED'] + ' ' + rightNow.toLocaleString());

    // Initialize steps
    akeeba.Backup.renderBackupSteps('');
    // Start the response timer
    akeeba.Backup.startTimeoutBar(akeeba.Backup.maxExecutionTime, akeeba.Backup.runtimeBias);
    // Perform Ajax request
    akeeba.Backup.tag = akeeba.Backup.srpInfo.tag;

    var ajax_request = {
        // Data to send to AJAX
        'ajax': 'start',
        description: $('#backup-description').val(),
        comment: $('#comment').val(),
        jpskey: jpskey,
        angiekey: angiekey
    };

    ajax_request = array_merge(ajax_request, akeeba.Backup.srpInfo);

    akeeba.System.doAjax(ajax_request, akeeba.Backup.onStep, akeeba.Backup.onError, false);
};

/**
 * Backup step callback handler
 *
 * @param   data  Backup data received
 */
akeeba.Backup.onStep = function(data)
{
    try
    {
        console.log('Running backup step');
        console.log(data);
    }
    catch(e) {
    }

    // Update visual step progress from active domain data
    akeeba.Backup.renderBackupSteps(data.Domain);
    akeeba.Backup.currentDomain = data.Domain;

    // Update percentage display
    var percentageText = data.Progress + '%';
    $('#backup-percentage div.bar').css({
        'width':			data.Progress+'%'
    });

	// Update Piecon percentage display
	if (data.Progress >= 100)
	{
		Piecon.setProgress(99);
	}
	else
	{
		Piecon.setProgress(data.Progress);
	}

    // Update step/substep display
    $('#backup-step').html(data.Step);
    $('#backup-substep').html(data.Substep);

    // Do we have warnings?
    if( data.Warnings.length > 0 )
    {
        $('#backup-percentage').addClass('progress-warning');

        $.each(data.Warnings, function(i, warning){
            akeeba.System.notification.notify(akeeba.Backup.translations['UI-BACKUPWARNING'], warning);

            var newDiv = $(document.createElement('div'))
                .html(warning)
                .appendTo( $('#warnings-list') );
        });

        if( $('#backup-warnings-panel').is(":hidden") )
        {
            $('#backup-warnings-panel').show('fast');
        }
    }

    // Do we have errors?
    var error_message = data.Error;

    if (error_message != '')
    {
        try
        {
            console.error('Got an error message');
            console.log(error_message);
        }
        catch(e) {
        }

        // Uh-oh! An error has occurred.
        akeeba.Backup.onError(error_message);

        return;
    }
    else
    {
        // No errors. Good! Are we finished yet?
        if (data["HasRun"] == 1)
        {
            try
            {
                console.log('Backup complete');
                console.log(error_message);
            }
            catch(e) {
            }

            // Yes. Show backup completion page.
            akeeba.Backup.onDone();
        }
        else
        {
            // No. Set the backup tag
            if (empty(akeeba.Backup.tag))
            {
                akeeba.Backup.tag = 'backend';
            }

            // Set the backup id
			akeeba.Backup.backupid = data.backupid;

			// Reset the retries
			akeeba.Backup.resume.retry = 0;

			// How much time do I have to wait?
			var waitTime = 10;

			if (data.hasOwnProperty('sleepTime'))
			{
				waitTime = data.sleepTime;
			}

			// ...and send an AJAX command
			try
			{
				console.log('Starting tick timer with waitTime = ' + waitTime + ' msec');
			}
			catch(e) {
			}

			akeeba.Backup.timer(waitTime);
        }
    }
};

/**
 * Resume a backup attempt after an AJAX error has occurred.
 */
akeeba.Backup.resumeBackup = function()
{
	// Make sure the timer is stopped
	akeeba.Backup.resetRetryTimeoutBar();

	// Hide error and retry panels
	$('#error-panel').hide("fast");
	$('#retry-panel').hide("fast");

	// Show progress and warnings
	$('#backup-progress-pane').show("fast");

	if ($('#warnings-list').html())
	{
		$('#backup-warnings-panel').show("fast");
	}

    var rightNow = new Date();
    akeeba.System.notification.notify(akeeba.Backup.translations['UI-BACKUPRESUME'] + ' ' + rightNow.toLocaleString());

	// Restart the backup
	akeeba.Backup.timer();
};

/**
 * Cancel the automatic resumption of a backup attempt after an AJAX error has occurred
 */
akeeba.Backup.cancelResume = function()
{
	// Make sure the timer is stopped
	akeeba.Backup.resetRetryTimeoutBar();

	// Kill the backup
	var errorMessage = $('#backup-error-message-retry').html();
	akeeba.Backup.endWithError(errorMessage);
};

/**
 * AJAX error callback
 *
 * @param   message  The error message received
 */
akeeba.Backup.onError = function(message)
{
	// If resume is not enabled, die.
	if (!akeeba.Backup.resume.enabled)
	{
		akeeba.Backup.endWithError(message);
	}

	// If we are past the max retries, die.
	if (akeeba.Backup.resume.retry >= akeeba.Backup.resume.maxRetries)
	{
		akeeba.Backup.endWithError(message);
		return;
	}

	// Make sure the timer is stopped
	akeeba.Backup.resume.retry++;
	akeeba.Backup.resetRetryTimeoutBar();

    var resumeNotificationMessage = akeeba.Backup.translations['UI-BACKUPHALT_DESC'];
    var resumeNotificationMessageReplaced = resumeNotificationMessage.replace('%d', akeeba.Backup.resume.timeout.toFixed(0));
    akeeba.System.notification.notify(akeeba.Backup.translations['UI-BACKUPHALT'], resumeNotificationMessageReplaced);

	// Hide progress and warnings
	$('#backup-progress-pane').hide("fast");
	$('#backup-warnings-panel').hide("fast");
	$('#error-panel').hide("fast");

	// Setup and show the retry pane
	$('#backup-error-message-retry').html(message);
	$('#retry-panel').show("fast");

	// Start the countdown
	akeeba.Backup.startRetryTimeoutBar();
};

/**
 * Terminate the backup with an error
 *
 * @param   message  The error message received
 */
akeeba.Backup.endWithError = function(message)
{
    // Make sure the timer is stopped
    akeeba.Backup.resetTimeoutBar();

	try {
		Piecon.reset();
	} catch (e) {}

    var alice_autorun = false;

    // Hide progress and warnings
    $('#backup-progress-pane').hide("fast");
    $('#backup-warnings-panel').hide("fast");
	$('#retry-panel').hide("fast");

	// Set up the view log URL
	var viewLogUrl = akeeba.Backup.URLs.LogURL + '&tag=' + akeeba.Backup.tag;
    var aliceUrl   = akeeba.Backup.URLs.AliceURL + '&log=' + akeeba.Backup.tag;

	if (akeeba.Backup.backupid)
	{
		viewLogUrl = viewLogUrl + '.' + encodeURIComponent(akeeba.Backup.backupid);
        aliceUrl   = aliceUrl + '.' + encodeURIComponent(akeeba.Backup.backupid);
	}

    if(akeeba.Backup.currentDomain == 'finale')
    {
        alice_autorun = true;
        aliceUrl += '&autorun=1';
    }

	$('#ab-viewlog-error').attr('href', viewLogUrl);
	$('#ab-alice-error').attr('href', aliceUrl);

    akeeba.System.notification.notify(akeeba.Backup.translations['UI-BACKUPFAILED'], message);

    // Try to send a push notification for failed backups
    akeeba.System.doAjax({
        'ajax'	        : 'pushFail',
        'tag'	        : akeeba.Backup.tag,
        'backupid'      : akeeba.Backup.backupid,
        'errorMessage'  : message
    }, function(msg) {});

    // Setup and show error pane
    $('#backup-error-message').html(message);
    $('#error-panel').show();

    // Do we have to automatically analyze the log?
    if(alice_autorun)
    {
       setTimeout(function(){window.location = aliceUrl}, 500);
    }
};

/**
 * Backup finished callback handler
 */
akeeba.Backup.onDone = function()
{
    var rightNow = new Date();
    akeeba.System.notification.notify(akeeba.Backup.translations['UI-BACKUPFINISHED'] + ' ' + rightNow.toLocaleString());

    // Make sure the timer is stopped
    akeeba.Backup.resetTimeoutBar();

	try {
		Piecon.reset();
	} catch (e) {}

    // Hide progress
    $('#backup-progress-pane').hide("fast");

    // Show finished pane
    $('#backup-complete').show();
    $('#backup-warnings-panel').width('100%');

    // Proceed to the return URL if it is set
    if(akeeba.Backup.returnUrl != '')
    {
        // If it's the Site Transfer Wizard, show a message first
        if (akeeba.Backup.isSTW)
        {
            alert(akeeba.Backup.translations['UI-STW-CONTINUE']);
        }

        window.location = akeeba.Backup.returnUrl;
    }
};

akeeba.Backup.restoreDefaultOptions = function()
{
	$('#backup-description').val(akeeba.Backup.default_descr);

	if($('#angiekey').length)
	{
		$('#angiekey').val(akeeba.Backup.config_angiekey);
	}

	if($('#jpskey').length)
	{
		$('#jpskey').val(akeeba.Backup.jpsKey);
	}

	$('#comment').val('');
}
}(akeeba.jQuery));