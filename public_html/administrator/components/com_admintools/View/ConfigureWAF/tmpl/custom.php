<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;
?>
<div class="akeeba-form-group">
	<label for="custom403msg"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_LABEL'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_DESC'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_LABEL'); ?>
	</label>

    <input type="text" name="custom403msg" value="<?php echo htmlentities($this->wafconfig['custom403msg']) ?>"
			   title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_DESC'); ?>"/>
</div>

<div class="akeeba-form-group">
	<label for="use403view"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_USE403VIEW'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_USE403VIEW_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_USE403VIEW'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'use403view', $this->wafconfig['use403view']); ?>
</div>

<div class="akeeba-form-group">
	<label for="troubleshooteremail"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TROUBLESHOOTEREMAIL'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TROUBLESHOOTEREMAIL_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TROUBLESHOOTEREMAIL'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'troubleshooteremail', $this->wafconfig['troubleshooteremail']); ?>
</div>
