<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

$js = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
function postMyForm()
{
	document.forms.akeebaform.submit();
}

akeeba.System.documentReady(function(){
	window.setTimeout(postMyForm, 1000);
});

JS;

$this->getContainer()->template->addJSInline($js);

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

<div class="akeeba-panel--information">
    <p>
	    <?php if($this->frag == 0): ?>
		    <?php echo \JText::sprintf('COM_AKEEBA_TRANSFER_MSG_UPLOADINGPART', $this->part+1, $this->parts); ?>
	    <?php else: ?>
		    <?php echo \JText::sprintf('COM_AKEEBA_TRANSFER_MSG_UPLOADINGFRAG', $this->part+1, $this->parts); ?>
	    <?php endif; ?>
    </p>
</div>
