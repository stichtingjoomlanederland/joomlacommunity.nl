<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var \Akeeba\Backup\Admin\View\Restore\Html $this */

$urlBrowser = addslashes('index.php?view=Browser&tmpl=component&processfolder=1&folder=');
$urlFtpBrowser = addslashes('index.php?option=com_akeeba&view=FTPBrowser');
$urlTestFtp = addslashes('index.php?option=com_akeeba&view=Restore&task=ajax&ajax=testftp');
$js = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
akeeba.System.documentReady(function() {
    // Push some custom URLs
    akeeba.Configuration.URLs['browser'] = '$urlBrowser';
    akeeba.Configuration.URLs['ftpBrowser'] = '$urlFtpBrowser';
    akeeba.Configuration.URLs['testFtp'] = '$urlTestFtp';

	akeeba.System.addEventListener(document.getElementById('backup-start'), 'click', function(event){
		document.adminForm.submit();
	});

    // Button hooks
    function onProcEngineChange(e)
    {
    	var elProcEngine = document.getElementById('procengine');

	    if (elProcEngine.options[elProcEngine.selectedIndex].value == 'direct')
        {
            document.getElementById('ftpOptions').style.display = 'none';
            document.getElementById('testftp').style.display = 'none';
        }
        else
        {
            document.getElementById('ftpOptions').style.display = 'block';
            document.getElementById('testftp').style.display = 'inline-block';
        }
    }

    akeeba.System.addEventListener(document.getElementById('ftp-browse'), 'click', function(){
	    akeeba.Configuration.FtpBrowser.initialise('ftp.initial_directory', 'ftp')
    });

	akeeba.System.addEventListener(document.getElementById('testftp'), 'click', function(){
		akeeba.Configuration.FtpTest.testConnection('testftp', 'ftp');
	});

	akeeba.System.addEventListener(document.getElementById('procengine'), 'change', onProcEngineChange);

    onProcEngineChange();

	// Work around Safari which ignores autocomplete=off
	setTimeout(akeeba.Restore.restoreDefaultOptions, 500);
});

JS;

?>
@inlineJs($js)
@include('admin:com_akeeba/CommonTemplates/FTPBrowser')
@include('admin:com_akeeba/CommonTemplates/FTPConnectionTest')
@include('admin:com_akeeba/CommonTemplates/ErrorModal')

<form name="adminForm" id="adminForm" action="index.php" method="post" class="akeeba-form--horizontal">
    <input type="hidden" name="option" value="com_akeeba" />
    <input type="hidden" name="view" value="Restore" />
    <input type="hidden" name="task" value="start" />
    <input type="hidden" name="id" value="{{ (int)$this->id }}" />
    <input type="hidden" name="@token(true)" value="1" />

    <h4>
        @lang('COM_AKEEBA_RESTORE_LABEL_EXTRACTIONMETHOD')
    </h4>

    <div class="akeeba-form-group">
        <label for="procengine">
            @lang('COM_AKEEBA_RESTORE_LABEL_EXTRACTIONMETHOD')
        </label>
        @jhtml('select.genericlist', $this->extractionmodes, 'procengine', '', 'value', 'text', $this->ftpparams['procengine'])
        <p class="akeeba-help-text">
            @lang('COM_AKEEBA_RESTORE_LABEL_REMOTETIP')
        </p>
    </div>

    @if($this->container->params->get('showDeleteOnRestore', 0) == 1)
        <div class="akeeba-form-group">
            <label for="zapbefore">
                @lang('COM_AKEEBA_RESTORE_LABEL_ZAPBEFORE')
            </label>
            @jhtml('FEFHelper.select.booleanswitch', 'zapbefore', 0)
            <p class="akeeba-help-text">
                @lang('COM_AKEEBA_RESTORE_LABEL_ZAPBEFORE_HELP')
            </p>
        </div>
    @endif

    @if($this->extension == 'jps')
        <h4>
            @lang('COM_AKEEBA_RESTORE_LABEL_JPSOPTIONS')
        </h4>

        <div class="akeeba-form-group">
            <label for="jps_key">
                @lang('COM_AKEEBA_CONFIG_JPS_KEY_TITLE')
            </label>
            <input id="jps_key" name="jps_key" value="" type="password" />
        </div>
    @endif

    <div id="ftpOptions">
        <h4>@lang('COM_AKEEBA_RESTORE_LABEL_FTPOPTIONS')</h4>

        <div class="akeeba-form-group">
            <label for="ftp_host">
                @lang('COM_AKEEBA_CONFIG_DIRECTFTP_HOST_TITLE')
            </label>
            <input id="ftp_host" name="" value="{{{ $this->ftpparams['ftp_host'] }}}" type="text" />
        </div>
        <div class="akeeba-form-group">
            <label for="ftp_port">
                @lang('COM_AKEEBA_CONFIG_DIRECTFTP_PORT_TITLE')
            </label>
            <input id="ftp_port" name="ftp_port" value="{{{ $this->ftpparams['ftp_port'] }}}" type="text" />
        </div>
        <div class="akeeba-form-group">
            <label for="ftp_user">
                @lang('COM_AKEEBA_CONFIG_DIRECTFTP_USER_TITLE')
            </label>
            <input id="ftp_user" name="ftp_user" value="{{{ $this->ftpparams['ftp_user'] }}}" type="text" />
        </div>
        <div class="akeeba-form-group">
            <label for="ftp_pass">
                @lang('COM_AKEEBA_CONFIG_DIRECTFTP_PASSWORD_TITLE')
            </label>
            <input id="ftp_pass" name="ftp_pass" value="{{{ $this->ftpparams['ftp_pass'] }}}" type="password" />
        </div>
        <div class="akeeba-form-group">
            <label for="ftp_root">
                @lang('COM_AKEEBA_CONFIG_DIRECTFTP_INITDIR_TITLE')
            </label>
            <input id="ftp_root" name="ftp_root" value="{{{ $this->ftpparams['ftp_root'] }}}" type="text" />
            <div class="akeXXeba-input-group">
                <div class="akeXXeba-input-group-btn" style="display: none;">
                    <button class="akeeba-btn--dark" id="ftp-browse" onclick="return false;">
                        <span class="akion-folder"></span>
                        @lang('COM_AKEEBA_CONFIG_UI_BROWSE')
                    </button>
                </div>
            </div>
        </div>
    </div>

    <h4>@lang('COM_AKEEBA_RESTORE_LABEL_TIME_HEAD')</h4>

    <div class="akeeba-form-group">
        <label for="min_exec">
            @lang('COM_AKEEBA_RESTORE_LABEL_MIN_EXEC')
        </label>
        <input type="number" min="0" max="180" name="min_exec"
               value="<?= $this->getModel()->getState('min_exec', 0, 'int') ?>" />
        <p class="akeeba-help-text">
            @lang('COM_AKEEBA_RESTORE_LABEL_MIN_EXEC_TIP')
        </p>
    </div>
    <div class="akeeba-form-group">
        <label for="max_exec">
            @lang('COM_AKEEBA_RESTORE_LABEL_MAX_EXEC')
        </label>
        <input type="number" min="0" max="180" name="max_exec"
               value="{{ $this->getModel()->getState('max_exec', 5, 'int') }}" />
        <p class="akeeba-help-text">
            @lang('COM_AKEEBA_RESTORE_LABEL_MAX_EXEC_TIP')
        </p>
    </div>

    <hr />

    <div class="akeeba-form-group--pull-right">
        <div class="akeeba-form-group--actions">
            <button class="akeeba-btn--primary" id="backup-start" onclick="return false;">
                <span class="akion-refresh"></span>
                @lang('COM_AKEEBA_RESTORE_LABEL_START')
            </button>
            <button class="akeeba-btn--grey" id="testftp" onclick="return false;">
                <span class="akion-ios-pulse-strong"></span>
                @lang('COM_AKEEBA_CONFIG_DIRECTFTP_TEST_TITLE')
            </button>
        </div>
    </div>

</form>
