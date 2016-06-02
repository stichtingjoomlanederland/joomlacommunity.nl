<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
<style type="text/css">
	dl { display: none; }
</style>

<div id="backup-percentage" class="progress">
	<div id="progressbar-inner" class="bar" style="width: <?php echo (int) $this->percent; ?>%"></div>
</div>

<div class="well">
	<?php echo \JText::sprintf('COM_AKEEBA_REMOTEFILES_LBL_DOWNLOADEDSOFAR', $this->done, $this->total, $this->percent); ?>
</div>