<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_META'); ?></legend>

<div class="control-group">
	<div class="control-label">
		<label for="jform_metaname"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PAGE_TITLE'); ?></label>
	</div>
	<div class="controls">
		<input type="text" value="<?php echo $this->escape($this->item->metaname); ?>" class="span10" id="jform_metaname" name="jform[metaname]" />
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_keywords"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PAGE_KEYWORDS'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHtml::_('rseventspro.tags', '#jform_metakeywords', array('url' => null)); ?>
		<select id="jform_metakeywords" name="jform[metakeywords][]" class="rsepro-chosen" multiple="multiple" style="width: 500px;">
			<?php echo JHtml::_('select.options', $this->eventClass->getKeywords(), 'value', 'text', $this->eventClass->getKeywords()); ?>
		</select>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_metadescription"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_PAGE_DESCRIPTION'); ?></label>
	</div>
	<div class="controls">
		<textarea class="input-block-level" rows="5" id="jform_metadescription" name="jform[metadescription]"><?php echo $this->escape($this->item->metadescription); ?></textarea>
	</div>
</div>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
</div>