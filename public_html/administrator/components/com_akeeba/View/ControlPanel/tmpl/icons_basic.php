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
<h3><?php echo \JText::_('COM_AKEEBA_CPANEL_HEADER_BASICOPS'); ?></h3>

<?php if ($this->permissions['backup']): ?>
<div class="icon">
	<a href="index.php?option=com_akeeba&view=Backup">
		<div class="ak-icon ak-icon-backup">&nbsp;</div>
		<span><?php echo \JText::_('COM_AKEEBA_BACKUP'); ?></span>
	</a>
</div>
<?php endif; ?>

<?php if ($this->permissions['download']): ?>
<div class="icon">
	<a href="index.php?option=com_akeeba&view=Transfer">
		<div class="ak-icon ak-icon-stw">&nbsp;</div>
		<span><?php echo \JText::_('COM_AKEEBA_TRANSFER'); ?></span>
	</a>
</div>
<?php endif; ?>

<div class="icon">
	<a href="index.php?option=com_akeeba&view=Manage">
		<div class="ak-icon ak-icon-manage">&nbsp;</div>
		<span><?php echo \JText::_('COM_AKEEBA_BUADMIN'); ?></span>
	</a>
</div>

<?php if ($this->permissions['configure']): ?>
<div class="icon">
	<a href="index.php?option=com_akeeba&view=Configuration">
		<div class="ak-icon ak-icon-configuration">&nbsp;</div>
		<span><?php echo \JText::_('COM_AKEEBA_CONFIG'); ?></span>
	</a>
</div>
<?php endif; ?>

<?php if ($this->permissions['configure']): ?>
<div class="icon">
	<a href="index.php?option=com_akeeba&view=Profiles">
		<div class="ak-icon ak-icon-profiles">&nbsp;</div>
		<span><?php echo \JText::_('COM_AKEEBA_PROFILES'); ?></span>
	</a>
</div>
<?php endif; ?>

<div class="clearfix"></div>