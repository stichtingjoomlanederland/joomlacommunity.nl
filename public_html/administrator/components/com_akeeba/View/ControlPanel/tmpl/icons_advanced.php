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
<h3><?php echo \JText::_('COM_AKEEBA_CPANEL_HEADER_ADVANCED'); ?></h3>

<?php if ($this->permissions['configure']): ?>
<div class="icon">
	<a href="index.php?option=com_akeeba&view=Schedule">
		<div class="ak-icon ak-icon-scheduling">&nbsp;</div>
		<span><?php echo \JText::_('COM_AKEEBA_SCHEDULE'); ?></span>
	</a>
</div>
<?php endif; ?>

<?php if(AKEEBA_PRO): ?>
	<?php if ($this->permissions['configure']): ?>
	<div class="icon">
		<a href="index.php?option=com_akeeba&view=Discover">
			<div class="ak-icon ak-icon-import">&nbsp;</div>
			<span><?php echo \JText::_('COM_AKEEBA_DISCOVER'); ?></span>
		</a>
	</div>
    <?php endif; ?>

    <?php if ($this->permissions['configure']): ?>
	<div class="icon">
		<a href="index.php?option=com_akeeba&view=S3Import">
			<div class="ak-icon ak-icon-s3import">&nbsp;</div>
			<span><?php echo \JText::_('COM_AKEEBA_S3IMPORT'); ?></span>
		</a>
	</div>
    <?php endif; ?>
<?php endif; ?>

<div class="clearfix"></div>