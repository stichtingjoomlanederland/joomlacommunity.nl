<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<dialog>

	<width>400</width>
	<height>120</height>
	<selectors type="json">
	{
	    "{closeButton}" : "[data-close-button]",
	    "{form}" : "[data-form-response]",
	    "{submitButton}" : "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
	    "{closeButton} click": function() {
	        this.parent.close();
	    }
	}
	</bindings>
	<title>
	    <?php echo JText::_('COM_EASYDISCUSS_CONVERT_COMMENT_DIALOG_TITLE'); ?>
	</title>
	<content>
	<p><?php echo JText::_('COM_EASYDISCUSS_COMMENT_CONVERT_CONFIRMATION'); ?></p>
	</content>
	<buttons>
	    <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_NO'); ?></button>
	    <button data-submit-button type="button" class="btn btn-danger btn-sm">
	   
	            <?php echo JText::_('COM_EASYDISCUSS_CONVERT_COMMENT_CONVERT'); ?>
	      
	    </button>
	</buttons>
</dialog>
