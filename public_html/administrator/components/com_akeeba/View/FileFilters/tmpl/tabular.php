<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ErrorModal'); ?>

<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ProfileName'); ?>

<div class="well form-inline">
	<div>
		<label><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_ROOTDIR'); ?></label>
		<span><?php echo $this->root_select; ?></span>
	</div>
	<div id="addnewfilter">
		<?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_ADDNEWFILTER'); ?>
		<button class="btn" onclick="akeeba.Fsfilters.addNew('directories'); return false;"><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_TYPE_DIRECTORIES'); ?></button>
		<button class="btn" onclick="akeeba.Fsfilters.addNew('skipfiles'); return false;"><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_TYPE_SKIPFILES'); ?></button>
		<button class="btn" onclick="akeeba.Fsfilters.addNew('skipdirs'); return false;"><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_TYPE_SKIPDIRS'); ?></button>
		<button class="btn" onclick="akeeba.Fsfilters.addNew('files'); return false;"><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_TYPE_FILES'); ?></button>
	</div>
</div>

<fieldset id="ak_roots_container_tab">
	<div id="ak_list_container">
		<table id="ak_list_table" class="table table-striped">
			<thead>
				<tr>
					<td width="250px"><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_TYPE'); ?></td>
					<td><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_FILTERITEM'); ?></td>
				</tr>
			</thead>
			<tbody id="ak_list_contents">
			</tbody>
		</table>
	</div>
</fieldset>

<script type="text/javascript" language="javascript">
akeeba.jQuery(document).ready(function($){
    akeeba.System.params.AjaxURL = '<?php echo addslashes('index.php?option=com_akeeba&view=FileFilters&task=ajax'); ?>';
	akeeba.Fsfilters.loadingGif = '<?php echo addslashes($this->container->template->parsePath('media://com_akeeba/icons/loading.gif')); ?>';
	var data = eval(<?php echo addcslashes($this->json, "'"); ?>);
    akeeba.Fsfilters.renderTab(data);
});
</script>