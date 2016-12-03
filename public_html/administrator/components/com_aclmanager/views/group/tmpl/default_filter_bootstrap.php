<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;
JHtml::_('formbehavior.chosen', 'select');

// Initialise variable
$app = JFactory::getApplication();
?>
<fieldset id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group pull-left">
		<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL');?></label>
		<input type="text" name="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>" title="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" />
	</div>
	<div class="btn-group pull-left hidden-phone">
		<button class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
		<button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
	</div>

	<div class="btn-group pull-right hidden-phone">
		<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>

	<div class="btn-group pull-right">
		<select <?php if ($this->state->get('filter.category') == '0') : ?>disabled="disabled"<?php endif; ?> name="filter_item" class="input-medium" onchange="this.form.submit()">
			<option value="" selected="selected"><?php echo JText::_('COM_ACLMANAGER_FILTER_ITEM');?></option>
			<?php echo JHtml::_('select.options', AclmanagerHelper::getYesNoList(), 'value', 'text', $this->state->get('filter.item'));?>
		</select>
	</div>

	<div class="btn-group pull-right">
		<select name="filter_category" class="input-medium" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('COM_ACLMANAGER_FILTER_CATEGORY');?></option>
			<?php echo JHtml::_('select.options', AclmanagerHelper::getYesNoList(), 'value', 'text', $this->state->get('filter.category'));?>
		</select>
	</div>

	<div class="btn-group pull-right">
		<select name="filter_component" class="input-large" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('COM_ACLMANAGER_FILTER_COMPONENT');?></option>
			<?php echo JHtml::_('select.options', AclmanagerHelper::getComponentList(), 'value', 'text', $this->state->get('filter.component'));?>
		</select>
	</div>

</fieldset>

<div class="clr"> </div>