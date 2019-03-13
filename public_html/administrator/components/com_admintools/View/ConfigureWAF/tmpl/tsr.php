<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;

?>
<div class="akeeba-form-group">
	<label for="tsrenable"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRENABLE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRENABLE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRENABLE'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'tsrenable', $this->wafconfig['tsrenable']); ?>
</div>

<div class="akeeba-form-group">
	<label
		   for="emailafteripautoban"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_EMAILAFTERIPAUTOBAN'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_EMAILAFTERIPAUTOBAN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_EMAILAFTERIPAUTOBAN'); ?>
	</label>

    <input type="text" size="50" name="emailafteripautoban" value="<?php echo $this->escape($this->wafconfig['emailafteripautoban']); ?>"/>
</div>

<div class="akeeba-form-group">
	<label for="tsrstrikes"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRSTRIKES'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRSTRIKES_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRSTRIKES'); ?>
	</label>

	<div class="akeeba-form--inline">
		<input class="input-mini pull-left" type="text" size="5" name="tsrstrikes"
			   value="<?php echo $this->escape($this->wafconfig['tsrstrikes']); ?>"/>
		<span class="floatme"><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRNUMFREQ'); ?></span>
		<input class="input-mini" type="text" size="5" name="tsrnumfreq"
			   value="<?php echo $this->escape($this->wafconfig['tsrnumfreq']); ?>"/>
		<?php echo Select::trsfreqlist('tsrfrequency', array('class' => 'input-small'), $this->wafconfig['tsrfrequency']); ?>
	</div>
</div>

<div class="akeeba-form-group">
	<label for="tsrbannum"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRBANNUM'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRBANNUM_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRBANNUM'); ?>
	</label>

	<div class="akeeba-form--inline">
		<input class="input-mini" type="text" size="5" name="tsrbannum"
			   value="<?php echo $this->escape($this->wafconfig['tsrbannum']); ?>"/>
		&nbsp;
		<?php echo Select::trsfreqlist('tsrbanfrequency', array(), $this->wafconfig['tsrbanfrequency']); ?>

	</div>
</div>

<div class="akeeba-form-group">
	<label for="tsrpermaban"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABAN'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABAN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABAN'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'permaban', $this->wafconfig['permaban']); ?>
</div>

<div class="akeeba-form-group">
	<label for="permabannum"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABANNUM'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABANNUM_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABANNUM'); ?>
	</label>

	<div>
		<input class="input-mini" type="text" size="5" name="permabannum"
			   value="<?php echo $this->escape($this->wafconfig['permabannum']); ?>"/>
		<span><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABANNUM_2'); ?></span>
	</div>
</div>

<div class="akeeba-form-group">
	<label
		   for="spammermessage"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_SPAMMERMESSAGE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_SPAMMERMESSAGE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_SPAMMERMESSAGE'); ?>
	</label>

    <input type="text" name="spammermessage"  value="<?php echo htmlentities($this->wafconfig['spammermessage']) ?>"/>
</div>
