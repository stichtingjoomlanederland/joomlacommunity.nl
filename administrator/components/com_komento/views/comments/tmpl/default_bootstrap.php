<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
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

				<?php echo $this->state; ?>
				<hr class="hr-condensed" />

				<?php echo $this->component; ?>
				<hr class="hr-condensed" />

				<?php echo $this->getColumnsState(); ?>
			</div>
		</div>
	</div>

	<div class="span10">
		<div class="filter-bar">
			<div class="filter-search input-append pull-left">
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->search); ?>" class="inputbox" onchange="document.adminForm.submit();" />
				<button class="btn btn-primary" onclick="this.form.submit();"><?php echo JText::_( 'COM_KOMENTO_COMMENTS_SEARCH' ); ?></button>
				<button class="btn" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KOMENTO_RESET_BUTTON' ); ?></button>
			</div>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>

		<table class="table table-striped" cellspacing="1">
		<thead>
			<tr>
				<th width="1%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_NUM' ); ?></th>
				<th width="1%"><input type="checkbox" id="toggle" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>

				<?php if( $this->columns->get( 'column_comment' ) ) { ?>
				<th width="30%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_COMMENT' ); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_published' ) ) { ?>
				<th width="5%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_STATUS' ), 'published', $this->orderDirection, $this->order ); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_sticked' ) ) { ?>
				<th width="5%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_STICKED' ), 'sticked', $this->orderDirection, $this->order ); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_link' ) ) { ?>
				<th widht="10%" class="center">
				<?php
				if( !$this->search )
				{
					echo JText::_('COM_KOMENTO_COLUMN_COMMENT_CHILD');
				}
				else
				{
					echo JText::_('COM_KOMENTO_COLUMN_COMMENT_PARENT');
				}
				?>
				</th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_edit' ) ) { ?>
				<th width="5%" class="center"><?php echo JText::_('COM_KOMENTO_COLUMN_EDIT'); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_component' ) ) { ?>
				<th width="10%" class="center"><?php echo JText::_('COM_KOMENTO_COLUMN_COMPONENT'); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_article' ) ) { ?>
				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ARTICLE' ), 'cid', $this->orderDirection, $this->order ); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_cid' ) ) { ?>
				<th width="5%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ARTICLEID' ), 'cid', $this->orderDirection, $this->order ); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_date' ) ) { ?>
				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_DATE' ), 'created', $this->orderDirection, $this->order ); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_author' ) ) { ?>
				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_AUTHOR' ) , 'created_by', $this->orderDirection, $this->order ); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_email' ) ) { ?>
				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_EMAIL' ) , 'email', $this->orderDirection, $this->order ); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_homepage' ) ) { ?>
				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_HOMEPAGE' ) , 'url', $this->orderDirection, $this->order ); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_ip' ) ) { ?>
				<th width="10%" class="center"><?php echo JText::_('COM_KOMENTO_COLUMN_IP'); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_latitude' ) ) { ?>
				<th width="10%" class="center"><?php echo JText::_('COM_KOMENTO_COLUMN_LATITUDE'); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_longitude' ) ) { ?>
				<th width="10%" class="center"><?php echo JText::_('COM_KOMENTO_COLUMN_LONGITUDE'); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_address' ) ) { ?>
				<th width="20%" class="center"><?php echo JText::_('COM_KOMENTO_COLUMN_ADDRESS'); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>

				<?php if( $this->columns->get( 'column_id' ) ) { ?>
				<th width="1%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ID' ) , 'id', $this->orderDirection, $this->order ); ?></th>
				<?php $this->columnCount++; ?>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php echo $this->loadTemplate( 'list_bootstrap' ); ?>
		</tbody>

		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>
</div>
