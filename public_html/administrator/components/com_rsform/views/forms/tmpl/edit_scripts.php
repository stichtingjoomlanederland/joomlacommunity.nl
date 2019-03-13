<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<h3 class="rsfp-legend"><?php echo JText::_('RSFP_SCRIPTS_DISPLAY'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_SCRIPTS_DISPLAY_DESC'); ?></p>
<?php echo RSFormProHelper::showEditor('ScriptDisplay', $this->form->ScriptDisplay, array('classes' => 'rs_100', 'syntax' => 'php')); ?>

<h3 class="rsfp-legend"><?php echo JText::_('RSFP_SCRIPTS_PROCESS'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_SCRIPTS_PROCESS_DESC'); ?></p>
<?php echo RSFormProHelper::showEditor('ScriptProcess', $this->form->ScriptProcess, array('classes' => 'rs_100', 'syntax' => 'php')); ?>

<h3 class="rsfp-legend"><?php echo JText::_('RSFP_SCRIPTS_PROCESS2'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_SCRIPTS_PROCESS2_DESC'); ?></p>
<?php echo RSFormProHelper::showEditor('ScriptProcess2', $this->form->ScriptProcess2, array('classes' => 'rs_100', 'syntax' => 'php')); ?>