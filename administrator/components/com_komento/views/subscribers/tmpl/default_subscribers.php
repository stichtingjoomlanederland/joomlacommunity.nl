<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>

<div class="filter-bar">
	<div class="row-table">
		<div class="filter-search form-inline col-cell">
			<?php echo $this->component; ?>
		</div>
	</div>
</div>

<div class="filter-result">
	<table class="table table-striped" cellspacing="1">
		<thead>
			<tr>
				<th width="1%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_NUM' ); ?></th>
				<th width="1%"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
				<th width="10%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_SUBSCRIPTION_TYPE' ); ?></th>
				<th width="10%" class="center" ><?php echo JHTML::_( 'grid.sort', JText::_('COM_KOMENTO_COLUMN_COMPONENT'), 'component', $this->orderDirection, $this->order ); ?></th>
				<th width="10%" class="center" ><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ARTICLE' ), 'cid', $this->orderDirection, $this->order ); ?></th>
				<th width="5%" class="center" ><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_USERID' ), 'userid', $this->orderDirection, $this->order ); ?></th>
				<th width="20%" class="center" ><?php echo JText::_( 'COM_KOMENTO_COLUMN_FULLNAME' ); ?></th>
				<th width="20%" class="center" ><?php echo JText::_( 'COM_KOMENTO_COLUMN_EMAIL' ); ?></th>
				<th width="10%" class="center" ><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_DATE' ), 'created', $this->orderDirection, $this->order ); ?></th>
				<th width="1%" class="center" ><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ID' ) , 'id', $this->orderDirection, $this->order ); ?></th>
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



