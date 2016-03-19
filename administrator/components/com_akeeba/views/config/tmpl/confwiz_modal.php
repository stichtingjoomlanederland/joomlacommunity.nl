<?php
/**
 * @package Akeeba
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 3.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

JHtml::_('behavior.modal');

$extraClass = version_compare(JVERSION, '3.0.0', 'ge') ? '' : 'akeeba-bootstrap';

?>

<div id="akeeba-config-confwiz-bubble" class="<?php echo $extraClass ?> modal">
	<div class="modal-header">
		<?php if (version_compare(JVERSION, '3.0.0', 'ge')): ?>
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<?php endif; ?>
		<h3>
			<?php echo JText::_('COM_AKEEBA_CONFIG_HEADER_CONFWIZ') ?>
		</h3>
	</div>
	<div class="modal-body">
		<p>
			<?php echo JText::_('COM_AKEEBA_CONFIG_LBL_CONFWIZ_INTRO') ?>
		</p>
		<p>
			<a href="index.php?option=com_akeeba&view=confwiz"
			   class="btn btn-large btn-success">
				<span class="icon icon-lightning"></span>
				<?php echo JText::_('AKEEBA_CONFWIZ'); ?>
			</a>
		</p>
		<p>
			<?php echo JText::_('COM_AKEEBA_CONFIG_LBL_CONFWIZ_AFTER'); ?>
		</p>
	</div>
	<?php if (version_compare(JVERSION, '3.0.0', 'ge')): ?>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">
			<span class="icon icon-cancel"></span>
			<?php echo JText::_('JCANCEL'); ?>
		</a>
	</div>
	<?php endif;?>
</div>

<script>
<?php if (version_compare(JVERSION, '3.0.0', 'ge')): ?>
	jQuery(document).ready(function(){
		jQuery("#akeeba-config-confwiz-bubble").modal({
			backdrop: true,
			keyboard: true,
			show: true
		});
	});
<?php else: ?>
	window.addEvent('domready', function() {
		SqueezeBox.open($('akeeba-config-confwiz-bubble'), {
			handler: 'adopt',
			size: {x: 400, y: 300}
		});
	});
<?php endif;?>
</script>
