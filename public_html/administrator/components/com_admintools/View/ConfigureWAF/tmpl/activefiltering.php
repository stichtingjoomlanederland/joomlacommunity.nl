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
	<label class="control-label" for="sqlishield"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SQLISHIELD'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SQLISHIELD_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SQLISHIELD'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('sqlishield', array(), $this->wafconfig['sqlishield']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="muashield"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_MUASHIELD'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_MUASHIELD_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_MUASHIELD'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('muashield', array(), $this->wafconfig['muashield']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="csrfshield"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CSRFSHIELD'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CSRFSHIELD_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CSRFSHIELD'); ?>
	</label>

	<div class="controls">
		<?php echo Select::csrflist('csrfshield', array(), $this->wafconfig['csrfshield']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="rfishield"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_RFISHIELD'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_RFISHIELD_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_RFISHIELD'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('rfishield', array(), $this->wafconfig['rfishield']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="dfishield"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DFISHIELD'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DFISHIELD_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DFISHIELD'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('dfishield', array(), $this->wafconfig['dfishield']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="uploadshield"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_UPLOADSHIELD'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_UPLOADSHIELD_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_UPLOADSHIELD'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('uploadshield', array(), $this->wafconfig['uploadshield']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="sessionshield"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SESSIONSHIELD'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SESSIONSHIELD_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SESSIONSHIELD'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('sessionshield', array(), $this->wafconfig['sessionshield']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="antispam"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ANTISPAM'); ?>"
		   data-content="<?php echo JText::sprintf('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ANTISPAM_TIP', 'index.php?option=com_admintools&view=BadWords'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ANTISPAM'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('antispam', array(), $this->wafconfig['antispam']); ?>

	</div>
</div>
