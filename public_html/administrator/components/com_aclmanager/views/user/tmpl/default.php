<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Initialise variable
$id = $this->state->get('filter.user_id');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<div id="aclmanager">
	<form action="<?php echo JRoute::_('index.php?option=com_aclmanager&view=user&id='.$id);?>" method="post" name="adminForm" id="adminForm">
		<!-- Begin sidebar -->
		<?php if(($this->params->get('show_legend',1)) || ($this->params->get('show_info',1)) || ($this->params->get('show_assigned',1))): ?>
		<div class="width-30 fltrt">
			<?php echo $this->loadTemplate('sidebar'); ?>
		</div>
		<div class="width-70 fltlft">
		<?php else: ?>
		<div class="width-100 fltlft">
		<?php endif; ?>
		<!-- End sidebar -->

		<!-- Begin filter -->
		<?php echo $this->loadTemplate('filter'); ?>
		<!-- End filter -->

		<table id="persistent" class="adminlist aclmanager">
			<!-- Begin header -->
			<?php echo $this->loadTemplate('table_header'); ?>
			<!-- End header -->
		</table>

		<table id="global-aclmanager" class="adminlist aclmanager">
			<!-- Begin header -->
			<?php echo $this->loadTemplate('table_header'); ?>
			<!-- End header -->
			<!-- Begin footer -->
			<tfoot>
				<tr>
					<td colspan="<?php echo(2 + count($this->actions));?>">
						<?php if (!$id) {
								echo '';
							} else {
								echo $this->pagination->getListFooter();
							}
						?>
					</td>
				</tr>
			</tfoot>
			<!-- End footer -->
			<!-- Begin table rows -->
			<?php echo $this->loadTemplate('table_permissions'); ?>
			<!-- End table rows -->
		</table>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo($id);?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>

<script type="text/javascript">
	window.addEvent('domready',function() {
		var table = $$('#global-aclmanager');
		table.each(function(table,i) {
			var pos = table.getCoordinates();
			var size = $('global-aclmanager').getStyle('width');
			var ss = new ScrollSpy({
				min: pos.top,
				max: pos.bottom-80,
				onEnter: function() {
					$$("#persistent thead").setStyle('width',size);
					$$("#persistent thead").setStyle('display','inline-table');
				},
				onLeave: function() {
					$$("#persistent thead").setStyle('display','none');
				}
			});
		});
	});
</script>

<div class="copyright">
	<p><?php echo JText::_('COM_ACLMANAGER_COPYRIGHT'); ?> &copy; 2011 - <?php echo date('Y');?>. <?php echo JText::_('COM_ACLMANAGER_DEVELOPED_BY');?>. <a href="http://www.aclmanager.net" target="_blank">www.aclmanager.net</a></p>
</div>