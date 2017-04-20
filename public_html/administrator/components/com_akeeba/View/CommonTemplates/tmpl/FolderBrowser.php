<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();
?>

<?php /* Filesystem browser */ ?>
<div class="modal" id="folderBrowserDialog" tabindex="-1" role="dialog" aria-labelledby="folderBrowserDialogLabel" aria-hidden="true" style="display: none;">
    <div class="modal-header">
        <h4 class="modal-title" id="folderBrowserDialogLabel">
			<?php echo \JText::_('COM_AKEEBA_CONFIG_UI_BROWSER_TITLE'); ?>
        </h4>
    </div>
    <div class="modal-body" id="folderBrowserDialogBody">
    </div>
</div>