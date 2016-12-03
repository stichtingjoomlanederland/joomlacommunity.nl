<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><fieldset id="acy_subscriber_form_menu">
	<div class="toolbar" id="acytoolbar" style="float: right;">
		<table><tr>
		<?php $config = acymailing_config(); if(acymailing_isAllowed($config->get('acl_newsletters_schedule','all')) && !empty($this->subscriber->subid)){ ?>
		<td id="acybutton_subscriber_send"><a class="modal" rel="{handler: 'iframe', size: {x: 640, y: 480}}" href="<?php echo JRoute::_('index.php?option=com_acymailing&ctrl=frontsubscriber&task=addqueue&tmpl=component&subid='.$this->subscriber->subid); ?>"><span class="icon-32-acysend" title="<?php echo JText::_('SEND'); ?>"></span><?php echo JText::_('SEND'); ?></a></td>
		<?php } ?>
		<td id="acybutton_subscriber_save"><a onclick="javascript:<?php if(ACYMAILING_J16) echo "Joomla."; ?>submitbutton('save'); return false;" href="#" ><span class="icon-32-save" title="<?php echo JText::_('ACY_SAVE'); ?>"></span><?php echo JText::_('ACY_SAVE'); ?></a></td>
		<td id="acybutton_subscriber_apply"><a onclick="javascript:<?php if(ACYMAILING_J16) echo "Joomla."; ?>submitbutton('apply'); return false;" href="#" ><span class="icon-32-apply" title="<?php echo JText::_('ACY_APPLY'); ?>"></span><?php echo JText::_('ACY_APPLY'); ?></a></td>
		<td id="acybutton_subscriber_cancel"><a onclick="javascript:<?php if(ACYMAILING_J16) echo "Joomla."; ?>submitbutton('cancel'); return false;" href="#" ><span class="icon-32-cancel" title="<?php echo JText::_('ACY_CANCEL'); ?>"></span><?php echo JText::_('ACY_CANCEL'); ?></a></td>
		</tr></table>
	</div>
	<div class="acyheader" style="float: left;"><h1><?php echo JText::_('ACY_USER'); ?></h1></div>
</fieldset>
<?php
include(ACYMAILING_BACK.'views'.DS.'subscriber'.DS.'tmpl'.DS.'form.php');
