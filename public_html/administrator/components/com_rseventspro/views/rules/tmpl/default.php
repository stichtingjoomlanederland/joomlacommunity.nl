<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.keepalive'); ?>

<script type="text/javascript">
	function rse_rule(val) {
		if (val == 4) {
			document.getElementById('email').style.display = '';
		} else {
			document.getElementById('email').style.display = 'none';
		}
	}

	function rse_selectEmail(id, name) {
		jQuery('#email').text(name);
		jQuery('#mid').val(id);
		window.parent.jQuery('#rseModal').modal('hide');
	}
</script>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=rules'); ?>" name="adminForm" id="adminForm">
	<?php echo RSEventsproAdapterGrid::sidebar(); ?>
		<table class="table table-striped">
			<tbody>
				<tr>
					<td>
						<span id="message1"><?php echo JText::_('COM_RSEVENTSPRO_RULE_MESSAGE_1'); ?></span> 
						<select name="payment" id="payment" class="input-large custom-select-sm">
							<?php echo JHtml::_('select.options', rseventsproHelper::getPayments(), 'value', 'text'); ?>
						</select>
						<span id="message2"><?php echo JText::_('COM_RSEVENTSPRO_RULE_MESSAGE_3'); ?></span> 
						<select name="status" id="status" class="input-large custom-select-sm">
							<?php echo JHtml::_('select.options', rseventsproHelper::getStatuses(), 'value', 'text'); ?>
						</select>
						<span id="message3"><?php echo JText::_('COM_RSEVENTSPRO_RULE_MESSAGE_2'); ?></span>
						<input type="text" id="interval" name="interval" value="12" class="input-mini" size="3" onkeyup="this.value=this.value.replace(/[^0-9]/g, '');" /> 
						<span id="message4"><?php echo JText::_('COM_RSEVENTSPRO_RULE_MESSAGE_4'); ?></span>
						<select name="rule" id="rule" class="input-large custom-select-sm" onchange="rse_rule(this.value);">
							<?php echo JHtml::_('select.options', rseventsproHelper::getRules(), 'value', 'text'); ?>
						</select>
						<a style="display:none;" id="email" onclick="jQuery('#rseModal').modal('show');" href="javascript:void(0)">
							<?php echo JText::_('COM_RSEVENTSPRO_SELECT_RULE_MESSAGE'); ?>
						</a>
						<input type="hidden" id="mid" name="mid" value="" />
						<a href="javascript:void(0)" onclick="addRule('<?php echo JText::_('COM_RSEVENTSPRO_INVALID_RULE',true); ?>','<?php echo JText::_('COM_RSEVENTSPRO_RULE_SELECT_MESSAGE',true); ?>','<?php echo JText::_('COM_RSEVENTSPRO_SELECT_RULE_MESSAGE',true); ?>');">
							<i class="fa fa-plus-circle"></i>
						</a>
						<?php echo JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'loader', 'style' => 'vertical-align: middle; display: none;'), true); ?> 
					</td>
				</tr>
			</tbody>
		</table>
		
		<table class="table table-striped">
			<thead>
				<th width="1%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JHtml::_('grid.checkall'); ?></th>
				<th><?php echo JText::_('COM_RSEVENTSPRO_RULE'); ?></th>
				<th width="1%" class="nowrap hidden-phone <?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
			</thead>
			<tbody id="rseprocontainer">
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="nowrap has-context">
							<?php echo JText::_('COM_RSEVENTSPRO_RULE_MESSAGE_1'); ?>
							<b><?php echo rseventsproHelper::getPayment($item->payment); ?></b>
							<?php echo JText::_('COM_RSEVENTSPRO_RULE_MESSAGE_3'); ?>
							<b><?php echo rseventsproHelper::getStatuses($item->status); ?></b>
							<?php echo JText::_('COM_RSEVENTSPRO_RULE_MESSAGE_2'); ?>
							<b><?php echo $item->interval; ?></b>
							<?php echo JText::_('COM_RSEVENTSPRO_RULE_MESSAGE_4'); ?>
							<b><?php echo rseventsproHelper::getRules($item->rule); ?></b>
							<?php if ($item->mid) { ?>
							 <b>(<?php echo $this->getSubject($item->mid); ?>)</b>
							<?php } ?>
						</td>
						<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?> hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
</form>
<script type="text/javascript">rse_rule(document.getElementById('rule').value);</script>

<?php echo JHtml::_('bootstrap.renderModal', 'rseModal', array('title' => '&nbsp;', 'url' => JRoute::_('index.php?option=com_rseventspro&view=emails&tmpl=component', false), 'bodyHeight' => 70)); ?>