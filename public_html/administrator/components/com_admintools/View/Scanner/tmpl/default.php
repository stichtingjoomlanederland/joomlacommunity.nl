<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal">
	<div class="akeeba-panel--primary">
		<header class="akeeba-block-header">
            <h3><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_BASICCONF'); ?></h3>
        </header>

		<div class="akeeba-form-group">
			<label for="fileextensions"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_FILEEXTENSIONS'); ?></label>

            <textarea cols="80" rows="10" name="fileextensions"
                      id="fileextensions"><?php echo implode("\n", $this->fileExtensions) ?></textarea>
		</div>

		<div class="akeeba-form-group">
			<label for="exludefolders"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_EXCLUDEFOLDERS'); ?></label>

            <textarea cols="80" rows="10" name="exludefolders"
                      id="exludefolders"><?php echo implode("\n", $this->excludeFolders) ?></textarea>
		</div>

		<div class="akeeba-form-group">
			<label for="exludefiles"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_EXCLUDEFILES'); ?></label>

            <textarea cols="80" rows="10" name="exludefiles"
                      id="exludefiles"><?php echo implode("\n", $this->excludeFiles) ?></textarea>
		</div>
	</div>

	<div class="akeeba-panel--primary">
		<header class="akeeba-block-header">
            <h3><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_TUNINGCONF'); ?></h3>
        </header>

		<div class="akeeba-form-group">
			<label for="mintime"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_MINEXECTIME'); ?></label>

            <div class="akeeba-form-controls">
                <div class="akeeba-input-group--small">
                    <?php echo Select::valuelist(array(
                        '0'    => '0', '250' => '0.25', '500' => '0.5', '1000' => '1',
                        '2000' => '2', '3000' => '3', '4000' => '4', '5000' => '5',
                        '7500' => '7.5', '10000' => '10', '15000' => '15', '20000' => '20',
                    ), 'mintime', array(), $this->minExecTime) ?>
                    <span>s</span>
                </div>
            </div>
		</div>

		<div class="akeeba-form-group">
			<label for="maxtime"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_MAXEXECTIME'); ?></label>

            <div class="akeeba-form-controls">
                <div class="akeeba-input-group--small">
                    <?php echo Select::valuelist(array(
                        '1', '2', '3', '5', '7', '10', '14', '15', '20', '23',
                        '25', '30', '45', '60', '90', '120', '180'
                    ), 'maxtime', array(), $this->maxExecTime, true) ?>
                    <span>s</span>
                </div>
            </div>
		</div>

		<div class="akeeba-form-group">
			<label for="runtimebias"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANNER_RUNTIMEBIAS'); ?></label>

            <div class="akeeba-form-controls">
                <div class="akeeba-input-group--small">
                    <?php echo Select::valuelist(array(
                        '10', '20', '25', '30', '40', '50', '60',
                        '75', '80', '90', '100'
                    ), 'runtimebias', array(), $this->runtimeBias, true) ?>
                    <span>%</span>
                </div>
            </div>
		</div>
	</div>

    <input type="hidden" name="option" value="com_admintools"/>
    <input type="hidden" name="view" value="Scanner"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
</form>
