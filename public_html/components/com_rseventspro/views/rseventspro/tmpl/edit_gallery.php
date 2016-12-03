<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_TAB_GALLERY'); ?></legend>

<div class="control-group">
	<div class="control-label">
		<label for="jform_gallery_tags"><?php echo JText::_('COM_RSEVENTSPRO_GALLERY_TAGS'); ?></label>
	</div>
	<div class="controls">
		<select class="rsepro-chosen" name="jform[gallery_tags][]" id="jform_gallery_tags" multiple="multiple">
			<?php echo JHtml::_('select.options', rseventsproHelper::getGalleryTags(), 'value','text',$this->eventClass->getSelectedGalleryTags()); ?>
		</select>
	</div>
</div>
<div class="clearfix"></div>
<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
</div>