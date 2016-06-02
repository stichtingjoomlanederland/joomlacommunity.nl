<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
<div class="alert alert-success">
<?php echo \JText::_('COM_AKEEBA_TRANSFER_MSG_DONE'); ?>
</div>

<script type="text/javascript" language="javascript">
	window.setTimeout('closeme();', 3000);
	function closeme()
	{
		parent.SqueezeBox.close();
	}
</script>