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

if (typeof akeeba.Wizard == 'undefined')
{
    akeeba.Wizard = {
        URLs: {},
        execTimes: [30,25,20,14,7,5,3],
        blockSizes: [240, 200, 160, 80, 40, 16, 4, 2, 1],
        translation: {}
    }
}

(function($){

/**
 * Boot up the Configuration Wizard benchmarking process
 */
akeeba.Wizard.boot = function()
{
    akeeba.Wizard.execTimes = [30,25,20,14,7,5,3];
    akeeba.Wizard.blockSizes = [480, 400, 240, 200, 160, 80, 40, 16, 4, 2, 1];

    // Show GUI
    $('#backup-progress-pane').css('display','block');
    akeeba.Backup.resetTimeoutBar();

    akeeba.Wizard.tryAjax();
};

/**
 * Try to figure out the optimal AJAX method
 */
akeeba.Wizard.tryAjax = function()
{
    akeeba.System.useIFrame = false;
    akeeba.Backup.resetTimeoutBar();
    akeeba.Backup.startTimeoutBar(10000, 100);

    $('#step-ajax').addClass('step-active');
    $('#backup-substep').text( akeeba.Wizard.translation['UI-TRYAJAX'] );

    akeeba.System.doAjax(
        {act: 'ping'},
        function() {
            // Successful AJAX call!
            akeeba.System.useIFrame = false;

            $('#step-ajax').removeClass('label-info');
            $('#step-ajax').addClass('label-success');

            akeeba.Wizard.minExec();
        },
        function() {
            // Let's try IFRAME
            akeeba.System.useIFrame = true;
            akeeba.Backup.resetTimeoutBar();
            akeeba.Backup.startTimeoutBar(10000, 100);
            $('#backup-substep').text( akeeba.Wizard.translation['UI-TRYIFRAME'] );
            akeeba.System.doAjax(
                { act: 'ping' },
                function() {
                    // Successful IFRAME call
                    $('#step-ajax').removeClass('label-info');
                    $('#step-ajax').addClass('label-success');
                    akeeba.Wizard.minExec();
                },
                function() {
                    // Unsuccessful IFRAME call, we've ran out if ideas!
                    $('#backup-progress-pane').css('display','none');
                    $('#error-panel').css('display','block');
                    $('#backup-error-message').html( akeeba.Wizard.translation['UI-CANTUSEAJAX'] );
                },
                false,
                10000
            );
        },
        false,
        10000
    );
};

/**
 * Determine the optimal Minimum Execution Time
 *
 * @param   seconds     How many seconds to test
 * @param   repetition  Which try is this?
 */
akeeba.Wizard.minExec = function(seconds, repetition)
{
    if(seconds == null) seconds = 0;
    if(repetition == null) repetition = 0;

    akeeba.Backup.resetTimeoutBar();
    akeeba.Backup.startTimeoutBar((2 * seconds + 5) * 1000, 100);

    var substepText = akeeba.Wizard.translation['UI-MINEXECTRY'].replace('%s', seconds.toFixed(1));

    $('#backup-substep').text( substepText );
    $('#step-minexec').addClass('label-info');

    akeeba.System.doAjax(
        {act: 'minexec', 'seconds': seconds},
        function(msg) {
            // The ping was successful. Add a repetition count.
            repetition++;
            if(repetition < 3) {
                // We need more repetitions
                akeeba.Wizard.minExec(seconds, repetition);
            } else {
                // Three repetitions reached. Success!
                akeeba.Wizard.minExecApply(seconds);
            }
        },
        function() {
            // We got a failure. Add half a second
            seconds += 0.5;

            if(seconds > 20)
            {
                // Uh-oh... We exceeded our maximum allowance!
                $('#backup-progress-pane').css('display','none');
                $('#error-panel').css('display','block');
                $('#backup-error-message').html( akeeba.Wizard.translation['UI-CANTDETERMINEMINEXEC'] );
            } else {
                akeeba.Wizard.minExec(seconds,0);
            }
        },
        false,
        (2 * seconds + 5) * 1000
    );
};

/**
 * Applies the AJAX preference and the minimum execution time determined in the previous steps
 *
 * @param   seconds  The minimum execution time, in seconds
 */
akeeba.Wizard.minExecApply = function(seconds)
{
    akeeba.Backup.resetTimeoutBar();
    akeeba.Backup.startTimeoutBar(25000,100);

    $('#backup-substep').text( akeeba.Wizard.translation['UI-SAVEMINEXEC'] );

    var iframe_opt = 0;

    if(akeeba.Backup.useIFrame) iframe_opt = 1;

    akeeba.System.doAjax(
        {act: 'applyminexec', 'iframes': iframe_opt, 'minexec': seconds},
        function(msg) {
            $('#step-minexec').removeClass('label-info');
            $('#step-minexec').addClass('label-success');

            akeeba.Wizard.directories();
        },
        function() {
            // Unsuccessful call. Oops!
            $('#backup-progress-pane').css('display','none');
            $('#error-panel').css('display','block');
            $('#backup-error-message').html( akeeba.Wizard.translation['UI-CANTSAVEMINEXEC'] );
        },
        false
    );
};

/**
 * Automatically determine the optimal output and temporary directories,
 * then make sure they are writable
 */
akeeba.Wizard.directories = function()
{
    akeeba.Backup.resetTimeoutBar();
    akeeba.Backup.startTimeoutBar(10000,100);

    $('#backup-substep').text( '' );
    $('#step-directory').addClass('label-info');

    akeeba.System.doAjax(
        {act: 'directories'},
        function(msg) {
            if(msg) {
                $('#step-directory').removeClass('label-info');
                $('#step-directory').addClass('label-success');

                akeeba.Wizard.database();
            } else {
                $('#backup-progress-pane').css('display','none');
                $('#error-panel').css('display','block');
                $('#backup-error-message').html( akeeba.Wizard.translation['UI-CANTFIXDIRECTORIES'] );
            }
        },
        function() {
            $('#backup-progress-pane').css('display','none');
            $('#error-panel').css('display','block');
            $('#backup-error-message').html( akeeba.Wizard.translation['UI-CANTFIXDIRECTORIES'] );
        },
        false
    );
};

/**
 * Determine the optimal database dump options, analyzing the site's database
 */
akeeba.Wizard.database = function()
{
    akeeba.Backup.resetTimeoutBar();
    akeeba.Backup.startTimeoutBar(30000,50);

    $('#backup-substep').text( '' );
    $('#step-dbopt').addClass('label-info');

    akeeba.System.doAjax(
        {act: 'database'},
        function(msg) {
            if(msg) {
                $('#step-dbopt').removeClass('label-info');
                $('#step-dbopt').addClass('label-success');

                akeeba.Wizard.maxExec();
            } else {
                $('#backup-progress-pane').css('display','none');
                $('#error-panel').css('display','block');
                $('#backup-error-message').html( akeeba.Wizard.translation['UI-CANTDBOPT'] );
            }
        },
        function() {
            $('#backup-progress-pane').css('display','none');
            $('#error-panel').css('display','block');
            $('#backup-error-message').html( akeeba.Wizard.translation['UI-CANTDBOPT'] );
        },
        false
    );
};

/**
 * Determine the optimal maximum execution time which doesn't cause a timeout or server error
 */
akeeba.Wizard.maxExec = function()
{
    var exec_time = array_shift(akeeba.Wizard.execTimes);

    if(empty(akeeba.Wizard.execTimes) || (exec_time == null)) {
        // Darn, we ran out of options
        $('#backup-progress-pane').css('display','none');
        $('#error-panel').css('display','block');
        $('#backup-error-message').html( akeeba.Wizard.translation['UI-EXECTOOLOW'] );

        return;
    }

    akeeba.Backup.resetTimeoutBar();
    akeeba.Backup.startTimeoutBar((exec_time * 1.2)*1000, 80);

    $('#step-maxexec').addClass('label-info');

    var substepText = akeeba.Wizard.translation['UI-MINEXECTRY'].replace('%s', exec_time.toFixed(0));

    $('#backup-substep').text( substepText );

    akeeba.System.doAjax(
        {act:'maxexec', 'seconds': exec_time},
        function(msg){
            if(msg) {
                // Success! Save this value.
                akeeba.Wizard.maxExecApply(exec_time);
            } else {
                // Uh... we have to try something lower than that
                akeeba.Wizard.maxExec();
            }
        },
        function(){
            // Uh... we have to try something lower than that
            akeeba.Wizard.maxExec();
        },
		false,
		38000 // Maximum time to wait: 38 seconds
    );
};

/**
 * Apply the maximum execution time
 *
 * @param   seconds  The number of max execution time (in seconds) we found that works on the server
 */
akeeba.Wizard.maxExecApply = function(seconds)
{
    akeeba.Backup.resetTimeoutBar();
    akeeba.Backup.startTimeoutBar(10000,100);

    $('#backup-substep').text( akeeba.Wizard.translation['UI-SAVINGMAXEXEC'] );

    akeeba.System.doAjax(
        {act: 'applymaxexec', 'seconds': seconds},
        function() {
            $('#step-maxexec').removeClass('label-info');
            $('#step-maxexec').addClass('label-success');
            akeeba.Wizard.partSize();
        },
        function() {
            $('#backup-progress-pane').css('display','none');
            $('#error-panel').css('display','block');
            $('#backup-error-message').html( akeeba.Wizard.translation['UI-CANTSAVEMAXEXEC'] );
        }
    );
};

/**
 * Try to find the best part size for split archives which works on this server
 */
akeeba.Wizard.partSize = function()
{
    akeeba.Backup.resetTimeoutBar();

    var block_size = array_shift(akeeba.Wizard.blockSizes);
    if(empty(akeeba.Wizard.blockSizes) || (block_size == null) ) {
        // Uh... I think you are running out of disk space, dude
        $('#backup-progress-pane').css('display','none');
        $('#error-panel').css('display','block');
        $('#backup-error-message').html( akeeba.Wizard.translation['UI-CANTDETERMINEPARTSIZE'] );

        return;
    }

    var part_size = block_size / 8; // Translate to Mb

    akeeba.Backup.startTimeoutBar(30000,100);
    var substepText = akeeba.Wizard.translation['UI-PARTSIZE'].replace('%s', part_size.toFixed(3));
    $('#backup-substep').text( substepText );

    $('#step-splitsize').addClass('label-info');

    akeeba.System.doAjax(
        {act: 'partsize', blocks: block_size},
        function(msg) {
            if(msg) {
                // We are done
                $('#step-splitsize').removeClass('label-info');
                $('#step-splitsize').addClass('label-success');

                akeeba.Wizard.done();
            } else {
                // Let's try the next (lower) value
                akeeba.Wizard.partSize();
            }
        },
        function(msg) {
            // The server blew up on our face. Let's try the next (lower) value.
            akeeba.Wizard.partSize();
        },
        false,
        60000
    );
};

/**
 * The configuration wizard is done
 */
akeeba.Wizard.done = function()
{
    $('#backup-progress-pane').hide();
    $('#backup-complete').show();
};

}(akeeba.jQuery));