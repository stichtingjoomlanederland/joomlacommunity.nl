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
		   for="custgenerator"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CUSTGENERATOR'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CUSTGENERATOR_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CUSTGENERATOR'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('custgenerator', array(), $this->wafconfig['custgenerator']); ?>

	</div>
</div>
<div class="control-group">
	<label class="control-label" for="generator"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_GENERATOR'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_GENERATOR_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_GENERATOR'); ?>
	</label>

	<div class="controls">
		<input type="text" size="45" name="generator" value="<?php echo $this->escape($this->wafconfig['generator']); ?>">
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="tmpl"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TMPL'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TMPL_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TMPL'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('tmpl', array(), $this->wafconfig['tmpl']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="tmplwhitelist"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TMPLWHITELIST'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TMPLWHITELIST_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TMPLWHITELIST'); ?>
	</label>

	<div class="controls">
		<input type="text" size="45" name="tmplwhitelist" value="<?php echo $this->escape($this->wafconfig['tmplwhitelist']); ?>"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="template"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TEMPLATE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TEMPLATE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TEMPLATE'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('template', array(), $this->wafconfig['template']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="allowsitetemplate"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ALLOWSITETEMPLATE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ALLOWSITETEMPLATE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ALLOWSITETEMPLATE'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('allowsitetemplate', array(), $this->wafconfig['allowsitetemplate']); ?>

	</div>
</div>
