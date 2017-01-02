<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;

?>
<form action="index.php" method="post" name="adminForm" id="adminForm"
	  class="form form-horizontal form-horizontal-wide">
	<input type="hidden" name="option" value="com_admintools"/>
	<input type="hidden" name="view" value="Scanner"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>

	<fieldset>
		<legend><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_BASICCONF'); ?></legend>
		<div class="control-group">
			<label class="control-label"
				   for="fileextensions"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_FILEEXTENSIONS'); ?></label>

			<div class="controls">
				<textarea cols="80" rows="10" name="fileextensions"
						  id="fileextensions"><?php echo implode("\n", $this->fileExtensions) ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"
				   for="exludefolders"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_EXCLUDEFOLDERS'); ?></label>

			<div class="controls">
				<textarea cols="80" rows="10" name="exludefolders"
						  id="exludefolders"><?php echo implode("\n", $this->excludeFolders) ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"
				   for="exludefiles"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_EXCLUDEFILES'); ?></label>

			<div class="controls">
				<textarea cols="80" rows="10" name="exludefiles"
						  id="exludefiles"><?php echo implode("\n", $this->excludeFiles) ?></textarea>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_TUNINGCONF'); ?></legend>
		<div class="akeeba-ui-optionrow control-group">
			<label class="control-label" for="mintime"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_MINEXECTIME'); ?></label>

			<div class="controls">
				<div class="input-append">
					<?php echo Select::valuelist(array(
						'0'    => '0', '250' => '0.25', '500' => '0.5', '1000' => '1',
						'2000' => '2', '3000' => '3', '4000' => '4', '5000' => '5',
						'7500' => '7.5', '10000' => '10', '15000' => '15', '20000' => '20',
					), 'mintime', array('class' => 'input-small'), $this->minExecTime) ?>
					<span class="add-on"> s</span>
				</div>
			</div>
		</div>
		<div class="akeeba-ui-optionrow control-group">
			<label class="control-label" for="maxtime"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_MAXEXECTIME'); ?></label>

			<div class="controls">
				<div class="input-append">
					<?php echo Select::valuelist(array(
						'1', '2', '3', '5', '7', '10', '14', '15', '20', '23',
						'25', '30', '45', '60', '90', '120', '180'
					), 'maxtime', array('class' => 'input-small'), $this->maxExecTime, true) ?>
					<span class="add-on"> s</span>
				</div>
			</div>
		</div>
		<div class="akeeba-ui-optionrow control-group">
			<label class="control-label"
				   for="runtimebias"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_RUNTIMEBIAS'); ?></label>

			<div class="controls">
				<div class="input-append">
					<?php echo Select::valuelist(array(
						'10', '20', '25', '30', '40', '50', '60',
						'75', '80', '90', '100'
					), 'runtimebias', array('class' => 'input-small'), $this->runtimeBias, true) ?>
					<span class="add-on"> %</span>
				</div>
			</div>
		</div>


	</fieldset>
</form>