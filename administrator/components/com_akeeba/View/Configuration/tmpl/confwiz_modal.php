<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

JHtml::_('behavior.modal');

?>

<div id="akeeba-config-confwiz-bubble" class="modal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>
			<?php echo \JText::_('COM_AKEEBA_CONFIG_HEADER_CONFWIZ'); ?>
		</h3>
	</div>
	<div class="modal-body">
		<p>
			<?php echo \JText::_('COM_AKEEBA_CONFIG_LBL_CONFWIZ_INTRO'); ?>
		</p>
		<p>
			<a href="index.php?option=com_akeeba&view=ConfigurationWizard" class="btn btn-large btn-success">
				<span class="icon icon-lightning"></span>
				<?php echo \JText::_('COM_AKEEBA_CONFWIZ'); ?>
			</a>
		</p>
		<p>
			<?php echo \JText::_('COM_AKEEBA_CONFIG_LBL_CONFWIZ_AFTER'); ?>
		</p>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">
			<span class="icon icon-cancel"></span>
			<?php echo \JText::_('JCANCEL'); ?>
		</a>
	</div>
</div>

<script>
	jQuery(document).ready(function(){
		jQuery("#akeeba-config-confwiz-bubble").modal({
			backdrop: true,
			keyboard: true,
			show: true
		});
	});
</script>
