<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.keepalive'); ?>

<?php if ($this->hash) { ?>
<script type="text/javascript">
var rsepro_restore_overwrite = <?php if ($this->overwrite) { ?>true;<?php } else { ?>false;<?php } ?>
window.addEventListener('DOMContentLoaded', function() {
	setTimeout(function() {
		jQuery('#backuprestore > li > a[href="#restore"]').click();
	jQuery('#backuprestore dt.restore').click();
	jQuery('a[href="#restore"]')[0].click();
	rsepro_restore('<?php echo $this->hash; ?>',0,0,0);
	},1000);
});
</script>
<?php } ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=backup'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" autocomplete="off" enctype="multipart/form-data">
	<?php echo RSEventsproAdapterGrid::sidebar(); ?>
		<div class="progress mb-3" id="rsepro-backup">
			<div style="width: 0%;" class="<?php echo RSEventsproAdapterGrid::styles(array('bar')); ?>">0%</div>
		</div>
			
		<?php 
			$this->tabs->addTitle('COM_RSEVENTSPRO_BACKUP', 'backup');
			$this->tabs->addContent($this->loadTemplate('backup'));
			$this->tabs->addTitle('COM_RSEVENTSPRO_RESTORE', 'restore');
			$this->tabs->addContent($this->loadTemplate('restore'));
			echo $this->tabs->render();
		?>
	</div>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="task" value="" />
</form>