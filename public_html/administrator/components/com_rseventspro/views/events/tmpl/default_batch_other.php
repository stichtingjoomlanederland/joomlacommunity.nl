<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="row-fluid form-horizontal" style="min-height: 250px">
	<div class="control-group">
		<div class="control-label">
			<label for="location"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION'); ?></label>
		</div>
		<div class="controls">
			<select name="batch[location]" id="location" class="rsepro-chosen">
				<?php $default	= array((object) array('value' => 0, 'text' => JText::_('COM_RSEVENTSPRO_SELECT_LOCATION'))); ?>
				<?php $options	= array_merge($default, rseventsproHelper::getLocations());  ?>
				<?php echo JHtml::_('select.options', $options); ?>
			</select>
		</div>
	</div>
	
	<div class="control-group">
		<div class="control-label">
			<label for="categories"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CATEGORIES'); ?></label>
		</div>
		<div class="controls">
			<select name="batch[categories][]" id="categories" multiple="multiple" class="rsepro-chosen">
				<?php echo JHtml::_('select.options', JHtml::_('category.options','com_rseventspro', array('filter.published' => array(1)))); ?>
			</select>
		</div>
	</div>
	
	<div class="control-group">
		<div class="control-label">
			<label for="rsepro_tags"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAGS'); ?></label>
		</div>
		<div class="controls">
			<?php echo JHtml::_('rseventspro.tags', '#rsepro_tags'); ?>
			<select id="rsepro_tags" name="batch[tags][]" class="rsepro-chosen" multiple="multiple" style="width: 500px;"></select>
		</div>
	</div>
	
	<div class="control-group">
		<div class="control-label">
			<label for="itemid"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ITEMID'); ?></label>
		</div>
		<div class="controls">
			<input type="text" name="batch[itemid]" id="itemid" class="input-small" value="" />
		</div>
	</div>
	
	<div class="control-group">
		<div class="controls">
			<label class="radio" for="batch_add">
				<input type="radio" checked="checked" value="a" id="batch_add" name="batch[type]"><?php echo JText::_('COM_RSEVENTSPRO_ADD'); ?>
			</label>
			<label class="radio" for="batch_replace">
				<input type="radio" value="r" id="batch_replace" name="batch[type]"><?php echo JText::_('COM_RSEVENTSPRO_REPLACE'); ?>
			</label>
		</div>
	</div>

</div>