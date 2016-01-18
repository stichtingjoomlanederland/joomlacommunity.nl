<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.keepalive'); 
$function = $this->from == 'editor' ? 'rsf_placeholder' : 'jSelectFolder'; ?>

<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#rsf_folders tr').each(function(){
		jQuery(this).on('mouseenter', function() {
			jQuery(this).find('.rsfl_options a').css('display','block');
		});
		
		jQuery(this).on('mouseleave', function() {
			jQuery(this).find('.rsfl_options a').css('display','none');
		});
	});
});

</script>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rsfiles&view=files&layout=modal'); ?>" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span12">
			<div class="well">
				<div class="rsf_navigation">
					<?php echo $this->navigation; ?>
				</div>
			</div>
			
			<table class="table table-striped adminlist" id="rsf_folders">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_RSFILES_FOLDERS'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->items as $i => $item) { ?>
					<?php $path = str_replace($this->config->download_folder, '', urldecode($item->fullpath)); ?>
					<?php $path = ltrim($path,rsfilesHelper::ds()); ?>
					<tr class="row<?php echo $i % 2; ?>">
						
						<?php if ($item->type == 'folder') { ?>
						<td>
							<img src="<?php echo JURI::root().'administrator/components/com_rsfiles/assets/images/icons/folder.png';?>" style="vertical-align:middle;" alt="" /> 
							<a href="javascript:void(0)" onclick="window.parent.<?php echo $function; ?>('<?php echo addslashes($path); ?>');">
								<?php echo $item->name; ?>
							</a> 
							<span class="rsfl_options">
								<a class="btn btn-mini" href="javascript:void(0);" onclick="window.parent.<?php echo $function; ?>('<?php echo addslashes($path); ?>');"><?php echo JText::_('COM_RSFILES_SELECT');?></a>
								<a class="btn btn-mini" href="<?php echo JRoute::_('index.php?option=com_rsfiles&view=files&layout=modal'.($this->from == 'editor' ? '&from=editor' : '').'&tmpl=component&folder='.$item->fullpath); ?>"><?php echo JText::_('COM_RSFILES_OPEN'); ?></a>
							</span>
						</td>
						<?php } ?>
						
						<?php if($item->type == 'file' || $item->type == 'external') { ?>
						<td>
							<img src="<?php echo JURI::root().'administrator/components/com_rsfiles/assets/images/icons/file.png';?>" style="vertical-align:middle;" alt="" /> 
							<a href="javascript:void(0)" onclick="window.parent.<?php echo $function; ?>('<?php echo addslashes($path); ?>');">
								<?php echo $item->name; ?>
							</a> 
							<span class="rsfl_options">
								<a class="btn btn-mini" href="javascript:void(0);" onclick="window.parent.<?php echo $function; ?>('<?php echo addslashes($path); ?>');"><?php echo JText::_('COM_RSFILES_SELECT');?></a>
							</span>
						</td>
						<?php } ?>
						
					</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="1" align="center"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="path" id="path" value="<?php echo $this->current; ?>" />
	<input type="hidden" name="folder" value="<?php echo $this->folder; ?>" />
	<input type="hidden" name="from" value="<?php echo $this->from; ?>" />
	<input type="hidden" name="tmpl" value="component" />
</form>