<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="onelineblockoptions" id="executed_actions_block">
	<span class="acyblocktitle"><?php echo JText::_('ACTIONS'); ?></span>
	<?php echo JText::_('ACY_DISTRIBUTION_DELETE_MESSAGE'); ?>
	<div id="actionsarea"></div>
	<button class="acymailing_button" onclick="addAction();return false;"><?php echo JText::_('ADD_ACTION'); ?></button>
	<table cellspacing="1" style="margin-top:30px;">
		<tr>
			<td class="acykey" style="padding-right:20px;">
				<label for="self_signed">
					<?php echo acymailing_tooltip(JText::_('ACTION_SENDER_FROM_DESC').'<br /><strong>'.JText::_('ACTION_SENDER_ADDRESS').'</strong>', '', '', JText::_('ACTION_SENDER_FROM')); ?>
				</label>
			</td>
			<td>
				<?php echo JHTML::_('acyselect.booleanlist', "data[action][senderfrom]", '', @$this->action->senderfrom); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey" style="padding-right:20px;">
				<label for="self_signed">
					<?php echo acymailing_tooltip(JText::_('ACTION_SENDER_TO_DESC').'<br /><strong>'.JText::_('ACTION_SENDER_ADDRESS').'</strong>', '', '', JText::_('ACTION_SENDER_TO')); ?>
				</label>
			</td>
			<td>
				<?php echo JHTML::_('acyselect.booleanlist', "data[action][senderto]", '', @$this->action->senderto); ?>
			</td>
		</tr>
	</table>
</div>
