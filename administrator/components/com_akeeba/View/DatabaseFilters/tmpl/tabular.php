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
		<label><?php echo \JText::_('COM_AKEEBA_DBFILTER_LABEL_ROOTDIR'); ?></label>
		<span><?php echo $this->root_select; ?></span>
	</div>
	<div id="addnewfilter">
		<label><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_ADDNEWFILTER'); ?></label>
		<button class="btn" onclick="akeeba.Dbfilters.addNew('tables'); return false;">
			<?php echo \JText::_('COM_AKEEBA_DBFILTER_TYPE_TABLES'); ?>
		</button>
		<button class="btn" onclick="akeeba.Dbfilters.addNew('tabledata'); return false;">
			<?php echo \JText::_('COM_AKEEBA_DBFILTER_TYPE_TABLEDATA'); ?>
		</button>
	</div>
</div>


<fieldset>
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
	akeeba.System.params.AjaxURL = '<?php echo addslashes('index.php?option=com_akeeba&view=DatabaseFilters&task=ajax'); ?>';
	var data = JSON.parse('<?php echo addcslashes($this->json, "'\\"); ?>');
    akeeba.Dbfilters.renderTab(data);
});
</script>