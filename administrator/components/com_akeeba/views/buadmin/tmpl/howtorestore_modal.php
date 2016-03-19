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

$proKey = (defined('AKEEBA_PRO') && AKEEBA_PRO) ? 'PRO' : 'CORE';
?>

<div id="akeeba-config-howtorestore-bubble" class="modal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>
			<?php echo JText::_('BUADMIN_LABEL_HOWDOIRESTORE_LEGEND') ?>
		</h3>
	</div>
	<div class="modal-body">
		<?php echo JText::sprintf('COM_AKEEBA_BUADMIN_LABEL_HOWDOIRESTORE_TEXT_' . $proKey,
			'https://www.akeebabackup.com/videos/1212-akeeba-backup-core/1618-abtc04-restore-site-new-server.html',
			'index.php?option=com_akeeba&view=transfer'); ?>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">
			<span class="icon icon-cancel"></span>
			<?php echo JText::_('COM_AKEEBA_BUADMIN_BTN_REMINDME'); ?>
		</a>
		<a href="index.php?option=com_akeeba&view=buadmin&task=hidemodal" class="btn btn-success">
			<span class="icon icon-ok-sign icon-white"></span>
			<?php echo JText::_('COM_AKEEBA_BUADMIN_BTN_DONTSHOWTHISAGAIN'); ?>
		</a>
	</div>
</div>

<script>
	jQuery(document).ready(function(){
		jQuery("#akeeba-config-howtorestore-bubble").modal({
			backdrop: true,
			keyboard: true,
			show: true
		});
	});
</script>