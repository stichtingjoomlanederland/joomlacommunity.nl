<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=components&component='.JFactory::getApplication()->input->get('component').'&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span11">
			<?php echo $this->filterbar->show();?>
			<table class="table table-striped table-hover adminlist" id="rsc_components_tbl" width="560">
				<thead>
					<tr>
						<th width="2%"><?php echo JText::_('COM_RSCOMMENTS_COMPONENT_ID'); ?></th>
						<th width="5"><?php echo JText::_('COM_RSCOMMENTS_COMPONENT_TITLE'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td><?php echo $item->id;?></td>
						<td><a href="#" rel="<?php echo $item->id;?>" class="rsc_filter_option"><?php echo $item->title; ?></a></td>
					</tr>
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
			</table>
			<?php echo JHtml::_( 'form.token' ); ?>
			<input type="hidden" name="task" value="" />
		</div>
	</div>
</form>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('.rsc_filter_option').click(function(){
		jQuery('#rsc_filter_component_id',window.parent.document).val(jQuery(this).attr('rel'));
		jQuery('#adminForm',window.parent.document).submit();
	});
});
</script>