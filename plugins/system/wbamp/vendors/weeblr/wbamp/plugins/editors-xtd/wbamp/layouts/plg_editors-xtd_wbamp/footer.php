<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;

?>
<div class="wbl-theme-default shmodal-footer">
	<div class="shmodal-toolbar-buttons">
		<button class="btn btn-success pull-left" id="wbamp-editor-help-button" data-helpid=""
		        onclick="wblib.wbampeditor.showHelp();"><?php echo JText::_('JHELP'); ?></button>
		<button class="btn btn-primary hide" type="button" id="wbamp-editor-insert-tag-button"
		        onclick="wblib.wbampeditor.insertTag(jQuery('#wbamp-option-select').val())"><?php echo JText::_('PLG_EDITORS_XTD_WBAMP_BUTTON_INSERT'); ?></button>
		<button class="btn" data-dismiss="modal"
		        ><?php echo JText::_('PLG_EDITORS_XTD_WBAMP_BUTTON_CLOSE'); ?></button>
	</div>
</div>
