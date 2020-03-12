<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;
?>
	<div id="rsfp-tabs">
		<?php echo JHtml::_('bootstrap.startTabSet', 'editModalTabs', array('active' => 'rsfptab0')); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'editModalTabs', 'rsfptab0', JText::_('RSFP_COMPONENTS_GENERAL_TAB')); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'editModalTabs', 'rsfptab1', JText::_('RSFP_COMPONENTS_VALIDATIONS_TAB')); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'editModalTabs', 'rsfptab2', JText::_('RSFP_COMPONENTS_ATTRIBUTES_TAB')); ?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'editModalTabs', 'rsfptab3', JText::_('RSFP_COMPONENTS_FREETEXT_TAB')); ?>
		<div id="rsfp-editor-container" class="rsfp-hidden">
		<?php echo RSFormProHelper::getEditor()->display('param[TEXT]', '', '100%', '120px', 40, 12, true, 'TEXT', null, null); ?>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>