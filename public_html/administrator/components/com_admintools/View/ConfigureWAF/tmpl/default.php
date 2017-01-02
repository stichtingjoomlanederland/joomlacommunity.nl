<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Utils\Ip;

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html $this */

// Protect from unauthorized access
defined('_JEXEC') or die;

JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen');

?>
<form name="adminForm" id="adminForm" action="index.php" method="post"
	  class="form form-horizontal form-horizontal-wide">
	<input type="hidden" name="option" value="com_admintools"/>
	<input type="hidden" name="view" value="ConfigureWAF"/>
	<input type="hidden" name="task" value="save"/>
	<input type="hidden" name="<?php echo $this->escape(JFactory::getSession()->getFormToken()); ?>" value="1"/>

<?php if (!$this->longConfig): ?>
	<?php echo \JHtml::_('bootstrap.startTabSet', 'admintools-wafconfig', array('active' => 'basic')); ?>
<?php endif; ?>

<?php if ($this->longConfig): ?>
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_BASICSETTINGS'); ?></h3>
<?php else: ?>
	<?php echo \JHtml::_('bootstrap.addTab', 'admintools-wafconfig', 'basic', addslashes(JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_BASICSETTINGS'))); ?>
<?php endif; ?>

	<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/base'); ?>

<?php if ($this->longConfig): ?>
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_ACTIVEFILTERING'); ?></h3>
<?php else: ?>
	<?php echo \JHtml::_('bootstrap.endTab'); ?>
	<?php echo \JHtml::_('bootstrap.addTab', 'admintools-wafconfig', 'activefiltering', addslashes(JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_ACTIVEFILTERING'))); ?>
<?php endif; ?>

	<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/activefiltering'); ?>

<?php if ($this->longConfig): ?>
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_JHARDENING'); ?></h3>
<?php else: ?>
	<?php echo \JHtml::_('bootstrap.endTab'); ?>
	<?php echo \JHtml::_('bootstrap.addTab', 'admintools-wafconfig', 'jhardening', addslashes(JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_JHARDENING'))); ?>
<?php endif; ?>

	<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/jhardening'); ?>

<?php if ($this->longConfig): ?>
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_FINGERPRINTING'); ?></h3>
<?php else: ?>
	<?php echo \JHtml::_('bootstrap.endTab'); ?>
	<?php echo \JHtml::_('bootstrap.addTab', 'admintools-wafconfig', 'fingerprinting', addslashes(JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_FINGERPRINTING'))); ?>
<?php endif; ?>

	<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/fingerprinting'); ?>

<?php if ($this->longConfig): ?>
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PROJECTHONEYPOT'); ?></h3>
<?php else: ?>
	<?php echo \JHtml::_('bootstrap.endTab'); ?>
	<?php echo \JHtml::_('bootstrap.addTab', 'admintools-wafconfig', 'projecthoneypot', addslashes(JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_PROJECTHONEYPOT'))); ?>
<?php endif; ?>

	<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/projecthoneypot'); ?>

<?php if ($this->longConfig): ?>
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_EXCEPTIONS'); ?></h3>
<?php else: ?>
	<?php echo \JHtml::_('bootstrap.endTab'); ?>
	<?php echo \JHtml::_('bootstrap.addTab', 'admintools-wafconfig', 'exceptions', addslashes(JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_EXCEPTIONS'))); ?>
<?php endif; ?>

	<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/exceptions'); ?>

<?php if ($this->longConfig): ?>
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSR'); ?></h3>
<?php else: ?>
	<?php echo \JHtml::_('bootstrap.endTab'); ?>
	<?php echo \JHtml::_('bootstrap.addTab', 'admintools-wafconfig', 'tsr', addslashes(JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_TSR'))); ?>
<?php endif; ?>

	<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/tsr'); ?>

<?php if ($this->longConfig): ?>
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_LOGGINGANDREPORTING'); ?></h3>
<?php else: ?>
	<?php echo \JHtml::_('bootstrap.endTab'); ?>
	<?php echo \JHtml::_('bootstrap.addTab', 'admintools-wafconfig', 'loggingandreporting', addslashes(JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPTGROUP_LOGGINGANDREPORTING'))); ?>
<?php endif; ?>

	<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/logging'); ?>

<?php if ($this->longConfig): ?>
	<h3><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_HEADER'); ?></h3>
<?php else: ?>
	<?php echo \JHtml::_('bootstrap.endTab'); ?>
	<?php echo \JHtml::_('bootstrap.addTab', 'admintools-wafconfig', 'custommessage', addslashes(JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CUSTOMMESSAGE_HEADER'))); ?>
<?php endif; ?>

	<?php echo $this->loadAnyTemplate('admin:com_admintools/ConfigureWAF/custom'); ?>

<?php if (!$this->longConfig): ?>
	<?php echo \JHtml::_('bootstrap.endTab'); ?>
	<?php echo \JHtml::_('bootstrap.endTabSet'); ?>
<?php endif; ?>
</form>