<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
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

?>
@inlineJs($js)
<div class="akeeba-panel--success">
    <p>
        @lang('COM_AKEEBA_TRANSFER_MSG_DONE')
    </p>
</div>
