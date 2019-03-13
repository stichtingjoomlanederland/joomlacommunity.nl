<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  \Akeeba\Backup\Admin\View\RemoteFiles\Html  $this */
?>
<div id="backup-percentage" class="akeeba-progress">
	<div id="progressbar-inner" class="akeeba-progress-fill" style="width: <?php echo $this->percent ?>%"></div>
</div>

<div class="akeeba-panel--information">
	<?php echo \JText::sprintf('COM_AKEEBA_REMOTEFILES_LBL_DOWNLOADEDSOFAR', $this->done, $this->total, $this->percent); ?>
</div>

<form action="index.php" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="RemoteFiles" />
	<input type="hidden" name="task" value="dltoserver" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo (int)$this->id; ?>" />
	<input type="hidden" name="part" value="<?php echo (int)$this->part; ?>" />
	<input type="hidden" name="frag" value="<?php echo (int)$this->frag; ?>" />
</form>
