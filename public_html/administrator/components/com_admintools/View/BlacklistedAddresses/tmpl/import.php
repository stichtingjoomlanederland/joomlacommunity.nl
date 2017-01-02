<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \FOF30\View\DataView\Html */
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form form-horizontal"
      enctype="multipart/form-data">
    <input type="hidden" name="option" value="com_admintools"/>
    <input type="hidden" name="view" value="BlacklistedAddresses"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="<?php echo $this->escape(JFactory::getSession()->getFormToken()); ?>" value="1"/>

    <div class="row-fluid">
        <div class="span6">
            <h3><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_DETAILS'); ?></h3>

            <div class="control-group">
                <label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_DELIMITERS'); ?></label>
                <div class="controls">
                    <?php echo Select::csvdelimiters('csvdelimiters', 1, array('class'=>'minwidth')); ?>

                    <div class="help-block">
                        <?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_DELIMITERS_DESC'); ?>
                    </div>
                </div>
            </div>
            <div class="control-group" id="field_delimiter" style="display:none">
                <label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FIELD_DELIMITERS'); ?></label>
                <div class="controls">
                    <input type="text" name="field_delimiter" value="">
                    <div class="help-block">
                        <?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FIELD_DELIMITERS_DESC'); ?>
                    </div>
                </div>
            </div>
            <div class="control-group" id="field_enclosure" style="display:none">
                <label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FIELD_ENCLOSURE'); ?></label>
                <div class="controls">
                    <input type="text" name="field_enclosure" value="">
                    <div class="help-block">
                        <?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FIELD_ENCLOSURE_DESC'); ?>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FILE'); ?></label>
                <div class="controls">
                    <input type="file" name="csvfile"/>
                    <div class="help-block">
                        <?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FILE_DESC'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>