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
	<label class="control-label" for="tsrenable"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRENABLE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRENABLE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRENABLE'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('tsrenable', array(), $this->wafconfig['tsrenable']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="emailafteripautoban"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_EMAILAFTERIPAUTOBAN'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_EMAILAFTERIPAUTOBAN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_EMAILAFTERIPAUTOBAN'); ?>
	</label>

	<div class="controls">
		<input class="input-large" type="text" size="50" name="emailafteripautoban"
			   value="<?php echo $this->escape($this->wafconfig['emailafteripautoban']); ?>"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="tsrstrikes"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRSTRIKES'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRSTRIKES_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRSTRIKES'); ?>
	</label>

	<div class="controls">
		<input class="input-mini pull-left" type="text" size="5" name="tsrstrikes"
			   value="<?php echo $this->escape($this->wafconfig['tsrstrikes']); ?>"/>
		<span class="floatme"><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRNUMFREQ'); ?></span>
		<input class="input-mini" type="text" size="5" name="tsrnumfreq"
			   value="<?php echo $this->escape($this->wafconfig['tsrnumfreq']); ?>"/>
		<?php echo Select::trsfreqlist('tsrfrequency', array('class' => 'input-small'), $this->wafconfig['tsrfrequency']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="tsrbannum"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRBANNUM'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRBANNUM_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSRBANNUM'); ?>
	</label>

	<div class="controls">
		<input class="input-mini" type="text" size="5" name="tsrbannum"
			   value="<?php echo $this->escape($this->wafconfig['tsrbannum']); ?>"/>
		&nbsp;
		<?php echo Select::trsfreqlist('tsrbanfrequency', array(), $this->wafconfig['tsrbanfrequency']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="tsrpermaban"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABAN'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABAN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABAN'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('permaban', array(), $this->wafconfig['permaban']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="permabannum"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABANNUM'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABANNUM_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABANNUM'); ?>
	</label>

	<div class="controls">
		<input class="input-mini" type="text" size="5" name="permabannum"
			   value="<?php echo $this->escape($this->wafconfig['permabannum']); ?>"/>
		<span><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PERMABANNUM_2'); ?></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="spammermessage"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_SPAMMERMESSAGE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_SPAMMERMESSAGE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_SPAMMERMESSAGE'); ?>
	</label>

	<div class="controls">
		<input type="text" class="input-xxlarge" name="spammermessage"
			   value="<?php echo htmlentities($this->wafconfig['spammermessage']) ?>"/>
	</div>
</div>
