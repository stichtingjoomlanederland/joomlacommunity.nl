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

<script type="text/javascript" language="javascript">
/**
 * Callback function for changing the active root in Database Table filters
 */
function akeeba_active_root_changed()
{
	(function($){
		var data = {
			'root': $('#active_root').val()
		};
        akeeba.Dbfilters.load(data);
	})(akeeba.jQuery);
}

akeeba.jQuery(document).ready(function($){
    akeeba.System.params.AjaxURL = '<?php echo addslashes('index.php?option=com_akeeba&view=DatabaseFilters&task=ajax'); ?>';
	var data = JSON.parse('<?php echo addcslashes($this->json, "'\\"); ?>');
    akeeba.Dbfilters.render(data);
});
</script>