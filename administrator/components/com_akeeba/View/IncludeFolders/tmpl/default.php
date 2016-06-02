<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

if(!class_exists('AkeebaHelperEscape')) JLoader::import('helpers.escape', JPATH_COMPONENT_ADMINISTRATOR);
?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ErrorModal'); ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/FolderBrowser'); ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ProfileName'); ?>

<fieldset>
	<div id="ak_list_container">
		<table id="ak_list_table" class="table table-striped">
			<thead>
				<tr>
					<!-- Delete -->
					<td width="20px">&nbsp;</td>
					<!-- Edit -->
					<td width="50px">&nbsp;</td>
					<!-- Directory path -->
					<td>
						<span rel="popover" data-original-title="<?php echo \JText::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_DIRECTORY'); ?>"
							  data-content="<?php echo \JText::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_DIRECTORY_HELP'); ?>">
							<?php echo \JText::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_DIRECTORY'); ?>
						</span>
					</td>
					<!-- Directory path -->
					<td>
						<span rel="popover" data-original-title="<?php echo \JText::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_VINCLUDEDIR'); ?>"
							  data-content="<?php echo \JText::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_VINCLUDEDIR_HELP'); ?>">
							<?php echo \JText::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_VINCLUDEDIR'); ?>
						</span>
					</td>
				</tr>
			</thead>
			<tbody id="ak_list_contents">
			</tbody>
		</table>
	</div>
</fieldset>

<script type="text/javascript" language="javascript">

akeeba.jQuery(document).ready(function($){
	akeeba.System.params.AjaxURL                       = '<?php echo addslashes(JUri::base() . 'index.php?option=com_akeeba&view=IncludeFolders&task=ajax'); ?>';
	akeeba.Configuration.URLs['browser']               = '<?php echo addslashes(JUri::base() . 'index.php?option=com_akeeba&view=Browser&processfolder=1&tmpl=component&folder='); ?>';
	akeeba.Configuration.enablePopoverFor(akeeba.jQuery('[rel="popover"]'));
	var data = JSON.parse('<?php echo addcslashes($this->json, "'\\"); ?>');
	akeeba.Extradirs.render(data);
});
</script>