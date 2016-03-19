<?php
/**
 * @package Akeeba
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 3.6.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
    <ul id="runCheckTabs" class="nav nav-tabs">
        <li>
            <a href="#absTabRunBackups" data-toggle="tab">
                <?php echo JText::_('COM_AKEEBA_SCHEDULE_LBL_RUN_BACKUPS'); ?>
            </a>
        </li>
        <li>
            <a href="#absTabCheckBackups" data-toggle="tab">
                <?php echo JText::_('COM_AKEEBA_SCHEDULE_LBL_CHECK_BACKUPS'); ?>
            </a>
        </li>
    </ul>

    <div id="runCheckTabsContent" class="tab-content">
<?php
    echo $this->loadTemplate('runbackups');
    echo $this->loadTemplate('checkbackups');
?>
    </div>
<?php
JFactory::getDocument()->addScriptDeclaration( <<<JS

	;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
	// due to missing trailing semicolon and/or newline in their code.
    (function($) {
        $(document).ready(function(){
            $('#runCheckTabs a:first').tab('show');
        });
    })(akeeba.jQuery);

JS
);
?>