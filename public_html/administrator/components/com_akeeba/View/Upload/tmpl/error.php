<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
<div class="akeeba-panel--failure">
	<h3>
        <?php echo \JText::_('COM_AKEEBA_TRANSFER_MSG_FAILED'); ?>
    </h3>
	<p>
		<?php echo $this->escape($this->errorMessage); ?>

	</p>
</div>
