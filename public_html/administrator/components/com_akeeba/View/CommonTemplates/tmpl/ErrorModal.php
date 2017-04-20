<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();
?>
<?php /* Error modal */ ?>
<div id="errorDialog" tabindex="-1" role="dialog" aria-labelledby="errorDialogLabel" aria-hidden="true"
     style="display:none;">
    <div class="modal-header">
        <h4 class="modal-title" id="errorDialogLabel">
			<?php echo \JText::_('COM_AKEEBA_CONFIG_UI_AJAXERRORDLG_TITLE'); ?>
        </h4>
    </div>
    <div class="modal-body" id="errorDialogBody">
        <p>
			<?php echo \JText::_('COM_AKEEBA_CONFIG_UI_AJAXERRORDLG_TEXT'); ?>
        </p>
        <pre id="errorDialogPre">
        </pre>
    </div>
</div>
