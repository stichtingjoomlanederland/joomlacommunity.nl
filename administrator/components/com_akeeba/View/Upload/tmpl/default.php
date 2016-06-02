<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
<form action="index.php" method="get" name="akeebaform" id="akeebaform">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="Upload" />
	<input type="hidden" name="task" value="upload" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo (int) $this->id; ?>" />
	<input type="hidden" name="part" value="0" />
	<input type="hidden" name="frag" value="0" />
</form>

<p class="well">
	<?php echo \JText::_('COM_AKEEBA_TRANSFER_MSG_START'); ?>
</p>

<script type="text/javascript" language="javascript">
		document.forms.akeebaform.submit();
</script>