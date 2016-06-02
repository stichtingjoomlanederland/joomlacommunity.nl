<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  \Akeeba\Backup\Admin\View\Configuration\Html  $this */
?>
<?php /* Configuration Wizard pop-up */ ?>
<?php if($this->promptForConfigurationWizard): ?>
	<?php echo $this->loadAnyTemplate('admin:com_akeeba/Configuration/confwiz_modal'); ?>
<?php endif; ?>

<?php /* Modal dialog prototypes */ ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/FTPBrowser'); ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/SFTPBrowser'); ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/FTPConnectionTest'); ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ErrorModal'); ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/FolderBrowser'); ?>

<?php if($this->securesettings == 1): ?>
<div class="alert alert-success">
	<?php echo \JText::_('COM_AKEEBA_CONFIG_UI_SETTINGS_SECURED'); ?>
</div>
<?php elseif($this->securesettings == 0): ?>
<div class="alert alert-error">
	<?php echo \JText::_('COM_AKEEBA_CONFIG_UI_SETTINGS_NOTSECURED'); ?>
</div>
<?php endif; ?>
<div class="clearfix"></div>

<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ProfileName'); ?>

<div class="alert">
	<?php echo \JText::_('COM_AKEEBA_CONFIG_WHERE_ARE_THE_FILTERS'); ?>
</div>

<form name="adminForm" id="adminForm" method="post" action="index.php" class="form-horizontal form-horizontal-wide">

<div class="well">
	<h4>
		<?php echo JText::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION') ?>
	</h4>

	<div class="control-group">
		<label class="control-label" for="profilename" rel="popover"
			data-original-title="<?php echo JText::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION') ?>"
			data-content="<?php echo JText::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION_TOOLTIP') ?>">
			<?php echo JText::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION') ?>
		</label>
		<div class="controls">
			<input type="text" name="profilename" id="profilename" value="<?php echo $this->escape($this->profilename); ?>" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="quickicon" rel="popover"
			   data-original-title="<?php echo JText::_('COM_AKEEBA_CONFIG_QUICKICON_LABEL') ?>"
			   data-content="<?php echo JText::_('COM_AKEEBA_CONFIG_QUICKICON_DESC') ?>">
			<?php echo JText::_('COM_AKEEBA_CONFIG_QUICKICON_LABEL') ?>
		</label>
		<div class="controls">
			<input type="checkbox" name="quickicon" id="quickicon" <?php echo $this->quickIcon ? 'checked="checked"' : ''; ?>/>
		</div>
	</div>
</div>

<input type="hidden" name="option" value="com_akeeba" />
<input type="hidden" name="view" value="Configuration" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />

<!-- This div contains dynamically generated user interface elements -->
<div id="akeebagui">
</div>

</form>
<script type="text/javascript" language="javascript">
	akeeba.jQuery(document).ready(function ($)
	{
		// Push some custom URLs
		akeeba.Configuration.URLs['browser']      = '<?php echo addslashes('index.php?option=com_akeeba&view=Browser&processfolder=1&tmpl=component&folder='); ?>';
		akeeba.Configuration.URLs['ftpBrowser']   = '<?php echo addslashes('index.php?option=com_akeeba&view=FTPBrowser'); ?>';
		akeeba.Configuration.URLs['sftpBrowser']  = '<?php echo addslashes('index.php?option=com_akeeba&view=SFTPBrowser'); ?>';
		akeeba.Configuration.URLs['testFtp']      = '<?php echo addslashes('index.php?option=com_akeeba&view=Configuration&task=testftp'); ?>';
		akeeba.Configuration.URLs['testSftp']     = '<?php echo addslashes('index.php?option=com_akeeba&view=Configuration&task=testsftp'); ?>';
		akeeba.Configuration.URLs['dpeauthopen']  = '<?php echo addslashes('index.php?option=com_akeeba&view=Configuration&task=dpeoauthopen&format=raw'); ?>';
		akeeba.Configuration.URLs['dpecustomapi'] = '<?php echo addslashes('index.php?option=com_akeeba&view=Configuration&task=dpecustomapi&format=raw'); ?>';
		akeeba.System.params.AjaxURL              = akeeba.Configuration.URLs['dpecustomapi'];

		// Load the configuration UI data in a timeout to prevent Safari from auto-filling the password fields
		var data = JSON.parse('<?php echo addcslashes($this->json, "'\\"); ?>');

		setTimeout(function ()
		{
			// Work around browsers which blatantly ignore autocomplete=off
			setTimeout('akeeba.Configuration.restoreDefaultPasswords();', 1000);

			// Render the configuration UI in the timeout to prevent Safari from auto-filling the password fields
			akeeba.Configuration.parseConfigData(data);

			// Enable popovers. Must obviously run after we have the UI set up.
			akeeba.Configuration.enablePopoverFor(akeeba.jQuery('[rel="popover"]'));
		}, 10);
	});
</script>