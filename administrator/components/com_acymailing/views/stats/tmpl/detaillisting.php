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
	<?php $app = JFactory::getApplication();
	if(JRequest::getString('tmpl') == 'component' && !$app->isAdmin()) include(dirname(__FILE__).DS.'menu.detaillisting.php') ?>
	<div id="iframedoc"></div>
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=stats" method="post" name="adminForm" id="adminForm">
		<table class="acymailing_table_options">
			<tr>
				<td width="100%">
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $this->filters->status; ?>
					<?php echo $this->filters->mail; ?>
					<?php echo $this->filters->bounce; ?>
				</td>
			</tr>
		</table>

		<table class="acymailing_table" cellpadding="1">
			<thead>
			<tr>
				<th class="title titlenum">
					<?php echo JText::_('ACY_NUM'); ?>
				</th>
				<th class="title titledate">
					<?php echo JHTML::_('grid.sort', JText::_('SEND_DATE'), 'a.senddate', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, JRequest::getCmd('task')); ?>
				</th>
				<?php $selectedMail = JRequest::getInt('filter_mail');
				if(empty($selectedMail)){ ?>
					<th class="title">
						<?php echo JHTML::_('grid.sort', JText::_('JOOMEXT_SUBJECT'), 'b.subject', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, JRequest::getCmd('task')); ?>
					</th>
				<?php } ?>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_USER'), 'c.email', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, JRequest::getCmd('task')); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('RECEIVED_VERSION'), 'a.html', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, JRequest::getCmd('task')); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('OPEN'), 'a.open', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, JRequest::getCmd('task')); ?>
				</th>
				<th class="title titledate">
					<?php echo JHTML::_('grid.sort', JText::_('OPEN_DATE'), 'a.opendate', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, JRequest::getCmd('task')); ?>
				</th>
				<?php if(acymailing_level(3)){ ?>
					<th class="title titletoggle">
						<?php echo JHTML::_('grid.sort', JText::_('BOUNCES'), 'a.bounce', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, JRequest::getCmd('task')); ?>
					</th>
				<?php } ?>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_SENT'), 'a.sent', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, JRequest::getCmd('task')); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter();
					echo $this->pagination->getResultsCounter();
					if(ACYMAILING_J30) echo '<br />'.$this->pagination->getLimitBox(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			$tmpl = (JRequest::getString('tmpl') == 'component') ? true : false;
			for($i = 0, $a = count($this->rows); $i < $a; $i++){
				$row =& $this->rows[$i];
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" style="text-align:center">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo acymailing_getDate($row->senddate); ?>
					</td>
					<?php if(empty($selectedMail)){ ?>
						<td>
							<?php
							$text = '<b>'.JText::_('ACY_ID').' : </b>'.$row->mailid;
							$text .= '<br /><b>'.JText::_('JOOMEXT_ALIAS').' : </b>'.$row->alias;

							if($row->type == 'followup'){
								$ctrl = 'followup';
							}else{
								$ctrl = 'newsletter';
							}
							echo acymailing_tooltip($text, $row->subject, '', $row->subject, acymailing_completeLink($ctrl.'&task=preview&mailid='.$row->mailid));
							?>
						</td>
					<?php } ?>
					<td>
						<?php
						$text = '<b>'.JText::_('ACY_NAME').' : </b>'.$row->name;
						$text .= '<br /><b>'.JText::_('ACY_ID').' : </b>'.$row->subid;
						$link = $tmpl ? '' : acymailing_completeLink('subscriber&task=edit&subid='.$row->subid);
						echo acymailing_tooltip($text, $row->email, '', $row->name.' ( '.$row->email.' )', $link);
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->html ? JText::_('HTML') : JText::_('JOOMEXT_TEXT'); ?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->open; ?>
					</td>
					<td align="center" style="text-align:center">
						<?php if(!empty($row->opendate)) echo acymailing_getDate($row->opendate); ?>
					</td>
					<?php if(acymailing_level(3)){ ?>
						<td align="center" style="text-align:center">
							<?php
							if($row->bounce == 0){
								echo $row->bounce;
							}else{
								if(empty($row->bouncerule)){
									$text = JText::_('NO_RULE_SAVED');
								}else{
									$found = preg_match('#^([A-Z0-9_]*) \[#Uis', $row->bouncerule, $match);
									$text = $found ? str_replace($match[1], JText::_($match[1]), $row->bouncerule) : $row->bouncerule;
								}
								echo acymailing_tooltip($text, JText::_('ACY_RULE'), '', $row->bounce);
							} ?>
						</td>
					<?php } ?>
					<td align="center" style="text-align:center" title="<?php echo JText::_('ACY_SENT').': '.$row->sent.' - '.JText::_('FAILED').': '.$row->fail; ?>">
						<?php echo $this->toggleClass->display('visible', empty($row->fail) ? true : false); ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>

		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value="<?php echo JRequest::getCmd('task'); ?>"/>
		<input type="hidden" name="defaulttask" value="detaillisting"/>
		<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>"/>

		<?php echo JHTML::_('form.token'); ?>
		<?php if($tmpl){ ?>
			<input type="hidden" name="tmpl" value="component"/>
		<?php }
		if(JRequest::getInt('listid')){ ?>
			<input type="hidden" name="listid" value="<?php echo JRequest::getInt('listid'); ?>"/>
		<?php } ?>
	</form>
</div>
