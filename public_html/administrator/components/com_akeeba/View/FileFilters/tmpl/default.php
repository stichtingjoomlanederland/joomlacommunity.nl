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
    akeeba.Fsfilters.render(data);
});

JS;

$this->getContainer()->template->addJSInline($js);

?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ErrorModal'); ?>

<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ProfileName'); ?>

<div class="form-inline well">
	<div>
		<label><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_ROOTDIR'); ?></label>
		<span><?php echo $this->root_select; ?></span>
		<button class="btn btn-danger" onclick="akeeba.Fsfilters.nuke(); return false;">
			<span class="icon-fire icon-trash"></span>
			<?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_NUKEFILTERS'); ?>
		</button>
		<a class="btn btn-small" href="index.php?option=com_akeeba&view=FileFilters&task=tabular">
			<span class="icon-list"></span>
			<?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_VIEWALL'); ?>
		</a>
	</div>
</div>

<div id="ak_crumbs_container" class="row-fluid">
	<ul id="ak_crumbs" class="breadcrumb"></ul>
</div>


<div id="ak_main_container">
	<fieldset id="ak_folder_container">
		<legend><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_DIRS'); ?></legend>
		<div id="folders"></div>
	</fieldset>

	<fieldset id="ak_files_container">
		<legend><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_FILES'); ?></legend>
		<div id="files"></div>
	</fieldset>
</div>