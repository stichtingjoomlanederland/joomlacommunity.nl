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
			<?php echo $this->state; ?>
			<?php echo $this->component; ?>
			<div class="input-group">
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->search); ?>" class="inputbox" onchange="document.adminForm.submit();" />
				<div class="input-group-btn">
					<button class="btn btn-default" onclick="this.form.submit();"><?php echo JText::_( 'COM_KOMENTO_COMMENTS_SEARCH' ); ?></button>
					<button class="btn btn-default" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_KOMENTO_RESET_BUTTON' ); ?></button>
				</div>
			</div>
		</div>

		<div class="filter-limit col-cell">
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
</div>
<div class="filter-result">
	<table class="table table-striped" cellspacing="1">
		<thead>
			<tr>
				<th width="1%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_NUM' ); ?></th>
				<th width="1%"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>

				<th width="30%"><?php echo JText::_( 'COM_KOMENTO_COLUMN_COMMENT' ); ?></th>

				<th width="5%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_STATUS' ), 'published', $this->orderDirection, $this->order ); ?></th>

				<th width="5%"><?php echo JHTML::_( 'grid.sort', JText::_( 'COM_KOMENTO_COLUMN_REPORT_COUNT' ) , 'reports', $this->orderDirection, $this->order ); ?></th>

				<th width="5%" class="center"><?php echo JText::_('COM_KOMENTO_COLUMN_EDIT'); ?></th>

				<th width="10%" class="center"><?php echo JText::_('COM_KOMENTO_COLUMN_COMPONENT'); ?></th>

				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ARTICLE' ), 'cid', $this->orderDirection, $this->order ); ?></th>

				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_DATE' ), 'created', $this->orderDirection, $this->order ); ?></th>

				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_AUTHOR' ) , 'created_by', $this->orderDirection, $this->order ); ?></th>
				
				<th width="1%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_KOMENTO_COLUMN_ID' ) , 'id', $this->orderDirection, $this->order ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php echo $this->loadTemplate( 'list' ); ?>
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
