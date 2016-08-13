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
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=stats" method="post" name="adminForm" id="adminForm">
		<table class="acymailing_table_options">
			<tr>
				<td width="min-width:325px;">
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td align="right">
					<span class="statistics_filter" id="statfilter" align="left"><?php echo $this->filterMsg; ?></span>

				</td>
			</tr>
		</table>

		<table class="acymailing_table" cellpadding="1">
			<thead>
			<tr>
				<th class="title titlenum">
					<?php echo JText::_('ACY_NUM'); ?>
				</th>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="acymailing_js.checkAll(this);"/>
				</th>
				<th class="title statsubjectsenddate">
					<?php echo JHTML::_('grid.sort', JText::_('JOOMEXT_SUBJECT'), 'b.subject', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing').' - '.JHTML::_('grid.sort', JText::_('SEND_DATE'), 'a.senddate', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('OPEN'), 'openprct', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing'); ?>
				</th>
				<?php if(acymailing_level(1)){ ?>
					<th class="title titletoggle">
						<?php echo JHTML::_('grid.sort', JText::_('CLICKED_LINK'), 'clickprct', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing'); ?>
					</th>
					<th class="title titletoggle">
						<?php echo JHTML::_('grid.sort', JText::_('ACY_CLICK_EFFICIENCY'), 'efficiencyprct', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing'); ?>
					</th>
				<?php } ?>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('UNSUBSCRIBE'), 'unsubprct', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing'); ?>
				</th>
				<?php if(acymailing_level(1)){ ?>
					<th class="title titletoggle">
						<?php echo JHTML::_('grid.sort', JText::_('FORWARDED'), 'a.forward', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing'); ?>
					</th>
				<?php } ?>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_SENT'), 'totalsent', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing'); ?>
				</th>
				<?php if(acymailing_level(3)){ ?>
					<th class="title titletoggle">
						<?php echo JHTML::_('grid.sort', JText::_('BOUNCES'), 'bounceprct', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing'); ?>
					</th>
				<?php } ?>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('FAILED'), 'a.fail', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing'); ?>
				</th>
				<?php if(acymailing_level(3)){ ?>
					<th class="title titletoggle" style="font-size: 12px;">
						<?php echo JText::_('STATS_PER_LIST'); ?>
					</th>
				<?php } ?>
				<th class="title titleid titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_ID'), 'a.mailid', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, 'listing'); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="14">
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
				if(acymailing_level(3)){
					$cleanSent = $row->senthtml + $row->senttext - $row->bounceunique;
				}else{
					$cleanSent = $row->senthtml + $row->senttext;
				}
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" style="text-align:center">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo JHTML::_('grid.id', $i, $row->mailid); ?>
					</td>
					<td>
						<?php if(acymailing_level(2)){ ?><a class="modal" href="<?php echo acymailing_completeLink('diagram&task=mailing&mailid='.$row->mailid, true) ?>" rel="{handler: 'iframe', size: {x: 800, y: 590}}"><i class="acyicon-statistic"></i><?php } ?>
							<?php echo '<span class="acy_stat_subject">'.acymailing_tooltip('<b>'.JText::_('JOOMEXT_ALIAS').' : </b>'.$row->alias, ' ', '', $row->subject).'</span>'; ?>
							<?php if(acymailing_level(2)){ ?></a><?php } ?>
						<?php echo '<br /><span class="acy_stat_date"><b>'.JText::_('SEND_DATE').' : </b>'.acymailing_getDate($row->senddate).'</span>'; ?>
					</td>
					<td align="center" style="text-align:center">
						<?php
						if(!empty($row->senthtml)){
							$text = '<b>'.JText::_('OPEN_UNIQUE').' : </b>'.$row->openunique.' / '.$cleanSent;
							$text .= '<br /><b>'.JText::_('OPEN_TOTAL').' : </b>'.$row->opentotal;
							$pourcent = ($cleanSent == 0 ? '0%' : (substr($row->openunique / $cleanSent * 100, 0, 5)).'%');
							$title = JText::sprintf('PERCENT_OPEN', $pourcent);
							echo acymailing_tooltip($text, $title, '', $pourcent, acymailing_completeLink('stats&task=detaillisting&filter_status=open&filter_mail='.$row->mailid));
						}
						?>
					</td>
					<?php if(acymailing_level(1)){ ?>
						<td align="center" style="text-align:center">
							<?php
							if(!empty($row->senthtml)){
								$text = '<b>'.JText::_('UNIQUE_HITS').' : </b>'.$row->clickunique.' / '.$cleanSent;
								$text .= '<br /><b>'.JText::_('TOTAL_HITS').' : </b>'.$row->clicktotal;
								$pourcent = ($cleanSent == 0 ? '0%' : (substr($row->clickunique / $cleanSent * 100, 0, 5)).'%');
								$title = JText::sprintf('PERCENT_CLICK', $pourcent);
								echo acymailing_tooltip($text, $title, '', $pourcent, acymailing_completeLink('statsurl&filter_mail='.$row->mailid));
							}
							?>
						</td>
						<td align="center" style="text-align:center">
							<?php
							if(!empty($row->senthtml)){
								$text = '<b>'.JText::_('UNIQUE_HITS').' : </b>'.$row->clickunique.' / '.$row->openunique;
								$text .= '<br /><b>'.JText::_('OPEN_UNIQUE').' : </b>'.$row->openunique;
								$pourcentEfficiency = ($row->openunique == 0 ? '0%' : (substr($row->clickunique / $row->openunique * 100, 0, 5)).'%');
								$title = JText::sprintf('ACY_CLICK_EFFICIENCY_DESC', $pourcentEfficiency);
								echo acymailing_tooltip($text, $title, '', $pourcentEfficiency, acymailing_completeLink('statsurl&filter_mail='.$row->mailid));
							}
							?>
						</td>
					<?php } ?>
					<td align="center" style="text-align:center">
						<?php echo '<a class="modal" href="'.acymailing_completeLink('stats&task=unsubchart&mailid='.$row->mailid, true).'" rel="{handler: \'iframe\', size: {x: 800, y: 590}}"><i class="acyicon-statistic"></i></a> '; ?>
						<?php $pourcent = ($cleanSent == 0) ? '0%' : (substr($row->unsub / $cleanSent * 100, 0, 5)).'%';
						$text = $row->unsub.' / '.$cleanSent;
						$title = JText::_('UNSUBSCRIBE');
						echo '<a class="modal" href="'.acymailing_completeLink('stats&start=0&task=unsubscribed&filter_mail='.$row->mailid, true).'" rel="{handler: \'iframe\', size: {x: 800, y: 590}}">'.acymailing_tooltip($text, $title, '', $pourcent).'</a>'; ?>
					</td>
					<?php if(acymailing_level(1)){ ?>
						<td align="center" style="text-align:center">
							<?php echo '<a class="modal" href="'.acymailing_completeLink('stats&start=0&task=forward&filter_mail='.$row->mailid, true).'" rel="{handler: \'iframe\', size: {x: 800, y: 590}}">'.$row->forward.'</a>'; ?>
						</td>
					<?php } ?>
					<td align="center" style="text-align:center">
						<?php $text = '<b>'.JText::_('HTML').' : </b>'.$row->senthtml;
						$text .= '<br /><b>'.JText::_('JOOMEXT_TEXT').' : </b>'.$row->senttext;
						$title = JText::_('ACY_SENT');
						echo acymailing_tooltip($text, $title, '', $row->senthtml + $row->senttext, acymailing_completeLink('stats&task=detaillisting&filter_status=0&filter_mail='.$row->mailid)); ?>
					</td>
					<?php if(acymailing_level(3)){ ?>
						<td align="center" style="text-align:center" nowrap="nowrap">
							<?php echo '<a class="modal" href="'.acymailing_completeLink('bounces&task=chart&mailid='.$row->mailid, true).'" rel="{handler: \'iframe\', size: {x: 800, y: 590}}"><i class="acyicon-statistic"></i></a> ';
							$text = $row->bounceunique.' / '.($row->senthtml + $row->senttext);
							$title = JText::_('BOUNCES');
							$pourcent = (empty($row->senthtml) AND empty($row->senttext)) ? '0%' : (substr($row->bounceunique / ($row->senthtml + $row->senttext) * 100, 0, 5)).'%';
							echo acymailing_tooltip($text, $title, '', $pourcent, acymailing_completeLink('stats&task=detaillisting&filter_status=bounce&filter_mail='.$row->mailid)); ?>
						</td>
					<?php } ?>
					<td align="center" style="text-align:center">
						<a href="<?php echo acymailing_completeLink('stats&task=detaillisting&filter_status=failed&filter_mail='.$row->mailid); ?>">
							<?php echo $row->fail; ?>
						</a>
					</td>
					<?php if(acymailing_level(3)){ ?>
						<td align="center" style="text-align:center">
							<?php echo '<a class="modal" href="'.acymailing_completeLink('stats&task=mailinglist&mailid='.$row->mailid, true).'" rel="{handler: \'iframe\', size: {x: 800, y: 590}}"><i class="acyicon-statistic"></i></a>'; ?>
						</td>
					<?php } ?>
					<td align="center" style="text-align:center">
						<?php echo $row->mailid; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
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
