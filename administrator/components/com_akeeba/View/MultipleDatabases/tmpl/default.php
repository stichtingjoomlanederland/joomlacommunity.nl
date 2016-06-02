<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

?>
<div id="akEditorDialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="akEditorDialogLabel" aria-hidden="true" style="display:none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="akEditorDialogLabel">
					<?php echo \JText::_('COM_AKEEBA_FILEFILTERS_EDITOR_TITLE'); ?>
				</h4>
			</div>
			<div class="modal-body" id="akEditorDialogBody">
				<div class="form form-horizontal" id="ak_editor_table">
					<div class="control-group">
						<label class="control-label" for="ake_driver"><?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_DRIVER'); ?></label>
						<div class="controls">
							<select id="ake_driver">
								<option value="mysqli">MySQLi</option>
								<option value="mysql">MySQL (old)</option>
								<option value="pdomysql">PDO MySQL</option>
								<option value="sqlsrv">SQL Server</option>
								<option value="sqlazure">Windows Azure SQL</option>
								<option value="postgresql">PostgreSQL</option>
							</select>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="ake_host"><?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_HOST'); ?></label>
						<div class="controls">
							<input id="ake_host" type="text" size="40" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="ake_port"><?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_PORT'); ?></label>
						<div class="controls">
							<input id="ake_port" type="text" size="10" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="ake_username"><?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_USERNAME'); ?></label>
						<div class="controls">
							<input id="ake_username" type="text" size="40" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="ake_password"><?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_PASSWORD'); ?></label>
						<div class="controls">
							<input id="ake_password" type="password" size="40" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="ake_database"><?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_DATABASE'); ?></label>
						<div class="controls">
							<input id="ake_database" type="text" size="40" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="ake_prefix"><?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_PREFIX'); ?></label>
						<div class="controls">
							<input id="ake_prefix" type="text" size="10" />
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="akEditorBtnDefault"><?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_TEST'); ?></button>
				<button type="button" class="btn btn-primary" id="akEditorBtnSave"><?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_SAVE'); ?></button>
				<button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_CANCEL'); ?></button>
			</div>
		</div>
	</div>
</div>

<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ErrorModal'); ?>

<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ProfileName'); ?>

<fieldset>
	<div id="ak_list_container">
		<table id="ak_list_table" class="table table-striped">
			<thead>
				<tr>
					<?php /* Delete */ ?>
					<td width="20px">&nbsp;</td>
					<?php /* Edit */ ?>
					<td width="20px">&nbsp;</td>
					<?php /* Database host */ ?>
					<td><?php echo \JText::_('COM_AKEEBA_MULTIDB_LABEL_HOST'); ?></td>
					<?php /* Database */ ?>
					<td><?php echo \JText::_('COM_AKEEBA_MULTIDB_LABEL_DATABASE'); ?></td>
				</tr>
			</thead>
			<tbody id="ak_list_contents">
			</tbody>
		</table>
	</div>
</fieldset>

<script type="text/javascript" language="javascript">

akeeba.jQuery(document).ready(function($){
    akeeba.System.params.AjaxURL = '<?php echo addslashes(JUri::base().'index.php?option=com_akeeba&view=MultipleDatabases&task=ajax'); ?>';
	akeeba.Multidb.loadingGif = '<?php echo addslashes($this->container->template->parsePath('media://com_akeeba/icons/loading.gif')); ?>';
	var data = JSON.parse('<?php echo addcslashes($this->json, "'\\"); ?>');
    akeeba.Multidb.render(data);
});
</script>