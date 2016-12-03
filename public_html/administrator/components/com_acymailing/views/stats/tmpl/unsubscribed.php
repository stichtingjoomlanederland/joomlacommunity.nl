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
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=<?php echo $this->app->isAdmin() ? 'stats' : 'frontnewsletter'; ?>" method="post" name="adminForm" id="adminForm">
		<?php if(!$this->app->isAdmin()){ ?>
			<fieldset class="acyheaderarea">
				<div class="acyheader icon-48-stats" style="float: left;"><?php echo(!empty($this->rows) ? $this->rows[0]->subject : JText::_('UNSUBSCRIBECAPTION')); ?></div>
				<div class="toolbar" id="toolbar" style="float: right;">
					<table>
						<tr>
							<?php if(JRequest::getString('tmpl') == 'component' && !empty($this->rows)){ ?>
								<td><a onclick="javascript:submitbutton('export<?php echo ucfirst(JRequest::getCmd('task')); ?>')" href="#"><span class="icon-32-acyexport" title="<?php echo JText::_('ACY_EXPORT', true); ?>"></span><?php echo JText::_('ACY_EXPORT'); ?></a></td>
								<td>
								</td>
							<?php } ?>
							<?php if(JRequest::getInt('fromdetail') == 1){ ?>
								<td><a href="<?php $link = $this->app->isAdmin() ? 'diagram&task=mailing' : 'frontnewsletter&task=stats&listid='.JRequest::getInt('listid');
									echo acymailing_completeLink($link.'&mailid='.JRequest::getInt('filter_mail'), true); ?>"><span class="icon-32-cancel" title="<?php echo JText::_('ACY_CANCEL', true); ?>"></span><?php echo JText::_('ACY_CANCEL'); ?></a></td>
							<?php } ?>
						</tr>
					</table>
				</div>
			</fieldset>
		<?php } ?>


		<table class="acymailing_table_options ">
			<tr>
				<td width="100%">
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td style="padding-left: 15px;">
					<?php echo $this->filterMail; ?>
				</td>
			</tr>
		</table>

		<table class="acymailing_table" cellspacing="1" align="center">
			<thead>
			<tr>
				<th class="title titlenum">
					<?php echo JText::_('ACY_NUM'); ?>
				</th>
				<th class="title titledate">
					<?php echo JHTML::_('grid.sort', JText::_('FIELD_DATE'), 'a.date', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_USER'), 'c.email', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo JText::_('ACY_DETAILS'); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->pagination->getListFooter();
					echo $this->pagination->getResultsCounter();
					if(ACYMAILING_J30) echo '<br />'.$this->pagination->getLimitBox(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			$i = 0;
			foreach($this->rows as $row){
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" valign="top">
						<?php echo $i + 1; ?>
					</td>
					<td align="center" valign="top">
						<?php echo acymailing_getDate($row->date); ?>
					</td>
					<td align="center" style="text-align:center">
						<?php
						$text = '<b>'.JText::_('ACY_NAME').' : </b>'.$row->name;
						$text .= '<br /><b>'.JText::_('ACY_ID').' : </b>'.$row->subid;
						echo acymailing_tooltip($text, $row->email, '', $row->email);
						?>
					</td>
					<td valign="top">
						<?php
						$data = explode("\n", $row->data);
						foreach($data as $value){
							if(!strpos($value, '::')){
								echo $value;
								continue;
							}
							list($part1, $part2) = explode("::", $value);
							if(empty($part2)) continue;
							if(preg_match('#^[A-Z_]*$#', $part2)) $part2 = JText::_($part2);
							echo '<b>'.JText::_($part1).' : </b>'.$part2.'<br />';
						}
						?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
				$i++;
			}
			?>
			</tbody>
		</table>

		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value="<?php echo JRequest::getCmd('task'); ?>"/>
		<input type="hidden" name="defaulttask" value="<?php echo JRequest::getCmd('task'); ?>"/>
		<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
		<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>"/>
		<input type="hidden" name="tmpl" value="component"/>
		<input type="hidden" name="fromdetail" value="<?php echo JRequest::getInt('fromdetail'); ?>"/>
	</form>
</div>
