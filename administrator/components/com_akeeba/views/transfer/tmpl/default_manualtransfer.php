<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  $this  AkeebaViewTransfer */

$this->loadHelper('utils');

$dotPos = strrpos($this->latestBackup['archivename'], '.');
$extension = substr($this->latestBackup['archivename'], $dotPos + 1);
$bareName = basename($this->latestBackup['archivename'], '.' . $extension);

?>
<fieldset id="akeeba-transfer-manualtransfer" style="display: none;">
	<legend>
		<?php echo JText::_('COM_AKEEBA_TRANSFER_HEAD_MANUALTRANSFER'); ?>
	</legend>

	<div class="alert alert-info">
		<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_MANUALTRANSFER_INFO'); ?>
	</div>

	<p style="text-align: center">
		<iframe width="640" height="480" src="https://www.youtube.com/embed/vo_r0r6cZNQ" frameborder="0" allowfullscreen></iframe>
	</p>

	<h3><?php echo JText::_('COM_AKEEBA_BUADMIN_LBL_BACKUPINFO') ?></h3>

	<p>
		<strong><?php echo JText::_('COM_AKEEBA_BUADMIN_LBL_ARCHIVENAME') ?></strong>
		<br/>
		<?php if ($this->latestBackup['multipart'] < 2): ?>
			<?php echo htmlentities($this->latestBackup['archivename']) ?>
		<?php else: ?>
		<?php echo JText::sprintf('COM_AKEEBA_TRANSFER_LBL_MANUALTRANSFER_MULTIPART', $this->latestBackup['multipart']); ?>
		<ul>
			<?php for ($i = 1; $i < $this->latestBackup['multipart']; $i++): ?>
				<li><?php echo htmlentities($bareName . '.' . substr($extension, 0, 1) . sprintf('%02u', $i)); ?></li>
			<?php endfor; ?>
			<li>
				<?php echo htmlentities($this->latestBackup['archivename']); ?>
			</li>
		</ul>
		<?php endif; ?>
	</p>

	<p>
		<strong><?php echo JText::_('COM_AKEEBA_BUADMIN_LBL_ARCHIVEPATH') ?></strong>
		<br/>
		<?php echo htmlentities(AkeebaHelperUtils::getRelativePath(JPATH_SITE, dirname($this->latestBackup['absolute_path']))) ?>
	</p>
</fieldset>