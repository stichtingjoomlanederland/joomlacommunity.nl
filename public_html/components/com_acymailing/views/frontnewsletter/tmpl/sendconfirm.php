<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
	<?php

	if(!empty($this->values->nbqueue)){
		echo acymailing_display(JText::sprintf('ALREADY_QUEUED', $this->values->nbqueue));
	}elseif(empty($this->lists)){
		echo acymailing_display(JText::_('EMAIL_AFFECT'), 'warning');
	}else{ ?>
		<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm">
			<div>
				<div class="onelineblockoptions">
					<span class="acyblocktitle"><?php echo JText::_('NEWSLETTER_SENT_TO'); ?></span>
					<table class="acymailing_table" cellspacing="1" align="center">
						<tbody>
						<?php
						$k = 0;
						$listids = array();
						foreach($this->lists as $row){
							$listids[] = $row->listid;
							?>
							<tr class="<?php echo "row$k"; ?>">
								<td>
									<?php
									echo acymailing_tooltip($row->description, $row->name, 'tooltip.png', $row->name);
									echo ' ( '.JText::sprintf('SELECTED_USERS', $row->nbsub).' )';
									?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						} ?>
						</tbody>
					</table>
					<?php
					$filterClass = acymailing_get('class.filter');
					if(!empty($this->mail->filter)){
						$resultFilters = $filterClass->displayFilters($this->mail->filter);
						if(!empty($resultFilters)){
							echo '<br />'.JText::_('RECEIVER_LISTS').'<br />'.JText::_('FILTER_ONLY_IF');
							echo '<ul><li>'.implode('</li><li>', $resultFilters).'</li></ul>';
						}
					} ?>
				</div>
				<?php
				$nbTotalReceivers = $nbTotalReceiversAll = $filterClass->countReceivers($listids, $this->mail->filter);
				if(!empty($this->values->alreadySent)){
					$filterClass->onlynew = true;
					$nbTotalReceivers = $nbTotalReceiversAlready = $filterClass->countReceivers($listids, $this->mail->filter, $this->mail->mailid);

					acymailing_display(JText::sprintf('ALREADY_SENT', $this->values->alreadySent).'<br />'.JText::_('REMOVE_ALREADY_SENT').'<br />'.JHTML::_('select.booleanlist', "onlynew", 'onclick="if(this.value == 1){document.getElementById(\'nbreceivers\').innerHTML = \''.$nbTotalReceiversAlready.'\';}else{document.getElementById(\'nbreceivers\').innerHTML = \''.$nbTotalReceiversAll.'\'}"', 1, JText::_('JOOMEXT_YES'), JText::_('SEND_TO_ALL')), 'warning');
				}
				if(empty($this->values->nbqueue)) echo JText::sprintf('SENT_TO_NUMBER', '<span style="font-weight:bold;" id="nbreceivers" >'.$nbTotalReceivers.'</span>').'<br />';
				?>

				<input type="submit" class="btn btn-primary" value="<?php echo JText::_('SEND'); ?>">
			</div>
			<div class="clr"></div>
			<input type="hidden" name="cid[]" value="<?php echo $this->mail->mailid; ?>"/>
			<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
			<input type="hidden" name="task" value="send"/>
			<input type="hidden" name="listid" value="<?php echo JRequest::getInt('listid'); ?>"/>
			<input type="hidden" name="ctrl" value="frontnewsletter"/>
			<input type="hidden" name="tmpl" value="component"/>
			<?php echo JHTML::_('form.token'); ?>
		</form>

	<?php } ?>
</div>
