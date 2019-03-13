<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<h3 class="rsfp-legend"><?php echo JText::_('RSFP_SCRIPTS_BEFORE_DISPLAY'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_SCRIPTS_BEFORE_DISPLAY_DESC'); ?></p>
<?php echo RSFormProHelper::showEditor('ScriptBeforeDisplay', $this->form->ScriptBeforeDisplay, array('classes' => 'rs_100', 'syntax' => 'php')); ?>

<h3 class="rsfp-legend"><?php echo JText::_('RSFP_SCRIPTS_BEFORE_VALIDATION'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_SCRIPTS_BEFORE_VALIDATION_DESC'); ?></p>
<?php echo RSFormProHelper::showEditor('ScriptBeforeValidation', $this->form->ScriptBeforeValidation, array('classes' => 'rs_100', 'syntax' => 'php')); ?>