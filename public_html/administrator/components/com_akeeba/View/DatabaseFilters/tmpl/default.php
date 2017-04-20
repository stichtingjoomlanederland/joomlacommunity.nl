<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

$ajaxUrl = addslashes('index.php?option=com_akeeba&view=DatabaseFilters&task=ajax');
$this->json = addcslashes($this->json, "'\\");
$js = <<< JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
/**
 * Callback function for changing the active root in Database Table filters
 */
function akeeba_active_root_changed()
{
	var elRoot = document.getElementById('active_root');
	var data = {
		'root': elRoot.options[elRoot.selectedIndex].value
	};
    akeeba.Dbfilters.load(data);
}

akeeba.System.documentReady(function(){
    akeeba.System.params.AjaxURL = '$ajaxUrl';
	var data = JSON.parse('{$this->json}');
    akeeba.Dbfilters.render(data);
});

JS;

$this->getContainer()->template->addJSInline($js);

?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ErrorModal'); ?>

<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ProfileName'); ?>

<div class="form-inline well">
	<div>
		<label><?php echo \JText::_('COM_AKEEBA_DBFILTER_LABEL_ROOTDIR'); ?></label>
		<?php echo $this->root_select; ?>

		<button class="btn btn-success" onclick="akeeba.Dbfilters.excludeNonCMS(); return false;">
			<span class="icon-flag icon-white"></span>
			<?php echo \JText::_('COM_AKEEBA_DBFILTER_LABEL_EXCLUDENONCORE'); ?>
		</button>
		<button class="btn btn-danger" onclick="akeeba.Dbfilters.nuke(); return false;">
			<span class="icon-refresh icon-white"></span>
			<?php echo \JText::_('COM_AKEEBA_DBFILTER_LABEL_NUKEFILTERS'); ?>
		</button>
	</div>
</div>

<fieldset>
	<legend><?php echo \JText::_('COM_AKEEBA_DBFILTER_LABEL_TABLES'); ?></legend>
	<div id="tables"></div>
</fieldset>