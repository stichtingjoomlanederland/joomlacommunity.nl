<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive'); 
JText::script('COM_RSCOMMENTS_EDIT');
JText::script('COM_RSCOMMENTS_DELETE');
JText::script('COM_RSCOMMENTS_SAVE');
JText::script('COM_RSCOMMENTS_CANCEL');
JText::script('COM_RSCOMMENTS_EMOTICONS_EMPTY_VALUES'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=emoticons'); ?>" method="post" name="adminForm" id="adminForm">
	
	<?php echo RSCommentsAdapterGrid::sidebar(); ?>
			
		<table class="table table-striped table-hover" width="100%">
			<thead>
				<tr>
					<th width="10%"><?php echo JText::_('COM_RSCOMMENTS_EMOTICON_CODE'); ?></th>
					<th width="10%" class="center text-center"><?php echo JText::_('COM_RSCOMMENTS_EMOTICON_IMAGE'); ?></th>
					<th width="5%" class="center text-center"><?php echo JText::_('COM_RSCOMMENTS_ACTIONS'); ?></th>
					<th width="1%" class="center text-center"></th>
				</tr>
			</thead>
			<tbody id="emoticons_container">
			<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>" id="row<?php echo $item->id; ?>">
					<td>
						<input type="text" id="symbol<?php echo $item->id; ?>" class="input-small form-control" size="10" name="symbol[<?php echo $item->id; ?>]" value="<?php echo $this->escape($item->replace); ?>" style="text-align: center;" disabled="disabled" />
					</td>
					
					<td class="center text-center">
						<img src="<?php echo strpos($item->with,'http') !== false ? $item->with : JURI::root().$item->with; ?>" id="preview<?php echo $item->id; ?>" class="rsc_emoticon_image" alt="" <?php if (!$item->with) { ?>style="display:none;"<?php } ?> /> 
						<input type="text" id="image<?php echo $item->id; ?>" class="input-xxlarge form-control" size="50" name="image[<?php echo $item->id; ?>]" value="<?php echo $this->escape($item->with); ?>" disabled="disabled" />
					</td>
					
					<td class="center text-center">
						<button type="button" id="edit<?php echo $item->id; ?>" class="btn btn-secondary" onclick="rsc_edit_emoticon(<?php echo $item->id; ?>);"><?php echo JText::_('COM_RSCOMMENTS_EDIT'); ?></button> 
						<button type="button" id="delete<?php echo $item->id; ?>" class="btn btn-secondary" onclick="rsc_delete_emoticon(<?php echo $item->id; ?>);"><?php echo JText::_('COM_RSCOMMENTS_DELETE'); ?></button> 
						<button type="button" id="save<?php echo $item->id; ?>" class="btn btn-secondary" style="display: none;" onclick="rsc_save_emoticon(<?php echo $item->id; ?>);"><?php echo JText::_('COM_RSCOMMENTS_SAVE'); ?></button> 
						<button type="button" id="cancel<?php echo $item->id; ?>" class="btn btn-secondary" style="display: none;" onclick="rsc_cancel_emoticon(<?php echo $item->id; ?>);"><?php echo JText::_('COM_RSCOMMENTS_CANCEL'); ?></button>
					</td>
					
					<td class="center text-center">
						<?php echo JHtml::image('com_rscomments/loader.gif', '', array('id' => 'loader'.$item->id, 'style' => 'display: none;'), true); ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
			<?php echo JHtml::_( 'form.token' ); ?>
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::image('com_rscomments/loader.gif', '', array('id' => 'loadingImage', 'style' => 'display: none;'), true); ?>
	</div>
</form>