<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><form action="<?php echo JRoute::_('index.php?option=com_acymailing&ctrl='.$this->ctrl); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" <?php if(in_array($this->type, array('news', 'autonews'))){
	if(!isset($this->app) || $this->app->isAdmin()) echo 'style="width:42%;min-width:480px;float:left;margin-right:15px;"';
} ?>>
	<div class="<?php if(!isset($this->app) || $this->app->isAdmin()){
		echo 'acyblockoptions';
	}else{
		echo 'onelineblockoptions';
	} ?> acyblock_newsletter" id="sendatest">
		<span class="acyblocktitle"><?php echo JText::_('SEND_TEST'); ?></span>

		<table width="100%">
			<tr>
				<td valign="top" width="100px;" nowrap="nowrap">
					<?php echo JText::_('SEND_TEST_TO'); ?>
				</td>
				<td>
					<?php echo $this->testreceiverType->display($this->infos->test_selection, $this->infos->test_group, $this->infos->test_emails); ?>
				</td>
			</tr>
			<tr>
				<td nowrap="nowrap">
					<?php echo JText::_('SEND_VERSION'); ?>
				</td>
				<td>
					<?php if($this->mail->html){
						echo JHTML::_('acyselect.booleanlist', 'test_html', '', $this->infos->test_html, JText::_('HTML'), JText::_('JOOMEXT_TEXT'));
					}else{
						echo JText::_('JOOMEXT_TEXT');
						echo '<input type="hidden" name="test_html" value="0" />';
					} ?>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo JText::_('SEND_COMMENT'); ?></td>
				<td>
					<div><textarea placeholder="<?php echo JText::_('SEND_COMMENT_DESC'); ?>" name="commentTest" id="commentTest" style="width:90%;height:80px;"><?php echo JRequest::getString('commentTest', ''); ?></textarea></div>
				</td>
			</tr>
			<tr>
				<td>

				</td>
				<td style="padding-top:10px;">
					<button type="submit" class="acymailing_button" onclick="var val = document.getElementById('message_receivers').value; if(val != ''){ setUser(val); }"><?php echo JText::_('SEND_TEST') ?></button>
				</td>
			</tr>
		</table>
	</div>
	<input type="hidden" name="cid[]" value="<?php echo $this->mail->mailid; ?>"/>
	<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
	<?php if(!empty($this->lists)){
		$firstList = reset($this->lists);
		$myListId = $firstList->listid;
	}else{
		$myListId = JRequest::getInt('listid', 0);
	}
	if(!empty($myListId)){
		?> <input type="hidden" name="listid" value="<?php echo $myListId; ?>"/> <?php } ?>
	<input type="hidden" name="task" value="sendtest"/>
	<input type="hidden" name="ctrl" value="<?php echo $this->ctrl; ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
