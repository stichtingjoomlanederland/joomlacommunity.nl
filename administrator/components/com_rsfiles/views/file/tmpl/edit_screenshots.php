<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access'); ?>

<div class="well">
	<div class="span8 rsspan8 rslft">
		<input type="file" name="screenshot[]" class="input-large" size="40" />
		<button type="button" class="btn button" onclick="rsf_add_files()"><?php echo JText::_('COM_RSFILES_ADD_MORE'); ?></button>
		<span id="rsf_files"></span>
	</div>
	<?php if (rsfilesHelper::gallery()) { ?>
	<div class="span4 rsspan4 rsrgt">
		<label for="jform_ScreenshotsTags"><?php echo JText::_('COM_RSFILES_GALLERY_TAGS'); ?></label>
		<select id="jform_ScreenshotsTags" name="jform[ScreenshotsTags][]" multiple="multiple" size="5">
			<?php echo JHtml::_('select.options', rsfilesHelper::getGalleryTags(),'value','text',$this->item->ScreenshotsTags); ?>
		</select>
	</div>
	<?php } ?>
	<div class="clearfix"></div>
	<div class="clr"></div>
</div>

<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th width="30%"><?php echo JText::_('COM_RSFILES_SCREENSHOT_NAME'); ?></th>
			<th width="1%" class="center" align="center"><?php echo JText::_('COM_RSFILES_DELETE'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php if (!empty($this->screenshots)) { ?>
	<?php foreach ($this->screenshots as $i => $screenshot) { ?>
		<tr class="row<?php echo $i % 2; ?>" id="screenshot<?php echo $screenshot->IdScreenshot; ?>">
			<td>
				<a href="<?php echo JURI::root(); ?>components/com_rsfiles/images/screenshots/<?php echo $screenshot->Path; ?>" class="modal" rel="{handler:'image'}">
					<?php echo $screenshot->Path; ?>
				</a>
			</td>
			<td class="center" align="center">
				<a href="javascript:void(0)" onclick="rsf_delete_screenshot(<?php echo $screenshot->IdScreenshot; ?>)">
					<img src="<?php echo JURI::root(); ?>/administrator/components/com_rsfiles/assets/images/icons/delete.png" alt="" />
				</a>
			</td>
		</tr>
	<?php } ?>
	<?php } ?>
	</tbody>
</table>