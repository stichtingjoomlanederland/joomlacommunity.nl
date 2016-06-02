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

<script type="text/javascript" language="javascript">
akeeba.jQuery(document).ready(function($){
    akeeba.System.params.AjaxURL = '<?php echo addslashes('index.php?option=com_akeeba&view=FileFilters&task=ajax'); ?>';
    akeeba.Fsfilters.loadingGif = '<?php echo addslashes($this->container->template->parsePath('media://com_akeeba/icons/loading.gif')); ?>';

	// Bootstrap the page display
	var data = eval(<?php echo addcslashes($this->json, "'"); ?>);
    akeeba.Fsfilters.render(data);
});
</script>