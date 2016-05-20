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

<div class="well form-inline">
	<div>
		<label><?php echo JText::_('COM_AKEEBA_DBFILTER_LABEL_ROOTDIR') ?></label>
		<span><?php echo $this->root_select; ?></span>
	</div>
	<div id="addnewfilter">
		<label><?php echo JText::_('COM_AKEEBA_FILEFILTERS_LABEL_ADDNEWFILTER') ?></label>
		<button class="btn" onclick="akeeba.Dbfilters.addNew('tables'); return false;"><?php echo JText::_('COM_AKEEBA_DBFILTER_TYPE_TABLES') ?></button>
		<button class="btn" onclick="akeeba.Dbfilters.addNew('tabledata'); return false;"><?php echo JText::_('COM_AKEEBA_DBFILTER_TYPE_TABLEDATA') ?></button>
	</div>
</div>


<fieldset>
	<div id="ak_list_container">
		<table id="ak_list_table" class="table table-striped">
			<thead>
				<tr>
					<td width="250px"><?php echo JText::_('COM_AKEEBA_FILEFILTERS_LABEL_TYPE') ?></td>
					<td><?php echo JText::_('COM_AKEEBA_FILEFILTERS_LABEL_FILTERITEM') ?></td>
				</tr>
			</thead>
			<tbody id="ak_list_contents">
			</tbody>
		</table>
	</div>
</fieldset>

<script type="text/javascript" language="javascript">
/**
 * Callback function for changing the active root in Filesystem Filters
 */
function akeeba_active_root_changed()
{
	(function($){
        akeeba.Dbfilters.loadTab($('#active_root').val());
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
?>
	// Bootstrap the page display
	var data = JSON.parse('<?php echo AkeebaHelperEscape::escapeJS($this->json,"'"); ?>');
    akeeba.Dbfilters.renderTab(data);
});
</script>