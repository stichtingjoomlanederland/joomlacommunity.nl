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
	<?php
	$saveOrder = ($this->pageInfo->filter->order->value == 'a.ordering' ? true : false);
	if(ACYMAILING_J30 && $saveOrder){
		$saveOrderingUrl = 'index.php?option=com_acymailing&task=saveorder&tmpl=component';
		JHtml::_('sortablelist.sortable', 'templateListing', 'adminForm', strtolower($this->pageInfo->filter->order->dir), $saveOrderingUrl);
	}
	?>
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=template" method="post" name="adminForm" id="adminForm">
		<table class="acymailing_table_options">
			<tr>
				<td width="100%">
					<?php acymailing_listingsearch($this->pageInfo->search); ?>
				</td>
				<td nowrap="nowrap">
					<?php
					if(acymailing_level(3)){
						echo $this->filters->category;
					}
					?>
				</td>
			</tr>
		</table>

		<table class="acymailing_table" cellpadding="1" id="templateListing">
			<thead>
			<tr>
				<th class="title titlenum">
					<?php echo JText::_('ACY_NUM'); ?>
				</th>
				<?php if(ACYMAILING_J30){ ?>
					<th class="title titleorder" style="width:32px !important; padding-left:1px; padding-right:1px;">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
				<?php } ?>
				<th class="title titlebox">
					<input type="checkbox" name="toggle" value="" onclick="acymailing_js.checkAll(this);"/>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_TEMPLATE'), 'a.name', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<?php if(!ACYMAILING_J30){ ?>
					<th class="title titleorder">
						<?php echo JHTML::_('grid.sort', JText::_('ACY_ORDERING'), 'a.ordering', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
						<?php if($this->order->ordering) echo JHTML::_('grid.order', $this->rows); ?>
					</th>
				<?php } ?>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_DEFAULT'), 'a.premium', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titletoggle">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_PUBLISHED'), 'a.published', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_ID'), 'a.tempid', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value); ?>
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

				$publishedid = 'published_'.$row->tempid;
				$premiumid = 'premium_'.$row->tempid;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" style="text-align:center;">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<?php if(ACYMAILING_J30){ ?>
						<td class="order">
							<?php $iconClass = '';
							if(!$saveOrder) $iconClass = ' inactive tip-top hasTooltip" title="'.JHtml::tooltipText('JORDERINGDISABLED'); ?>
							<span class="sortable-handler<?php echo $iconClass ?>">
							<i class="icon-menu"></i>
						</span>
							<?php if($saveOrder){ ?>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order"/>
							<?php } ?>
						</td>
					<?php } ?>
					<td align="center" style="text-align:center;">
						<?php echo JHTML::_('grid.id', $i, $row->tempid); ?>
					</td>
					<td>
						<?php if(!empty($row->thumb)){ ?>
							<a href="<?php echo acymailing_completeLink('template&task=edit&tempid='.$row->tempid); ?>">
								<img class="template_thumbnail" src="<?php echo rtrim(JURI::root(), '/').'/'.strip_tags($row->thumb) ?>" style="float:left;width:100px;margin-right:10px;"/>
							</a>
						<?php } ?>
						<a href="<?php echo acymailing_completeLink('template&task=edit&tempid='.$row->tempid); ?>"><?php echo acymailing_dispSearch($row->name, $this->pageInfo->search); ?></a><br/>
						<?php echo acymailing_absoluteURL(nl2br($row->description)); ?>
					</td>
					<?php if(!ACYMAILING_J30){ ?>
						<td class="order" style="text-align:center;">
							<span><?php echo $this->pagination->orderUpIcon($i, $this->order->reverse XOR ($row->ordering >= @$this->rows[$i - 1]->ordering), $this->order->orderUp, 'Move Up', $this->order->ordering); ?></span>
							<span><?php echo $this->pagination->orderDownIcon($i, $a, $this->order->reverse XOR ($row->ordering <= @$this->rows[$i + 1]->ordering), $this->order->orderDown, 'Move Down', $this->order->ordering); ?></span>
							<input type="text" name="order[]" size="5" <?php if(!$this->order->ordering) echo 'disabled="disabled"' ?> value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center"/>
						</td>
					<?php } ?>
					<td align="center" style="text-align:center;">
						<span id="<?php echo $premiumid ?>"><?php echo $this->toggleClass->toggle($premiumid, $row->premium, 'template') ?></span>
					</td>
					<td align="center" style="text-align:center;">
						<span id="<?php echo $publishedid ?>"><?php echo $this->toggleClass->toggle($publishedid, $row->published, 'template') ?></span>
					</td>
					<td width="1%" align="center" style="text-align:center;">
						<?php echo acymailing_dispSearch($row->tempid, $this->pageInfo->search); ?>
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
