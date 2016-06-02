<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  $this  \Akeeba\Backup\Admin\View\Transfer\Html */
?>

<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/FTPBrowser'); ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/SFTPBrowser'); ?>

<?php echo $this->loadAnyTemplate('admin:com_akeeba/transfer/default_prerequisites'); ?>

<?php if ( ! (empty($this->latestBackup))): ?>
	<?php echo $this->loadAnyTemplate('admin:com_akeeba/transfer/default_remoteconnection'); ?>
	<?php echo $this->loadAnyTemplate('admin:com_akeeba/transfer/default_manualtransfer'); ?>
	<?php echo $this->loadAnyTemplate('admin:com_akeeba/transfer/default_upload'); ?>
<?php endif; ?>