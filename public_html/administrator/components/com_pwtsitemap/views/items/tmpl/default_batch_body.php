<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;
?>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="control-group span6">
			<div class="controls">
				<label id="batch-addtohtmlsitemap-lbl" for="batch-addtohtmlsitemap" class="modalTooltip" title="<?php echo JHtml::_('tooltipText', 'COM_PWTSITEMAP_FIELD_SHOW_IN_HTML', 'COM_PWTSITEMAP_FIELD_SHOW_IN_HTML'); ?>">
					<?php echo JText::_('COM_PWTSITEMAP_FIELD_SHOW_IN_HTML'); ?>
				</label>
				<select name="batch[addtohtmlsitemap]" class="inputbox" id="batch-addtohtmlsitemap">
					<option value=""><?php echo JText::_('COM_PWTSITEMAP_BATCH_NO_CHANGE'); ?></option>
					<?php echo JHtml::_('select.options', array(array('text' => JText::_("JYES"), 'value' => "yes"), array('text' => JText::_("JNO"), 'value' => "no"))); ?>
				</select>
			</div>
		</div>
		<div class="control-group span6">
			<div class="controls">
				<label id="batch-addtoxmlsitemap-lbl" for="batch-addtoxmlsitemap" class="modalTooltip" title="<?php echo JHtml::_('tooltipText', 'COM_PWTSITEMAP_FIELD_SHOW_IN_HTML', 'COM_PWTSITEMAP_FIELD_SHOW_IN_HTML'); ?>">
					<?php echo JText::_('COM_PWTSITEMAP_FIELD_SHOW_IN_XML'); ?>
				</label>
				<select name="batch[addtoxmlsitemap]" class="inputbox" id="batch-addtoxmlsitemap">
					<option value=""><?php echo JText::_('COM_PWTSITEMAP_BATCH_NO_CHANGE'); ?></option>
					<?php echo JHtml::_('select.options', array(array('text' => JText::_("JYES"), 'value' => "yes"), array('text' => JText::_("JNO"), 'value' => "no"))); ?>
				</select>
			</div>
		</div>
	</div>
</div>