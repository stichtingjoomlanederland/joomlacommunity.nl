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
<h3><?php echo \JText::_('COM_AKEEBA_CPANEL_HEADER_QUICKBACKUP'); ?></h3>

<?php foreach($this->quickIconProfiles as $qiProfile): ?>
	<div class="icon">
		<a href="index.php?option=com_akeeba&view=Backup&autostart=1&profileid=<?php echo (int) $qiProfile->id; ?>&<?php echo $this->container->platform->getToken(true); ?>=1">
			<div class="ak-icon ak-icon-backup">&nbsp;</div>
			<span><?php echo $this->escape($qiProfile->description); ?></span>
		</a>
	</div>
<?php endforeach; ?>

<div class="clearfix"></div>