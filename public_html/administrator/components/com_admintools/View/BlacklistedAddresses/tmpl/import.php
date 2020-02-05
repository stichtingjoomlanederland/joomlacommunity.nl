<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \FOF30\View\DataView\Html */
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal" enctype="multipart/form-data">

    <div class="akeeba-panel--primary">
        <header class="akeeba-block-header">
            <h3><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_DETAILS'); ?></h3>
        </header>

        <div class="akeeba-form-group">
            <label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_DELIMITERS'); ?></label>

            <?php echo Select::csvdelimiters('csvdelimiters', 1, array('class'=>'minwidth')); ?>

            <p>
                <?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_DELIMITERS_DESC'); ?>
            </p>
        </div>

        <div class="akeeba-form-group" id="field_delimiter" style="display:none;">
            <label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FIELD_DELIMITERS'); ?></label>

            <input type="text" name="field_delimiter" value="">
            <p>
                <?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FIELD_DELIMITERS_DESC'); ?>
            </p>
        </div>

        <div class="akeeba-form-group" id="field_enclosure" style="display:none">
            <label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FIELD_ENCLOSURE'); ?></label>

            <input type="text" name="field_enclosure" value="">
            <p>
                <?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FIELD_ENCLOSURE_DESC'); ?>
            </p>
        </div>

        <div class="akeeba-form-group">
            <label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FILE'); ?></label>

            <input type="file" name="csvfile"/>
            <p>
                <?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FILE_DESC'); ?>
            </p>
        </div>
    </div>

    <input type="hidden" name="option" value="com_admintools"/>
    <input type="hidden" name="view" value="BlacklistedAddresses"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
</form>
