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
<h3><?php echo \JText::_('COM_AKEEBA_CPANEL_HEADER_INCLUDEEXCLUDE'); ?></h3>

<?php if(AKEEBA_PRO): ?>
	<div class="icon">
		<a href="index.php?option=com_akeeba&view=MultipleDatabases">
			<div class="ak-icon ak-icon-multidb">&nbsp;</div>
			<span><?php echo \JText::_('COM_AKEEBA_MULTIDB'); ?></span>
		</a>
	</div>

	<div class="icon">
		<a href="index.php?option=com_akeeba&view=IncludeFolders">
			<div class="ak-icon ak-icon-extradirs">&nbsp;</div>
			<span><?php echo \JText::_('COM_AKEEBA_INCLUDEFOLDER'); ?></span>
		</a>
	</div>
<?php endif; ?>

<div class="icon">
	<a href="index.php?option=com_akeeba&view=FileFilters">
		<div class="ak-icon ak-icon-fsfilter">&nbsp;</div>
		<span><?php echo \JText::_('COM_AKEEBA_FILEFILTERS'); ?></span>
	</a>
</div>

<div class="icon">
	<a href="index.php?option=com_akeeba&view=DatabaseFilters">
		<div class="ak-icon ak-icon-dbfilter">&nbsp;</div>
		<span><?php echo \JText::_('COM_AKEEBA_DBFILTER'); ?></span>
	</a>
</div>

<?php if(AKEEBA_PRO): ?>
	<div class="icon">
		<a href="index.php?option=com_akeeba&view=RegExFileFilters">
			<div class="ak-icon ak-icon-regexfiles">&nbsp;</div>
			<span><?php echo \JText::_('COM_AKEEBA_REGEXFSFILTERS'); ?></span>
		</a>
	</div>

	<div class="icon">
		<a href="index.php?option=com_akeeba&view=RegExDatabaseFilters">
			<div class="ak-icon ak-icon-regexdb">&nbsp;</div>
			<span><?php echo \JText::_('COM_AKEEBA_REGEXDBFILTERS'); ?></span>
		</a>
	</div>
<?php endif; ?>

<div class="clearfix"></div>