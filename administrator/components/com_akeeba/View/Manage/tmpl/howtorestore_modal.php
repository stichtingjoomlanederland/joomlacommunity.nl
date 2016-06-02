<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  \Akeeba\Backup\Admin\View\Manage\Html  $this */

?>

<div id="akeeba-config-howtorestore-bubble" class="modal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3>
			<?php echo \JText::_('COM_AKEEBA_BUADMIN_LABEL_HOWDOIRESTORE_LEGEND'); ?>
		</h3>
	</div>
	<div class="modal-body">
        <?php echo \JText::sprintf('COM_AKEEBA_BUADMIN_LABEL_HOWDOIRESTORE_TEXT_PRO',
                'https://www.akeebabackup.com/videos/1212-akeeba-backup-core/1618-abtc04-restore-site-new-server.html',
                'index.php?option=com_akeeba&view=Transfer',
                'https://www.akeebabackup.com/latest-kickstart-core.zip'
                ); ?>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" data-dismiss="modal">
			<span class="icon icon-cancel"></span>
			<?php echo \JText::_('COM_AKEEBA_BUADMIN_BTN_REMINDME'); ?>
		</a>
		<a href="index.php?option=com_akeeba&view=Manage&task=hidemodal" class="btn btn-success">
			<span class="icon icon-ok-sign icon-white"></span>
			<?php echo \JText::_('COM_AKEEBA_BUADMIN_BTN_DONTSHOWTHISAGAIN'); ?>
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