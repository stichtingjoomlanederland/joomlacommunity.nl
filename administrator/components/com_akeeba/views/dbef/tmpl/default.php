<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 1.3
 */

defined('_JEXEC') or die();

?>
<div id="dialog" title="<?php echo JText::_('DBFILTER_ERROR_TITLE') ?>">
</div>

<div class="alert alert-info">
	<strong><?php echo JText::_('COM_AKEEBA_CPANEL_PROFILE_TITLE'); ?></strong>
	#<?php echo $this->profileid; ?> <?php echo $this->profilename; ?>
</div>

<div class="form-inline well">
	<div>
		<label><?php echo JText::_('COM_AKEEBA_DBFILTER_LABEL_ROOTDIR') ?></label>
		<?php echo $this->root_select; ?>
		<button class="btn btn-success" onclick="akeeba.Dbfilters.excludeNonCMS(); return false;">
			<i class="icon-flag icon-white"></i>
			<?php echo JText::_('COM_AKEEBA_DBFILTER_LABEL_EXCLUDENONCORE'); ?>
		</button>
		<button class="btn btn-danger" onclick="akeeba.Dbfilters.nuke(); return false;">
			<i class="icon-fire icon-white"></i>
			<?php echo JText::_('COM_AKEEBA_DBFILTER_LABEL_NUKEFILTERS'); ?>
		</button>
	</div>
</div>

<fieldset>
	<legend><?php echo JText::_('COM_AKEEBA_DBFILTER_LABEL_TABLES'); ?></legend>
	<div id="tables"></div>
</fieldset>

<script type="text/javascript" language="javascript">
/**
 * Callback function for changing the active root in Database Table filters
 */
function akeeba_active_root_changed()
{
	(function($){
		var data = new Object;
		data.root = $('#active_root').val();
        akeeba.Dbfilters.load(data);
	})(akeeba.jQuery);
}

akeeba.jQuery(document).ready(function($){
	// Set the AJAX proxy URL
    akeeba.System.params.AjaxURL = '<?php echo AkeebaHelperEscape::escapeJS('index.php?option=com_akeeba&view=dbef&task=ajax') ?>';

	// Create the dialog
	$("#dialog").dialog({
		autoOpen: false,
		closeOnEscape: false,
		height: 200,
		width: 300,
		hide: 'slide',
		modal: true,
		position: 'center',
		show: 'slide'
	});
	// Create an AJAX error trap
    akeeba.System.params.errorCallback = function( message ) {
		var dialog_element = $("#dialog");
		dialog_element.html(''); // Clear the dialog's contents
		dialog_element.dialog('option', 'title', '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_UI_AJAXERRORDLG_TITLE')) ?>');
		$(document.createElement('p')).html('<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_CONFIG_UI_AJAXERRORDLG_TEXT')) ?>').appendTo(dialog_element);
		$(document.createElement('pre')).html( message ).appendTo(dialog_element);
		dialog_element.dialog('open');
	};
	// Push translations
    akeeba.Dbfilters.translations['UI-ROOT'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_FILEFILTERS_LABEL_UIROOT')) ?>';
    akeeba.Dbfilters.translations['UI-ERROR-FILTER'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER')) ?>';
	<?php
			$filters = array('tables', 'tabledata');
			foreach($filters as $type)
			{
				echo "\takeeba.Dbfilters.translations['UI-FILTERTYPE-".strtoupper($type)."'] = '".
					AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_DBFILTER_TYPE_'.strtoupper($type))).
					"';\n";
			}

			$table_types = array('misc', 'table', 'view', 'procedure', 'function', 'trigger');
			foreach($table_types as $type)
			{
				echo "\takeeba.Dbfilters.translations['UI-TABLETYPE-".strtoupper($type)."'] = '".
					AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_DBFILTER_TABLE_'.strtoupper($type))).
					"';\n";
			}
	?>

	// Bootstrap the page display
	var data = JSON.parse('<?php echo AkeebaHelperEscape::escapeJS($this->json,"'"); ?>');
    akeeba.Dbfilters.render(data);
});
</script>