<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\Backup\Admin\View\ControlPanel\Html */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
<?php /* Configuration Wizard pop-up */ ?>
<?php if ($this->promptForConfigurationWizard): ?>
	<?php echo $this->loadAnyTemplate('admin:com_akeeba/Configuration/confwiz_modal'); ?>
<?php endif; ?>

<?php /* Stuck database updates warning */?>
<?php if ($this->stuckUpdates):?>
	<div class="alert alert-danger">
		<p>
		<?php
			echo \JText::sprintf('COM_AKEEBA_CPANEL_ERR_UPDATE_STUCK',
					$this->getContainer()->db->getPrefix(),
					'index.php?option=com_akeeba&view=ControlPanel&task=forceUpdateDb'
			)?>
		</p>
	</div>
<?php endif;?>

<?php /* mbstring warning */ ?>
<?php if ( ! ($this->checkMbstring)): ?>
	<div class="alert alert-danger">
		<?php echo \JText::sprintf('COM_AKEEBA_CPANL_ERR_MBSTRING', PHP_VERSION); ?>
	</div>
<?php endif; ?>

<?php /* Front-end backup secret word reminder */ ?>
<?php if ( ! (empty($this->frontEndSecretWordIssue))): ?>
	<div class="alert alert-danger">
		<h3><?php echo \JText::_('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_HEADER'); ?></h3>
		<p><?php echo \JText::_('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_INTRO'); ?></p>
		<p><?php echo $this->frontEndSecretWordIssue; ?></p>
		<p>
			<?php echo \JText::_('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_WHATTODO_JOOMLA'); ?>
			<?php echo \JText::sprintf('COM_AKEEBA_CPANEL_ERR_FESECRETWORD_WHATTODO_COMMON', $this->newSecretWord); ?>
		</p>
		<p>
			<a class="btn btn-success btn-large"
			   href="index.php?option=com_akeeba&view=ControlPanel&task=resetSecretWord&<?php echo $this->container->platform->getToken(true) ?>=1">
				<span class="icon icon-white icon-refresh"></span>
				<?php echo JText::_('COM_AKEEBA_CPANEL_BTN_FESECRETWORD_RESET'); ?>
			</a>
		</p>
	</div>
<?php endif; ?>

<?php /* Old PHP version reminder */ ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/ControlPanel/warning_phpversion'); ?>

<?php /* Wrong media directory permissions */ ?>
<?php if ( ! ($this->areMediaPermissionsFixed)): ?>
	<div id="notfixedperms" class="alert alert-error">
		<h3><?php echo \JText::_('COM_AKEEBA_CONTROLPANEL_WARN_WARNING'); ?></h3>
		<p><?php echo \JText::_('COM_AKEEBA_CONTROLPANEL_WARN_PERMS_L1'); ?></p>
		<p><?php echo \JText::_('COM_AKEEBA_CONTROLPANEL_WARN_PERMS_L2'); ?></p>
		<ol>
			<li><?php echo \JText::_('COM_AKEEBA_CONTROLPANEL_WARN_PERMS_L3A'); ?></li>
			<li><?php echo \JText::_('COM_AKEEBA_CONTROLPANEL_WARN_PERMS_L3B'); ?></li>
		</ol>
		<p><?php echo \JText::_('COM_AKEEBA_CONTROLPANEL_WARN_PERMS_L4'); ?></p>
	</div>
<?php endif; ?>

<?php /* You need to enter your Download ID */ ?>
<?php if($this->needsDownloadID): ?>
	<div class="alert alert-success">
		<h3>
			<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_MUSTENTERDLID') ?>
		</h3>
		<p>
			<?php echo JText::sprintf('COM_AKEEBA_LBL_CPANEL_NEEDSDLID','https://www.akeebabackup.com/instructions/1435-akeeba-backup-download-id.html'); ?>
		</p>
		<form name="dlidform" action="index.php" method="post" class="form-inline">
			<input type="hidden" name="option" value="com_akeeba" />
			<input type="hidden" name="view" value="ControlPanel" />
			<input type="hidden" name="task" value="applydlid" />
			<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />
		<span>
			<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_PASTEDLID') ?>
		</span>
			<input type="text" name="dlid" placeholder="<?php echo JText::_('COM_AKEEBA_CONFIG_DOWNLOADID_LABEL')?>" class="input-xlarge">
			<button type="submit" class="btn btn-success">
				<span class="icon icon-<?php echo version_compare(JVERSION, '3.0.0', 'ge') ? 'checkbox' : 'ok icon-white' ?>"></span>
				<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_APPLYDLID') ?>
			</button>
		</form>
	</div>
<?php endif; ?>

<?php /* You have CORE; you need to upgrade, not just enter a Download ID */ ?>
<?php if($this->coreWarningForDownloadID): ?>
	<div class="alert alert-danger">
		<?php echo JText::sprintf('COM_AKEEBA_LBL_CPANEL_NEEDSUPGRADE','https://www.akeebabackup.com/videos/1212-akeeba-backup-core/1617-abtc03-upgrade-core-professional.html'); ?>
	</div>
<?php endif; ?>

<?php
	/* Warn about CloudFlare Rocket Loader */
	$testfile  = 'CLOUDFLARE::'.$this->getContainer()->template->parsePath('media://com_akeeba/js/ControlPanel.min.js');
	$testfile .= '?'.$this->getContainer()->mediaVersion;
?>
	<div class="alert alert-error" style="display: none;" id="cloudFlareWarn">
		<h3><?php echo JText::_('COM_AKEEBA_CPANEL_MSG_CLOUDFLARE_WARN')?></h3>
		<p><?php echo JText::sprintf('COM_AKEEBA_CPANEL_MSG_CLOUDFLARE_WARN1', 'https://support.cloudflare.com/hc/en-us/articles/200169456-Why-is-JavaScript-or-jQuery-not-working-on-my-site-')?></p>
	</div>
<?php
/**
 * DO NOT USE INLINE JAVASCRIPT FOR THIS SCRIPT. DO NOT REMOVE THE ATTRIBUTES.
 *
 * This is a specialised test which looks for CloudFlare's completely broken RocketLoader feature and warns the user
 * about it.
 */
?>
	<script type="text/javascript" data-cfasync="true">
		var test = localStorage.getItem('<?php echo $testfile?>');
		if (test)
		{
			document.getElementById('cloudFlareWarn').style.display = 'block';
		}
	</script>
