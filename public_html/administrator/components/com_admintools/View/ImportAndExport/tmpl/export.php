<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;
?>
<div id="emailtemplateWarning" class="akeeba-block--failure" style="display: none">
	<?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_EMAILTEMPLATE_WARN'); ?>
</div>

<form name="adminForm" id="adminForm" action="index.php" method="post" class="akeeba-form--horizontal">
	<div class="akeeba-panel--primary">
		<header class="akeeba-block-header">
            <h3><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_FINE_TUNING'); ?></h3>
        </header>

		<div class="akeeba-form-group">
			<label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_WAFCONFIG'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'exportdata[wafconfig]', 1); ?>
		</div>

		<div class="akeeba-form-group">
			<label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_WAFBLACKLIST'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'exportdata[wafblacklist]', 1); ?>
    	</div>

		<div class="akeeba-form-group">
			<label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_WAFEXCEPTIONS'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'exportdata[wafexceptions]',1); ?>
		</div>

		<div class="akeeba-form-group">
			<label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_IPBLACKLIST'); ?></label>

			<?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'exportdata[ipblacklist]',  1); ?>
		</div>

		<div class="akeeba-form-group">
			<label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_IPWHITELIST'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'exportdata[ipwhitelist]', 1); ?>
		</div>

		<div class="akeeba-form-group">
			<label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_BADWORDS'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'exportdata[badwords]', 1); ?>
		</div>

		<div class="akeeba-form-group">
			<label><?php echo \JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_EMAILTEMPLATES'); ?></label>

            <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'exportdata[emailtemplates]', 0); ?>
		</div>
	</div>

    <input type="hidden" name="option" value="com_admintools"/>
    <input type="hidden" name="view" value="ImportAndExport"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
</form>
