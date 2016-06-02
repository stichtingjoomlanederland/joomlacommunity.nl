<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
<div class="alert alert-error">
	<h4><?php echo \JText::_('COM_AKEEBA_TRANSFER_MSG_FAILED'); ?></h4>
	<p>
		<?php echo $this->escape($this->errorMessage); ?>

	</p>
</div>