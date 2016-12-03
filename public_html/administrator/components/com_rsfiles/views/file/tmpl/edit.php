<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal'); ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		<?php if (!$this->briefcase && is_dir(rsfilesHelper::root(true).$this->item->FilePath) && $this->type == 'folder') { ?>
		if (task == 'file.apply' || task == 'file.save') {
			if (
				jQuery('#extendCanCreate').prop('checked') || jQuery('#extendCanUpload').prop('checked') || 
				jQuery('#extendCanEdit').prop('checked') || jQuery('#extendCanDelete').prop('checked') || 
				jQuery('#extendView').prop('checked') || jQuery('#extendDownload').prop('checked')
			) {
				if (jQuery('#toolbar-apply a').length == 0) {
					jQuery('#toolbar-apply button').prop('disabled',true);
					jQuery('#toolbar-save button').prop('disabled',true);
					jQuery('#toolbar-cancel button').prop('disabled',true);
				} else {
					jQuery('#toolbar-apply a').removeAttr('onclick');
					jQuery('#toolbar-save a').removeAttr('onclick');
					jQuery('#toolbar-cancel a').removeAttr('onclick');
				}
				
				RSFiles.Sync.steps  = [
					'checkExtendFolders',
					'checkExtendFiles',
					'checkExtendExternal'
				];
				RSFiles.Sync.type = 'extend';
				RSFiles.Sync.startFolder = "<?php echo addslashes($this->item->FilePath); ?>";
				RSFiles.Sync.stop = function() {
					window.setTimeout(function () {
						jQuery('#com-rsfiles-sync-progress').css('display','none');
						jQuery('#com-rsfiles-sync-text').html('');
						
						if (jQuery('#toolbar-apply a').length == 0) {
							jQuery('#toolbar-apply button').prop('disabled',false);
							jQuery('#toolbar-save button').prop('disabled',false);
							jQuery('#toolbar-cancel button').prop('disabled',false);
						} else {
							jQuery('#toolbar-apply a').on('click', function () { Joomla.submitform(task, document.getElementById('adminForm')); });
							jQuery('#toolbar-save a').on('click', function () { Joomla.submitform(task, document.getElementById('adminForm')); });
							jQuery('#toolbar-cancel a').on('click', function () { Joomla.submitform(task, document.getElementById('adminForm')); });
						}
						
						Joomla.submitform(task, document.getElementById('adminForm'));
					}, 2000);
				};
				RSFiles.Sync.start();
			} else {
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		} else {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		<?php } else { ?>
		if (task == 'file.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
		<?php } ?>
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rsfiles&view=file&layout=edit&IdFile='.(int) $this->item->IdFile); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span12">
			<div id="com-rsfiles-sync-progress" style="display:none;">
				<img src="<?php echo JURI::root(); ?>administrator/components/com_rsfiles/assets/images/icons/loader.gif" alt="" />
				<div id="com-rsfiles-sync-text"></div>
			</div>
			
			<?php foreach ($this->layouts as $layout) {
				// add the tab title
				$this->tabs->title('COM_RSFILES_FILE_TAB_'.strtoupper($layout), $layout);
				
				// prepare the content
				$content = $this->loadTemplate($layout);
				
				// add the tab content
				$this->tabs->content($content);
			}
			
			// render tabs
			echo $this->tabs->render();
			?>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('IdFile'); ?>
	<?php echo $this->form->getInput('FileType'); ?>
	<?php if ($this->type == 'external') echo $this->form->getInput('FileParent'); ?>
</form>