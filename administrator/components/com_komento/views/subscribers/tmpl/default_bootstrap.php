<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access'); ?>
<div class="row-fluid">
	<div class="span2">
		<div id="sidebar">
			<h4 class="page-header"><?php echo JText::_( 'COM_KOMENTO_FILTER' ); ?>:</h4>

			<div class="filter-select hidden-phone">
				<?php echo $this->component; ?>
				<hr class="hr-condensed" />
			</div>
		</div>
	</div>
	<div class="span10">
		<table class="table table-stripped" cellspacing="1">
		<thead>

			<!--

			Row
				Number
				Checkbox
				Type
				Component
				Article Id
				UserId
				FullName
				Email
				Date
				Id

			-->

			<tr>
				<th width="1%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_NUM' ); ?></th>
				<th width="1%"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
				<th width="10%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_SUBSCRIPTION_TYPE' ); ?></th>
				<th width="10%"><?php echo JHTML::_( 'grid.sort', JText::_('COM_KOMENTO_COLUMN_COMPONENT'), 'component', $this->orderDirection, $this->order ); ?></th>
				<th width="10%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ARTICLE' ), 'cid', $this->orderDirection, $this->order ); ?></th>
				<th width="5%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_USERID' ), 'userid', $this->orderDirection, $this->order ); ?></th>
				<th width="20%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_FULLNAME' ); ?></th>
				<th width="20%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_EMAIL' ); ?></th>
				<th width="10%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_DATE' ), 'created', $this->orderDirection, $this->order ); ?></th>
				<th width="1%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ID' ) , 'id', $this->orderDirection, $this->order ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if($this->subscribers)
		{
			$k = 0;
			$x = 0;
			$i = 0;
			$n = count($this->subscribers);
			$config = JFactory::getConfig();

			foreach($this->subscribers as $row) { ?>
			<!--

			Row
				Number
				Checkbox
				Type
				Component
				Article Id
				UserId
				FullName
				Email
				Date
				Id

			-->

				<tr id="<?php echo 'kmt-' . $row->id; ?>" class="<?php echo "row$k"; ?>">
					<!-- Number -->
					<td class="center">
						<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>

					<!-- Checkbox -->
					<td class="center">
						<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
					</td>

					<!-- Subscription Type -->
					<td class="center">
						<?php echo JText::_( 'COM_KOMENTO_SUBSCRIPTION_' . strtoupper( $row->type ) );?>
					</td>

					<!-- Component -->
					<td class="center">
						<?php echo $row->componenttitle; ?>
					</td>

					<!-- Article Title -->
					<td class="center">
						<?php echo $row->contenttitle; ?>
					</td>

					<!-- UserId -->
					<td class="center">
						<?php echo $row->userid; ?>
					</td>

					<!-- Fullname -->
					<td class="center">
						<?php echo $row->fullname; ?>
					</td>

					<!-- Email -->
					<td class="center">
						<?php echo $row->email; ?>
					</td>

					<!-- Date -->
					<td class="center">
						<?php echo $row->created;?>
					</td>

					<!-- ID -->
					<td class="center">
						<?php echo $row->id; ?>
					</td>
				</tr>
		<?php
			$k = 1 - $k;
			$i++;
			}

		} else { ?>
			<tr>
				<td colspan="10" class="center">
					<?php echo JText::_( 'COM_KOMENTO_SUBSCRIBERS_NO_SUBSCRIBERS' ); ?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>
</div>

