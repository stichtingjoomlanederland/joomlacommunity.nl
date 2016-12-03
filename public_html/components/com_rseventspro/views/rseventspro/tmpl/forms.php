<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<script type="text/javascript">
function rse_add_form(id,value) {
	window.parent.jQuery('.rsepro-event-form').text(value);
	window.parent.jQuery('#form').val(id);
	window.parent.SqueezeBox.close();
}
</script>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=forms&tmpl=component&id='.JFactory::getApplication()->input->getInt('id')); ?>" method="post" name="adminForm" id="adminForm">
	<table class="category table table-striped">
		<thead>
			<tr>
				<th width="5">#</th>
				<th class="title"><?php echo JText::_('COM_RSEVENTSPRO_FORM_NAME'); ?></th>
				<th class="title"><?php echo JText::_('COM_RSEVENTSPRO_OBSERVATIONS'); ?></th>
			</tr>
		</thead>
		<tbody>
		<tr class="row0">
			<td>-</td>
			<td><a href="javascript:void(0);" onclick="rse_add_form('0','<?php echo JText::_('COM_RSEVENTSPRO_DEFAULT_FORM',true); ?>');"><?php echo JText::_('COM_RSEVENTSPRO_DEFAULT_FORM'); ?></a></td>
			<td></td>
		</tr>
		<?php if (!empty($this->forms)) { ?>
		<?php $i = 0; ?>
		<?php $k = 1; ?>
		<?php foreach($this->forms as $row) { ?>
		<?php $form = rseventsproHelper::checkform($row->FormId,JFactory::getApplication()->input->getInt('id')); ?>
			<tr class="row<?php echo $k; ?>">
				<td><?php echo $this->pagination->getRowOffset($i); ?></td>
				<td>
					<?php if (@$form['result']) { ?>
					<a href="javascript:void(0);" onclick="rse_add_form('<?php echo $row->FormId; ?>','<?php echo $this->escape($row->FormName); ?>');"><?php echo $row->FormName; ?></a>
					<?php } else { ?>
					<?php echo $row->FormName; ?>
					<?php } ?>
				</td>
				<td><?php echo @$form['message']; ?></td>
			</tr>
		<?php $i++; ?>
		<?php $k=1-$k; ?>
		<?php } ?>
		<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
	</table>

	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="controller" value="events" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo JFactory::getApplication()->input->getInt('id'); ?>" />
</form>