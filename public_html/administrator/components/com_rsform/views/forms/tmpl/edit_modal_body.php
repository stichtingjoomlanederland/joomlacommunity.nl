<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;
?>
	<div id="rsform_textboxdiv" class="rsform_hide">
		<?php echo JHtml::_('bootstrap.startTabSet', 'editModalTabs', array('active' => 'rsfptab0')); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'editModalTabs', 'rsfptab0', JText::_('RSFP_COMPONENTS_GENERAL_TAB')); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'editModalTabs', 'rsfptab1', JText::_('RSFP_COMPONENTS_VALIDATIONS_TAB')); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'editModalTabs', 'rsfptab2', JText::_('RSFP_COMPONENTS_ATRIBUTES_TAB')); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>