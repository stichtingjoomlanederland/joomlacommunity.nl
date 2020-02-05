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
	<label
		   for="httpblenable"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLENABLE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLENABLE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLENABLE'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'httpblenable', $this->wafconfig['httpblenable']); ?>
</div>

<div class="akeeba-form-group">
	<label for="bbhttpblkey"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY'); ?>
	</label>

    <input type="text" size="45" name="bbhttpblkey" value="<?php echo $this->escape($this->wafconfig['bbhttpblkey']); ?>"/>
</div>

<div class="akeeba-form-group">
	<label
		   for="httpblthreshold"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLTHRESHOLD'); ?>"
		   data-content="<?php echo str_replace('"', '&quot;', \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLTHRESHOLD_TIP')); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLTHRESHOLD'); ?>
	</label>

    <input type="text" size="5" name="httpblthreshold" value="<?php echo $this->escape($this->wafconfig['httpblthreshold']); ?>"/>
</div>

<div class="akeeba-form-group">
	<label
		   for="httpblmaxage"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLMAXAGE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLMAXAGE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLMAXAGE'); ?>
	</label>

    <input type="text" size="5" name="httpblmaxage" value="<?php echo $this->escape($this->wafconfig['httpblmaxage']); ?>"/>
</div>

<div class="akeeba-form-group">
	<label
		   for="httpblblocksuspicious"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLBLOCKSUSPICIOUS'); ?>"
		   data-content="<?php echo str_replace('"', '&quot;', \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLBLOCKSUSPICIOUS_TIP')); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLBLOCKSUSPICIOUS'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'httpblblocksuspicious', $this->wafconfig['httpblblocksuspicious']); ?>
</div>
