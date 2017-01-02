<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;
?>
<div class="control-group">
	<label class="control-label"
		   for="httpblenable"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLENABLE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLENABLE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLENABLE'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('httpblenable', array(), $this->wafconfig['httpblenable']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="bbhttpblkey"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BBHTTPBLKEY'); ?>
	</label>

	<div class="controls">
		<input type="text" size="45" name="bbhttpblkey" value="<?php echo $this->escape($this->wafconfig['bbhttpblkey']); ?>"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="httpblthreshold"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLTHRESHOLD'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLTHRESHOLD_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLTHRESHOLD'); ?>
	</label>

	<div class="controls">
		<input type="text" size="5" name="httpblthreshold"
			   value="<?php echo $this->escape($this->wafconfig['httpblthreshold']); ?>"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="httpblmaxage"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLMAXAGE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLMAXAGE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLMAXAGE'); ?>
	</label>

	<div class="controls">
		<input type="text" size="5" name="httpblmaxage" value="<?php echo $this->escape($this->wafconfig['httpblmaxage']); ?>"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="httpblblocksuspicious"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLBLOCKSUSPICIOUS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLBLOCKSUSPICIOUS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_HTTPBLBLOCKSUSPICIOUS'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('httpblblocksuspicious', array(), $this->wafconfig['httpblblocksuspicious']); ?>

	</div>
</div>
