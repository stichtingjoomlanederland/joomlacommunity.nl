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
<div id="dialog" title="<?php echo JText::_('FSFILTER_ERROR_TITLE') ?>">
</div>

<div class="alert alert-info">
	<strong><?php echo JText::_('CPANEL_PROFILE_TITLE'); ?></strong>
	#<?php echo $this->profileid; ?> <?php echo $this->profilename; ?>
</div>

<div class="well form-inline">
	<div>
		<label><?php echo JText::_('FSFILTER_LABEL_ROOTDIR') ?></label>
		<span><?php echo $this->root_select; ?></span>
	</div>
	<div id="addnewfilter">
		<?php echo JText::_('FSFILTER_LABEL_ADDNEWFILTER') ?>
		<button class="btn" onclick="akeeba.Fsfilters.addNew('directories'); return false;"><?php echo JText::_('FSFILTER_TYPE_DIRECTORIES') ?></button>
		<button class="btn" onclick="akeeba.Fsfilters.addNew('skipfiles'); return false;"><?php echo JText::_('FSFILTER_TYPE_SKIPFILES') ?></button>
		<button class="btn" onclick="akeeba.Fsfilters.addNew('skipdirs'); return false;"><?php echo JText::_('FSFILTER_TYPE_SKIPDIRS') ?></button>
		<button class="btn" onclick="akeeba.Fsfilters.addNew('files'); return false;"><?php echo JText::_('FSFILTER_TYPE_FILES') ?></button>
	</div>
</div>

<fieldset id="ak_roots_container_tab">
	<div id="ak_list_container">
		<table id="ak_list_table" class="table table-striped">
			<thead>
				<tr>
					<td width="250px"><?php echo JText::_('FILTERS_LABEL_TYPE') ?></td>
					<td><?php echo JText::_('FILTERS_LABEL_FILTERITEM') ?></td>
				</tr>
			</thead>
			<tbody id="ak_list_contents">
			</tbody>
		</table>
	</div>
</fieldset>

<script type="text/javascript" language="javascript">
akeeba.jQuery(document).ready(function($){
	// Set the AJAX proxy URL
    akeeba.System.params.AjaxURL = '<?php echo AkeebaHelperEscape::escapeJS('index.php?option=com_akeeba&view=fsfilter&task=ajax') ?>';
	// Set the media root
    akeeba.Fsfilters.loadingGif = '<?php echo $this->mediadir ?>../icons/loading.gif';
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
		dialog_element.dialog('option', 'title', '<?php echo AkeebaHelperEscape::escapeJS(JText::_('CONFIG_UI_AJAXERRORDLG_TITLE')) ?>');
		$(document.createElement('p')).html('<?php echo AkeebaHelperEscape::escapeJS(JText::_('CONFIG_UI_AJAXERRORDLG_TEXT')) ?>').appendTo(dialog_element);
		$(document.createElement('pre')).html( message ).appendTo(dialog_element);
		dialog_element.dialog('open');
	};
	// Push translations
    akeeba.Fsfilters.translations['UI-ROOT'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('FILTERS_LABEL_UIROOT')) ?>';
    akeeba.Fsfilters.translations['UI-ERROR-FILTER'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('FILTERS_LABEL_UIERRORFILTER')) ?>';
<?php
	$filters = array('directories', 'skipfiles', 'skipdirs', 'files');
	foreach($filters as $type)
	{
		echo "\takeeba.Fsfilters.translations['UI-FILTERTYPE-".strtoupper($type)."'] = '".
			AkeebaHelperEscape::escapeJS(JText::_('FSFILTER_TYPE_'.strtoupper($type))).
			"';\n";
	}
?>
	// Bootstrap the page display
	var data = JSON.parse('<?php echo AkeebaHelperEscape::escapeJS($this->json,"'"); ?>');
    akeeba.Fsfilters.renderTab(data);
});
</script>