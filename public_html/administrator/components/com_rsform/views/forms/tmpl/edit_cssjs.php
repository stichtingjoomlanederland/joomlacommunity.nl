<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<h3 class="rsfp-legend"><?php echo JText::_('RSFP_CSS'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_CSS_DESC'); ?></p>
<?php echo RSFormProHelper::showEditor('CSS', $this->form->CSS, array('classes' => 'rs_100', 'syntax' => 'html')); ?>

<h3 class="rsfp-legend"><?php echo JText::_('RSFP_JS'); ?></h3>
<p class="alert alert-info"><?php echo JText::_('RSFP_JS_DESC'); ?></p>
<?php echo RSFormProHelper::showEditor('JS', $this->form->JS, array('classes' => 'rs_100', 'syntax' => 'html')); ?>