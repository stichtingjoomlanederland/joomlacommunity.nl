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
	<div id="iframedoc"></div>
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=<?php echo JRequest::getCmd('ctrl'); ?>" method="post" name="adminForm" id="adminForm">
		<table class="acymailing_table_options">
			<tr>
				<td width="100%">
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td nowrap="nowrap">
					<?php if(!empty($this->filters->category)) echo $this->filters->category; ?>
				</td>
			</tr>
		</table>
		<table class="acymailing_table" cellpadding="1">
			<thead>
			<tr>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="acymailing_js.checkAll(this);"/>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('JOOMEXT_SUBJECT'), 'subject', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('JOOMEXT_ALIAS'), 'alias', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titlesender">
					<?php echo JHTML::_('grid.sort', JText::_('SENDER_INFORMATIONS'), 'fromname', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_PUBLISHED'), 'published', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_ID'), 'mailid', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter();
					echo $this->pagination->getResultsCounter();
					if(ACYMAILING_J30) echo '<br />'.$this->pagination->getLimitBox(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;

			for($i = 0, $a = count($this->rows); $i < $a; $i++){
				$row =& $this->rows[$i];
				$publishedid = 'published_'.$row->mailid;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" style="text-align:center">
						<?php echo JHTML::_('grid.id', $i, $row->mailid); ?>
					</td>
					<td>
						<?php
						$subjectLine = str_replace('<ADV>', $this->escape('<ADV>'), $row->subject);
						echo acymailing_tooltip('<b>'.JText::_('JOOMEXT_ALIAS').' : </b>'.$row->alias, ' ', '', $subjectLine, acymailing_completeLink('notification&task=edit&mailid='.$row->mailid)); ?>
					</td>
					<td><?php echo $row->alias; ?></td>
					<td align="center" style="text-align:center">
						<?php
						if(empty($row->fromname)) $row->fromname = $this->config->get('from_name');
						if(empty($row->fromemail)) $row->fromemail = $this->config->get('from_email');
						if(empty($row->replyname)) $row->replyname = $this->config->get('reply_name');
						if(empty($row->replyemail)) $row->replyemail = $this->config->get('reply_email');
						if(!empty($row->fromname)){
							$text = '<b>'.JText::_('FROM_NAME').' : </b>'.$row->fromname;
							$text .= '<br /><b>'.JText::_('FROM_ADDRESS').' : </b>'.$row->fromemail;
							$text .= '<br /><br /><b>'.JText::_('REPLYTO_NAME').' : </b>'.$row->replyname;
							$text .= '<br /><b>'.JText::_('REPLYTO_ADDRESS').' : </b>'.$row->replyemail;
							echo acymailing_tooltip($text, ' ', '', $row->fromname);
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<span id="<?php echo $publishedid ?>" class="loading"><?php echo $this->toggleClass->toggle($publishedid, (int)$row->published, 'mail') ?></span>
					</td>
					<td width="1%" align="center">
						<?php echo $row->mailid; ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
