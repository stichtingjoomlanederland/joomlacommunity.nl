<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;
?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal" enctype="multipart/form-data">
	<input type="hidden" name="option" value="com_admintools"/>
	<input type="hidden" name="view" value="ImportAndExport"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="<?php echo $this->escape(JFactory::getSession()->getFormToken()); ?>" value="1"/>

	<fieldset>
		<legend><?php echo \JText::_('COM_ADMINTOOLS_TITLE_IMPORT_SETTINGS'); ?></legend>

		<div class="control-group">
			<label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FILE'); ?></label>
			<div class="controls">
				<input type="file" name="importfile" value="" />
			</div>
		</div>
	</fieldset>
</form>