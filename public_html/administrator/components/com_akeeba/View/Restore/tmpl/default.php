<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

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
        }
        else
        {
            document.getElementById('ftpOptions').style.display = 'block';
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
	setTimeout('akeeba.Restore.restoreDefaultOptions();', 500);
});

JS;

$this->getContainer()->template->addJSInline($js);

?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/FTPBrowser'); ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/FTPConnectionTest'); ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ErrorModal'); ?>

<form name="adminForm" id="adminForm" action="index.php" method="post" class="form-horizontal">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="Restore" />
	<input type="hidden" name="task" value="start" />
	<input type="hidden" name="id" value="<?php echo (int)$this->id; ?>" />
	<input type="hidden" name="<?php echo $this->container->platform->getToken(true)?>" value="1"/>

	<fieldset>
		<legend><?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_EXTRACTIONMETHOD'); ?></legend>
		<div class="control-group">
			<label class="control-label" for="procengine">
				<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_EXTRACTIONMETHOD'); ?>
			</label>
			<div class="controls">
				<?php echo \JHtml::_('select.genericlist', $this->extractionmodes, 'procengine', '', 'value', 'text', $this->ftpparams['procengine']); ?>
				<p class="help-block">
					<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_REMOTETIP'); ?>
				</p>
			</div>
		</div>
	</fieldset>

    <?php if ($this->extension == 'jps'): ?>
	<fieldset>
		<legend><?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_JPSOPTIONS'); ?></legend>
		<div class="control-group">
			<label class="control-label" for="jps_key">
				<?php echo \JText::_('COM_AKEEBA_CONFIG_JPS_KEY_TITLE'); ?>
			</label>
			<div class="controls">
				<input id="jps_key" name="jps_key" value="" type="password" />
			</div>
		</div>
	</fieldset>
    <?php endif; ?>

    <fieldset id="ftpOptions">
		<legend><?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_FTPOPTIONS'); ?></legend>

		<div class="control-group">
			<label class="control-label" for="ftp_host">
				<?php echo \JText::_('COM_AKEEBA_CONFIG_DIRECTFTP_HOST_TITLE'); ?>
			</label>
			<div class="controls">
				<input id="ftp_host" name="" value="<?php echo $this->escape($this->ftpparams['ftp_host']); ?>" type="text" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="ftp_port">
				<?php echo \JText::_('COM_AKEEBA_CONFIG_DIRECTFTP_PORT_TITLE'); ?>
			</label>
			<div class="controls">
				<input id="ftp_port" name="ftp_port" value="<?php echo $this->escape($this->ftpparams['ftp_port']); ?>" type="text" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="ftp_user">
				<?php echo \JText::_('COM_AKEEBA_CONFIG_DIRECTFTP_USER_TITLE'); ?>
			</label>
			<div class="controls">
				<input id="ftp_user" name="ftp_user" value="<?php echo $this->escape($this->ftpparams['ftp_user']); ?>" type="text" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="ftp_pass">
				<?php echo \JText::_('COM_AKEEBA_CONFIG_DIRECTFTP_PASSWORD_TITLE'); ?>
			</label>
			<div class="controls">
				<input id="ftp_pass" name="ftp_pass" value="<?php echo $this->escape($this->ftpparams['ftp_pass']); ?>" type="password" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="ftp_root">
				<?php echo \JText::_('COM_AKEEBA_CONFIG_DIRECTFTP_INITDIR_TITLE'); ?>
			</label>
			<div class="controls">
				<input id="ftp_root" name="ftp_root" value="<?php echo $this->escape($this->ftpparams['ftp_root']); ?>" type="text" />
				<button class="btn btn-inverse btn-mini" id="ftp-browse" onclick="return false;" style="display: none;">
					<span class="icon-white icon-folder-open"></span>
					<?php echo \JText::_('COM_AKEEBA_CONFIG_UI_BROWSE'); ?>
				</button>
			</div>
		</div>
	</fieldset>

	<div class="form-actions">
		<button class="btn btn-primary btn-large" id="backup-start" onclick="return false;">
			<span class="icon-refresh icon-white"></span>
			<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_START'); ?>
		</button>
		<button class="btn" id="testftp" onclick="return false;">
			<?php echo \JText::_('COM_AKEEBA_CONFIG_DIRECTFTP_TEST_TITLE'); ?>
		</button>
	</div>

</form>