<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="onelineblockoptions">
	<span class="acyblocktitle"><?php echo JText::_('FE_EDITION'); ?></span>
	<table class="acymailing_table" cellspacing="1">
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('DEFAULT_SENDER_DESC'), JText::_('DEFAULT_SENDER'), '', JText::_('DEFAULT_SENDER')); ?>
			</td>
			<td>
				<?php echo JHTML::_('acyselect.booleanlist', "config[frontend_sender]", '', $this->config->get('frontend_sender', 0)); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('DEFAULT_REPLY_DESC'), JText::_('DEFAULT_REPLY'), '', JText::_('DEFAULT_REPLY')); ?>
			</td>
			<td>
				<?php echo JHTML::_('acyselect.booleanlist', "config[frontend_reply]", '', $this->config->get('frontend_reply', 0)); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('FE_MODIFICATION_DESC'), JText::_('FE_MODIFICATION'), '', JText::_('FE_MODIFICATION')); ?>
			</td>
			<td>
				<?php echo JHTML::_('acyselect.booleanlist', "config[frontend_modif]", '', $this->config->get('frontend_modif', 1)); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('FE_MODIFICATION_SENT_DESC'), JText::_('FE_MODIFICATION_SENT'), '', JText::_('FE_MODIFICATION_SENT')); ?>
			</td>
			<td>
				<?php echo JHTML::_('acyselect.booleanlist', "config[frontend_modif_sent]", '', $this->config->get('frontend_modif_sent', 1)); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('ACY_FE_DELETE_BUTTON_DESC'), JText::_('ACY_FE_DELETE_BUTTON'), '', JText::_('ACY_FE_DELETE_BUTTON')); ?>
			</td>
			<td>
				<?php $deleteButton = array(JHTML::_('select.option', "delete", JText::_('DELETE_USER')), JHTML::_('select.option', "unsub", JText::_('UNSUB_USER')));
				echo JHTML::_('acyselect.radiolist', $deleteButton, 'config[frontend_delete_button]', '', 'value', 'text', $this->config->get('frontend_delete_button', 'delete')); ?>
			</td>
		</tr>
	</table>
</div>
