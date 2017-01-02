<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;
?>
<div id="emailtemplateWarning" class="alert alert-error" style="display: none">
	<?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_EMAILTEMPLATE_WARN'); ?>
</div>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal">
	<input type="hidden" name="option" value="com_admintools"/>
	<input type="hidden" name="view" value="ImportAndExport"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="<?php echo $this->escape(JFactory::getSession()->getFormToken()); ?>" value="1"/>

	<fieldset>
		<legend><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FINE_TUNING'); ?></legend>

		<div class="control-group">
			<label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_WAFCONFIG'); ?></label>
			<div class="controls">
				<?php echo Select::booleanlist('exportdata[wafconfig]', null, 1); ?>

			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_WAFBLACKLIST'); ?></label>
			<div class="controls">
				<?php echo Select::booleanlist('exportdata[wafblacklist]', null, 1); ?>

			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_WAFEXCEPTIONS'); ?></label>
			<div class="controls">
				<?php echo Select::booleanlist('exportdata[wafexceptions]', null, 1); ?>

			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_IPBLACKLIST'); ?></label>
			<div class="controls">
				<?php echo Select::booleanlist('exportdata[ipblacklist]', null, 1); ?>

			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_IPWHITELIST'); ?></label>
			<div class="controls">
				<?php echo Select::booleanlist('exportdata[ipwhitelist]', null, 1); ?>

			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_BADWORDS'); ?></label>
			<div class="controls">
				<?php echo Select::booleanlist('exportdata[badwords]', null, 1); ?>

			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_EMAILTEMPLATES'); ?></label>
			<div class="controls">
				<?php echo Select::booleanlist('exportdata[emailtemplates]', null, 0); ?>

			</div>
		</div>
	</fieldset>
</form>