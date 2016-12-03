<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\Backup\Admin\View\ControlPanel\Html */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
<h3><?php echo \JText::_('COM_AKEEBA_CPANEL_HEADER_TROUBLESHOOTING'); ?></h3>

<div class="icon">
	<a href="index.php?option=com_akeeba&view=Log">
		<div class="ak-icon ak-icon-viewlog">&nbsp;</div>
		<span><?php echo \JText::_('COM_AKEEBA_LOG'); ?></span>
	</a>
</div>

<?php if(AKEEBA_PRO): ?>
	<div class="icon">
		<a href="index.php?option=com_akeeba&view=Alice">
			<div class="ak-icon ak-icon-viewlog">&nbsp;</div>
			<span><?php echo \JText::_('COM_AKEEBA_ALICE'); ?></span>
		</a>
	</div>
<?php endif; ?>

<div class="clearfix"></div>