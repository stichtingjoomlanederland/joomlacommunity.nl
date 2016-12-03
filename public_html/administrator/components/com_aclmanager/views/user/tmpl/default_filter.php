<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Initialise variable
$app = JFactory::getApplication();
?>
<fieldset id="filter-bar">
	<div class="filter-search fltlft">
		<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
		<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('COM_ACLMANAGER_FILTER_SEARCH_DESC'); ?>" />
		<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
		<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</div>

	<div class="filter-select fltrt">
		<select name="filter_component" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('COM_ACLMANAGER_FILTER_COMPONENT');?></option>
			<?php echo JHtml::_('select.options', AclmanagerHelper::getComponentList(), 'value', 'text', $this->state->get('filter.component'));?>
		</select>
		<select name="filter_category" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('COM_ACLMANAGER_FILTER_CATEGORY');?></option>
			<?php echo JHtml::_('select.options', AclmanagerHelper::getYesNoList(), 'value', 'text', $this->state->get('filter.category'));?>
		</select>
		<select <?php if ($this->state->get('filter.category') == '0') : ?>disabled="disabled"<?php endif; ?> name="filter_item" class="inputbox" onchange="this.form.submit()">
			<option value="" selected="selected"><?php echo JText::_('COM_ACLMANAGER_FILTER_ITEM');?></option>
			<?php echo JHtml::_('select.options', AclmanagerHelper::getYesNoList(), 'value', 'text', $this->state->get('filter.item'));?>
		</select>
	</div>
</fieldset>

<div class="clr"> </div>