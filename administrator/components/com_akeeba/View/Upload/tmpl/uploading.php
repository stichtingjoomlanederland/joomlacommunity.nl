<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
<form action="index.php" method="get" name="akeebaform">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="Upload" />
	<input type="hidden" name="task" value="upload" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo (int) $this->id; ?>" />
	<input type="hidden" name="part" value="<?php echo (int)$this->part; ?>" />
	<input type="hidden" name="frag" value="<?php echo (int)$this->frag; ?>" />
</form>

<?php if($this->frag == 0): ?>
<p class="well">
	<?php echo \JText::sprintf('COM_AKEEBA_TRANSFER_MSG_UPLOADINGPART', $this->part+1, $this->parts); ?>
</p>
<?php else: ?>
<p class="well">
	<?php echo \JText::sprintf('COM_AKEEBA_TRANSFER_MSG_UPLOADINGFRAG', $this->part+1, $this->parts); ?>
</p>
<?php endif; ?>

<script type="text/javascript" language="javascript">
	window.setTimeout('postMyForm();', 1000);
	function postMyForm()
	{
		document.forms.akeebaform.submit();
	}
</script>