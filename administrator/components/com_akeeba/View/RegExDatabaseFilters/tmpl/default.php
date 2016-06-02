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
	<label><?php echo \JText::_('COM_AKEEBA_DBFILTER_LABEL_ROOTDIR'); ?></label>
	<span id="ak_roots_container_tab">
		<span><?php echo $this->root_select; ?></span>
	</span>
</div>

<div>
	<div id="ak_list_container">
		<table id="table-container" class="adminlist table table-striped">
			<thead>
				<tr>
					<td width="90px">&nbsp;</td>
					<td width="250px"><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_TYPE'); ?></td>
					<td><?php echo \JText::_('COM_AKEEBA_FILEFILTERS_LABEL_FILTERITEM'); ?></td>
				</tr>
			</thead>
			<tbody id="ak_list_contents" class="table-container">
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript" language="javascript">
akeeba.jQuery(document).ready(function($){
    akeeba.System.params.AjaxURL = '<?php echo addslashes(JUri::base().'index.php?option=com_akeeba&view=RegExDatabaseFilters&task=ajax'); ?>';
	var data = JSON.parse('<?php echo addcslashes($this->json, "'\\"); ?>');
    akeeba.Regexdbfilters.render(data);
});
</script>