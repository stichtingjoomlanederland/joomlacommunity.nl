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
JText::script('COM_RSCOMMENTS_EMOTICONS_EMPTY_VALUES');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'emoticons.add') {
			rsc_add_emoticon('<?php echo JURI::root(); ?>');
			return false;
		}
		
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=emoticons'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			
			<table class="adminlist table table-striped table-hover" width="100%">
				<thead>
					<tr>
						<th width="10%"><?php echo JText::_('COM_RSCOMMENTS_EMOTICON_CODE'); ?></th>
						<th width="10%" class="center" align="center"><?php echo JText::_('COM_RSCOMMENTS_EMOTICON_IMAGE'); ?></th>
						<th width="5%" class="center" align="center"><?php echo JText::_('COM_RSCOMMENTS_ACTIONS'); ?></th>
						<th width="1%" class="center" align="center"></th>
					</tr>
				</thead>
				<tbody id="emoticons_container">
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>" id="row<?php echo $item->id; ?>">
						<td>
							<input type="text" id="symbol<?php echo $item->id; ?>" class="input-small" size="10" name="symbol[<?php echo $item->id; ?>]" value="<?php echo $this->escape($item->replace); ?>" style="text-align: center;" disabled="disabled" />
						</td>
						
						<td class="center" align="center">
							<img src="<?php echo strpos($item->with,'http') !== false ? $item->with : JURI::root().$item->with; ?>" id="preview<?php echo $item->id; ?>" class="rsc_emoticon_image" alt="" <?php if (!$item->with) { ?>style="display:none;"<?php } ?> /> 
							<input type="text" id="image<?php echo $item->id; ?>" class="input-xxlarge" size="50" name="image[<?php echo $item->id; ?>]" value="<?php echo $this->escape($item->with); ?>" disabled="disabled" />
						</td>
						
						<td class="center" align="center">
							<button type="button" id="edit<?php echo $item->id; ?>" class="btn button" onclick="rsc_edit_emoticon(<?php echo $item->id; ?>);"><?php echo JText::_('COM_RSCOMMENTS_EDIT'); ?></button> 
							<button type="button" id="delete<?php echo $item->id; ?>" class="btn button" onclick="rsc_delete_emoticon(<?php echo $item->id; ?>);"><?php echo JText::_('COM_RSCOMMENTS_DELETE'); ?></button> 
							<button type="button" id="save<?php echo $item->id; ?>" class="btn button" style="display: none;" onclick="rsc_save_emoticon(<?php echo $item->id; ?>);"><?php echo JText::_('COM_RSCOMMENTS_SAVE'); ?></button> 
							<button type="button" id="cancel<?php echo $item->id; ?>" class="btn button" style="display: none;" onclick="rsc_cancel_emoticon(<?php echo $item->id; ?>);"><?php echo JText::_('COM_RSCOMMENTS_CANCEL'); ?></button>
						</td>
						
						<td class="center" align="center">
							<img id="loader<?php echo $item->id; ?>" src="<?php echo JURI::root(); ?>components/com_rscomments/assets/images/loader.gif" alt="" style="display: none;" />
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			<?php echo JHtml::_( 'form.token' ); ?>
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="task" value="" />
		</div>
	</div>
</form>