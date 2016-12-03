<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=emails&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" autocomplete="off">
	<div class="row-fluid">
		<div class="span12">
			<div style="width:100%;text-align:right;">
				<button type="button" onclick="Joomla.submitbutton('email.add');" class="btn btn-primary button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_NEW_BTN'); ?></button>
				<button type="button" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ITEM_INFO',true); ?>');}else{ Joomla.submitbutton('email.edit')}" class="btn btn-primary button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EDIT_BTN'); ?></button>
				<button type="button" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ITEM_INFO',true); ?>');}else{ Joomla.submitbutton('emails.delete')}" class="btn btn-primary button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE_BTN'); ?></button>
				<button type="button" onclick="window.parent.SqueezeBox.close();" class="btn button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
			</div>
			<br />
			
			<table class="table table-striped adminlist">
				<thead>
					<th width="1%" align="center" class=""><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
					<th><?php echo JText::_('COM_RSEVENTSPRO_RULE_EMAIL_SUBJECT'); ?></th>
					<th width="3%" align="center" class="center hidden-phone"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
				</thead>
				<tbody>
					<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center"><?php echo JHTML::_('grid.id', $i, $item->id); ?></td>
						<td class="nowrap has-context">
							<a href="javascript:void(0)" onclick="if (window.parent) window.parent.rse_selectEmail('<?php echo $item->id; ?>','<?php echo addslashes($item->subject); ?>');">
								<?php echo $this->escape($item->subject); ?>
							</a> 
							<small>
								&mdash;
								<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=email.edit&tmpl=component&id='.$item->id); ?>">
								[<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EDIT_BTN'); ?>]
							</a>
							</small>
						</td>
						<td align="center" class="center hidden-phone">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
			
			<div>
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="tmpl" value="component" />
			</div>
		</div>
	</div>
</form>