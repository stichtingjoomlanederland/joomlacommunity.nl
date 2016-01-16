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
<div class="adminform-head">
	<table class="adminform">
		<tr>
			<td width="50%">
				<button type="button" onclick="Komento.showColumnsConfiguration()"><?php echo JText::_( 'COM_KOMENTO_COMMENTS_CONFIGURE_COLUMNS' ); ?></button>
				<label><?php echo JText::_( 'COM_KOMENTO_COMMENTS_SEARCH' ); ?> :</label>
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->search); ?>" class="inputbox" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'COM_KOMENTO_COMMENTS_SEARCH' ); ?></button>
				<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KOMENTO_RESET_BUTTON' ); ?></button>
			</td>
			<td width="50%" style="text-align: right;">
				<label><?php echo JText::_( 'COM_KOMENTO_COMMENTS_FILTER_STATUS' ); ?> :</label>
				<?php echo $this->state; ?>
				<label><?php echo JText::_( 'COM_KOMENTO_COMMENTS_FILTER_COMPONENT' ); ?> :</label>
				<?php echo $this->component; ?>
			</td>
		</tr>
		<tr class="columnsConfiguration" style="display: none;">
			<td>
				<?php echo $this->getColumnsState(); ?>
			</td>
			<td></td>
		</tr>
	</table>
</div>

<div class="adminform-body">
<table class="adminlist" cellspacing="1">
<thead>

	<tr>
		<th width="1%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_NUM' ); ?></th>
		<th width="1%"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->comments ); ?>);" /></th>

		<?php if( $this->columns->get( 'column_comment' ) ) { ?>
		<th width="30%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_COMMENT' ); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_published' ) ) { ?>
		<th width="5%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_STATUS' ), 'published', $this->orderDirection, $this->order ); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<th width="5%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_REPORT_COUNT' ) , 'reports', $this->orderDirection, $this->order ); ?></th>

		<?php if( $this->columns->get( 'column_edit' ) ) { ?>
		<th width="5%"><?php echo JText::_('COM_KOMENTO_COLUMN_EDIT'); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_component' ) ) { ?>
		<th width="10%"><?php echo JText::_('COM_KOMENTO_COLUMN_COMPONENT'); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_article' ) ) { ?>
		<th width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ARTICLE' ), 'cid', $this->orderDirection, $this->order ); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_cid' ) ) { ?>
		<th width="5%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ARTICLEID' ), 'cid', $this->orderDirection, $this->order ); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_date' ) ) { ?>
		<th width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_DATE' ), 'created', $this->orderDirection, $this->order ); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_author' ) ) { ?>
		<th width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_AUTHOR' ) , 'created_by', $this->orderDirection, $this->order ); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_email' ) ) { ?>
		<th width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_EMAIL' ) , 'email', $this->orderDirection, $this->order ); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_homepage' ) ) { ?>
		<th width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_HOMEPAGE' ) , 'url', $this->orderDirection, $this->order ); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_ip' ) ) { ?>
		<th width="10%"><?php echo JText::_('COM_KOMENTO_COLUMN_IP'); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_latitude' ) ) { ?>
		<th width="10%"><?php echo JText::_('COM_KOMENTO_COLUMN_LATITUDE'); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_longitude' ) ) { ?>
		<th width="10%"><?php echo JText::_('COM_KOMENTO_COLUMN_LONGITUDE'); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_address' ) ) { ?>
		<th width="20%"><?php echo JText::_('COM_KOMENTO_COLUMN_ADDRESS'); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>

		<?php if( $this->columns->get( 'column_id' ) ) { ?>
		<th width="1%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ID' ) , 'id', $this->orderDirection, $this->order ); ?></th>
		<?php $this->columnCount++; ?>
		<?php } ?>
	</tr>
</thead>
<tbody>
	<?php echo $this->loadTemplate( 'list_joomla' ); ?>
</tbody>
<tfoot>
	<tr>
		<td colspan="<?php echo $this->columnCount; ?>">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
</table>
</div>
