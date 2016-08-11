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
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=<?php echo JRequest::getCmd('ctrl'); ?>" method="post" name="adminForm" id="adminForm">
		<?php
		$app = JFactory::getApplication();
		if(!$app->isAdmin()){
			?>
			<fieldset>
				<div class="acyheader icon-48-stats" style="float: left;"><?php echo JText::_('CLICK_STATISTICS'); ?></div>
				<div class="toolbar" id="toolbar" style="float: right;">
					<table>
						<tr>
							<?php if(acymailing_isAllowed($this->config->get('acl_subscriber_export', 'all'))){
								if(ACYMAILING_J16){
									$exportAction = "Joomla.submitbutton('export'); return false;";
								}else $exportAction = "javascript:submitbutton('export')";
								?>
								<td><a onclick="<?php echo $exportAction; ?>" href="#"><span class="icon-32-acyexport" title="<?php echo JText::_('ACY_EXPORT', true); ?>"></span><?php echo JText::_('ACY_EXPORT'); ?></a></td>
							<?php }
							$link = 'frontnewsletter&task=stats&listid='.JRequest::getInt('listid').'&mailid='.JRequest::getInt('mailid'); ?>
							<td><a href="<?php echo acymailing_completeLink($link, true) ?>"><span class="icon-32-cancel" title="<?php echo JText::_('ACY_CANCEL', true); ?>"></span><?php echo JText::_('ACY_CANCEL'); ?></a></td>
						</tr>
					</table>
				</div>
			</fieldset>
		<?php } ?>
		<table width="100%" class="acymailing_table_options">
			<tr>
				<td width="100%">
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td nowrap="nowrap">
					<?php echo $this->filters->mail; ?>
					<?php echo $this->filters->url; ?>
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
					<?php echo JHTML::_('grid.sort', JText::_('CLICK_DATE'), 'a.date', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<?php $selectedMail = JRequest::getInt('filter_mail');
				if(empty($selectedMail)){ ?>
					<th class="title">
						<?php echo JHTML::_('grid.sort', JText::_('JOOMEXT_SUBJECT'), 'b.subject', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
					</th>
				<?php } ?>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('URL'), 'c.name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_USER'), 'd.email', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('TOTAL_HITS'), 'a.click', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
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

			for($i = 0, $a = count($this->rows); $i < $a; $i++){
				$row =& $this->rows[$i];
				$id = 'urlclick'.$i;
				?>
				<tr class="<?php echo "row$k"; ?>" id="<?php echo $id; ?>">
					<td align="center" style="text-align:center">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo acymailing_getDate($row->date); ?>
					</td>
					<?php if(empty($selectedMail)){ ?>
						<td align="center" style="text-align:center">
							<?php
							$text = '<b>'.JText::_('ACY_ID', true).' : </b>'.$row->mailid;
							echo acymailing_tooltip($text, $row->subject, '', $row->subject);
							?>
						</td>
					<?php } ?>
					<td align="center" style="text-align:center">
						<?php if(strlen(strip_tags($row->urlname)) > 50) $row->urlname = substr($row->urlname, 0, 20).'...'.substr($row->urlname, -20); ?>
						<a target="_blank" href="<?php echo strip_tags($row->url); ?>"><?php echo $row->urlname; ?></a>
					</td>
					<td align="center" style="text-align:center">
						<?php
						$text = '<b>'.JText::_('JOOMEXT_NAME', true).' : </b>'.$row->name;
						$text .= '<br /><b>'.JText::_('JOOMEXT_EMAIL', true).' : </b>'.$row->email;
						$text .= '<br /><b>'.JText::_('ACY_ID', true).' : </b>'.$row->subid;
						echo acymailing_tooltip($text, $row->email, '', $row->email);
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->click; ?>
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
		<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
		<input type="hidden" name="defaulttask" value="detaillisting"/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>"/>
		<?php echo JHTML::_('form.token'); ?>
		<?php if(JRequest::getInt('listid')){ ?> <input type="hidden" name="listid" value="<?php echo JRequest::getInt('listid') ?>" /><?php } ?>
		<?php if(JRequest::getCmd('tmpl')){ ?> <input type="hidden" name="tmpl" value="<?php echo JRequest::getCmd('tmpl') ?>" /><?php } ?>
	</form>
</div>
