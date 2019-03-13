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
function closeme()
{
	parent.akeeba.Manage.uploadModal.close();
}

akeeba.System.documentReady(function(){
	window.setTimeout(closeme, 3000);
});

JS;

$this->getContainer()->template->addJSInline($js);

?>
<div class="akeeba-panel--success">
    <p>
        <?php echo \JText::_('COM_AKEEBA_TRANSFER_MSG_DONE'); ?>
    </p>
</div>
