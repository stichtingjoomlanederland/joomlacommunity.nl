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
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=campaign" method="post" name="adminForm" id="adminForm">
		<table class="acymailing_table_options">
			<tr>
				<td width="100%">
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td nowrap="nowrap">
				</td>
			</tr>
		</table>

		<table class="acymailing_table acymailing_campaigns_listing" cellpadding="1">
			<thead>
			<tr>
				<th class="title titlenum">
					<?php echo JText::_('ACY_NUM'); ?>
				</th>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="acymailing_js.checkAll(this);"/>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_TITLE'), 'a.name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_DESCRIPTION'), 'a.description', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo JText::_('FOLLOWUP'); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('ENABLED'), 'a.published', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_ID'), 'a.listid', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
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
				$publishedid = 'published_'.$row->listid;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" style="text-align:center" valign="top">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td align="center" style="text-align:center" valign="top">
						<?php echo JHTML::_('grid.id', $i, $row->listid); ?>
					</td>
					<td valign="top">
						<a href="<?php echo acymailing_completeLink('campaign&task=edit&listid='.$row->listid); ?>">
							<?php echo acymailing_dispSearch($row->name, $this->pageInfo->search); ?>
						</a>
					</td>
					<td valign="top">
						<?php echo $row->description; ?>
					</td>
					<td style="text-align:center">
						<a href="<?php echo acymailing_completeLink('followup&task=add&campaign='.$row->listid) ?>" class="hasTooltip" data-original-title="<?php echo JText::_('FOLLOWUP_ADD', true) ?>"><i class="acyicon-new" alt="<?php echo JText::_('FOLLOWUP_ADD', true); ?>"></i></a>
						<?php echo JText::sprintf('NUM_FOLLOWUP_CAMPAIGN', '<span id="followupCount_'.$row->listid.'">'.count($row->followup).'</span>');
						$refreshCount = 'document.getElementById(\'followupCount_'.$row->listid.'\').innerHTML=document.getElementById(\'followupCount_'.$row->listid.'\').innerHTML-1;';
						if(!empty($row->followup)){
							echo '<div class="acyblockoptions"><table width="100%" style="padding-left:50px">';
							foreach($row->followup as $oneFollow){
								$publishedidfollow = 'published_'.$oneFollow->mailid.'_followup';
								$iddelete = 'followup_'.$oneFollow->mailid;
								$copyButton = '<a href="index.php?option='.ACYMAILING_COMPONENT.'&ctrl=followup&task=copy&followupid='.$oneFollow->mailid.'&'.acymailing_getFormToken().'=1"><i class="hasTooltip acyicon-copy" data-original-title="'.JText::_('ACY_COPY').'" alt="'.JText::_('ACY_COPY').'" title="'.JText::_('ACY_COPY').'"></i></a>';
								$statButton = '<span class="acystatsbutton" style="margin-right:12px;">&nbsp;</span>';
								if(acymailing_isAllowed($this->config->get('acl_statistics_manage', 'all'))){
									$urlStat = acymailing_completeLink('diagram&task=mailing&mailid='.$oneFollow->mailid, true);
									$statButton = '<span class="acystatsbutton"><a class="modal" rel="{handler: \'iframe\', size: {x: 800, y: 590}}" href="'.$urlStat.'"><i class="hasTooltip acyicon-statistic" data-original-title="'.JText::_('STATISTICS', true).'" alt="'.JText::_('STATISTICS', true).'" ></i></a></span>';
								}
								echo '<tr id="'.$iddelete.'"><td width="100px" align="right">'.$this->delay->display($oneFollow->senddate).'</td><td width="50%" align="left"><span id="'.$publishedidfollow.'" class="spanloading" style="padding:2px 20px;width:65px;white-space: nowrap">'.$this->toggleClass->display('published', (int)$oneFollow->published).'</span><a title="'.JText::_('ACY_EDIT', true).'" href="'.acymailing_completeLink('followup&task=edit&campaign='.$row->listid.'&mailid=').$oneFollow->mailid.'">'.$oneFollow->subject.'</a></td><td class="titletoggle" align="center">'.$statButton.' '.$copyButton.' '.$this->toggleClass->delete($iddelete, $row->listid.'_'.$oneFollow->mailid, 'followup', true, '', $refreshCount).'</td></tr>';
							}
							echo '</table></div>';
						} ?>
					</td>
					<td align="center" style="text-align:center" valign="top">
						<span id="<?php echo $publishedid ?>" class="loading"><?php echo $this->toggleClass->toggle($publishedid, $row->published, 'list') ?></span>
					</td>
					<td align="center" style="text-align:center" valign="top">
						<?php echo $row->listid; ?>
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
