<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

//keep session alive while editing
JHtml::_('behavior.keepalive'); ?>

<?php if ($this->hash) { ?>
<script type="text/javascript">
jQuery('#backuprestore > li > a[href="#restore"]').click();
jQuery('#backuprestore dt.restore').click();
var rsepro_restore_overwrite = <?php if ($this->overwrite) { ?>true;<?php } else { ?>false;<?php } ?>
jQuery(document).ready(function() {
	rsepro_restore('<?php echo $this->hash; ?>',0,0,0);
});
</script>
<?php } ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=backup'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" autocomplete="off" enctype="multipart/form-data">
	<div class="row-fluid" id="rsepro-backup-container">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
			<div class="progress" id="rsepro-backup">
				<div style="width: 0%;" class="bar">
					<div class="pull-right progress-label">0%</div>
				</div>
			</div>
			
			<?php 
				$this->tabs->title('COM_RSEVENTSPRO_BACKUP', 'backup');
				$this->tabs->content($this->loadTemplate('backup'));
				$this->tabs->title('COM_RSEVENTSPRO_RESTORE', 'restore');
				$this->tabs->content($this->loadTemplate('restore'));
				echo $this->tabs->render();
			?>
		</div>
	</div>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="task" value="" />
</form>