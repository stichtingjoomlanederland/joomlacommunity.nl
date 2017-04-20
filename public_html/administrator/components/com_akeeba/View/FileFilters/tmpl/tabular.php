<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

$ajaxUrl = addslashes('index.php?option=com_akeeba&view=FileFilters&task=ajax');
$loadingUrl = addslashes($this->container->template->parsePath('media://com_akeeba/icons/loading.gif'));
$this->json = addcslashes($this->json, "'");
$js = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
akeeba.System.documentReady(function() {
    akeeba.System.params.AjaxURL = '$ajaxUrl';
    akeeba.Fsfilters.loadingGif = '$loadingUrl';

	// Bootstrap the page display
	var data = eval({$this->json});
    akeeba.Fsfilters.renderTab(data);
});

JS;

$this->getContainer()->template->addJSInline($js);

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