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
	<span class="acyblocktitle"><?php echo JText::_('ACY_CONDITIONS'); ?></span>
	<table cellspacing="1" width="100%">
		<tr>
			<td valign="top" style="width:200px;">
				<?php echo JText::_('ACY_ALLOWED_SENDER'); ?>
			</td>
			<td>
				<?php
				$sender = array();
				$sender[] = JHTML::_('select.option', 'all', JText::_('ACY_ALL'));
				$sender[] = JHTML::_('select.option', 'specific', JText::_('ACY_SPECIFIC'));
				$sender[] = JHTML::_('select.option', 'group', JText::_('ACY_GROUP'));
				$sender[] = JHTML::_('select.option', 'list', JText::_('LIST'));

				echo JHTML::_('select.genericlist', $sender, "data[conditions][sender]", 'size="1" style="width:125px;" onchange="displayAllowedOptions(this.value);"', 'value', 'text', $this->escape(@$this->action->conditions['sender'])).' ';

				echo $this->allowedoptions->specific;
				echo $this->allowedoptions->group;
				echo $this->allowedoptions->list;
				?>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo JText::_('ACY_ALLOWED_SUBJECT'); ?>
			</td>
			<td>
				<?php
				$subjectchoices = array();
				$subjectchoices[] = (object)array('value' => 'all', 'text' => JText::_('ACY_ALL'));
				$subjectchoices[] = (object)array('value' => 'begins', 'text' => JText::_('ACY_BEGINS_WITH'));
				$subjectchoices[] = (object)array('value' => 'ends', 'text' => JText::_('ACY_ENDS_WITH'));
				$subjectchoices[] = (object)array('value' => 'contains', 'text' => JText::_('ACY_CONTAINS'));
				echo JHTML::_('select.genericlist', $subjectchoices, "data[conditions][subject]", 'size="1" style="width:125px;" onchange="if(this.value == \'all\'){document.getElementById(\'dataconditionssubjectvalue\').style.display = \'none\';}else{document.getElementById(\'dataconditionssubjectvalue\').style.display = \'\';}"', 'value', 'text', $this->escape(@$this->action->conditions['subject']));
				$hideSubject = empty($this->action->conditions['subject']) || $this->action->conditions['subject'] == 'all' ? 'display:none;' : '';
				?>
				<input type="text" name="data[conditions][subjectvalue]" id="dataconditionssubjectvalue" value="<?php echo @$this->action->conditions['subjectvalue']; ?>" style="<?php echo $hideSubject; ?>width:200px;" />
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo JText::_('ACY_DELETE_WRONG_EMAILS'); ?>
			</td>
			<td>
				<?php echo JHTML::_('acyselect.booleanlist', "data[action][delete_wrong_emails]", '', @$this->action->delete_wrong_emails); ?>
			</td>
		</tr>
	</table>
</div>
