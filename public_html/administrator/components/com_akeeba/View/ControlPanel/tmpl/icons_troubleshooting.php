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
<h3><?php echo \JText::_('COM_AKEEBA_CPANEL_HEADER_TROUBLESHOOTING'); ?></h3>

<?php if ($this->permissions['backup']): ?>
<div class="icon">
	<a href="index.php?option=com_akeeba&view=Log">
		<div class="ak-icon ak-icon-viewlog">&nbsp;</div>
		<span><?php echo \JText::_('COM_AKEEBA_LOG'); ?></span>
	</a>
</div>
<?php endif; ?>

<?php if (AKEEBA_PRO && $this->permissions['configure']): ?>
	<div class="icon">
		<a href="index.php?option=com_akeeba&view=Alice">
			<div class="ak-icon ak-icon-viewlog">&nbsp;</div>
			<span><?php echo \JText::_('COM_AKEEBA_ALICE'); ?></span>
		</a>
	</div>
<?php endif; ?>

<div class="clearfix"></div>