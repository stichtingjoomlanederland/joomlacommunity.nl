<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');


$headerSpan = ( DiscussHelper::getJoomlaVersion() >= '3.0' ) ? '11' : '12';

$ordering		= ($this->order == 'lft');
$saveOrder		= ($this->order == 'lft' && $this->orderDirection == 'asc');
$originalOrders	= array();
?>
<script type="text/javascript">
EasyDiscuss(function($){
	$.Joomla( 'submitbutton' , function(action){
		if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYDISCUSS_ARE_YOU_SURE_CONFIRM_DELETE', true); ?>')) {
			$.Joomla( 'submitform' , [action] );
		}
	});
});
</script>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_DESC' );?>
		</p>
	</div>
</div>
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="row-fluid filter-bar">
		<div class="pa-10">
			<div class="span<?php echo $headerSpan; ?>">
				<div class="pull-left form-inline">
					<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="input-medium" onchange="document.adminForm.submit();" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_SEARCH' , true );?>"/>
					<button class="btn btn-success" type="submit" onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH' ); ?></button>
					<button class="btn" type="submit" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_RESET' ); ?></button>
				</div>

				<div class="pull-right">
					<?php echo JText::_( 'COM_EASYDISCUSS_FILTER' ); ?>: <?php echo $this->state; ?>
				</div>
			</div>

			<?php if( DiscussHelper::getJoomlaVersion() >= '3.0' ) { ?>
			<div class="btn-group pull-right span1">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<table class="table table-striped table-discuss">
	<thead>
		<tr>
			<th width="1%">
				<input type="checkbox" name="toggle" class="discussCheckAll" />
			</th>
			<th class="title" style="text-align: left;"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYDISCUSS_CATEGORIES_CATEGORY_TITLE' ) , 'title', $this->orderDirection, $this->order ); ?></th>
			<th width="5%" class="center"><?php echo JText::_( 'COM_EASYDSCUSS_CATEGORIES_DEFAULT' ); ?></th>
			<th width="5%" nowrap="nowrap" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_PUBLISHED' ); ?></th>
			<th width="100px">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_EASYDISCUSS_ORDER'), 'lft', 'desc', $this->order ); ?>
				<?php echo JHTML::_('grid.order',  $this->categories ); ?>
			</th>
			<th width="5%" nowrap="nowrap" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_ENTRIES' ); ?></th>
			<th width="5%" nowrap="nowrap" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_CHILD_COUNT' ); ?></th>
			<th width="8%" class="center"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYDISCUSS_CATEGORIES_AUTHOR' ) , 'created_by', $this->orderDirection, $this->order ); ?></th>
			<th width="1%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_ID' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( $this->categories )
	{

		$k = 0;
		$x = 0;
		$rows   = $this->categories;

		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $this->categories[$i];

			$link			= 'index.php?option=com_easydiscuss&amp;controller=category&amp;task=edit&amp;catid='. $row->id;
			$previewLink	= JURI::root() . 'index.php?option=com_easydiscuss&amp;view=categories&layout=listing&id=' . $row->id;
			$user			= JFactory::getUser( $row->created_by );

			$orderkey		= array_search($row->id, $this->ordering[$row->parent_id]);
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td class="center"><?php echo JHTML::_('grid.id', $x++, $row->id); ?></td>
			<td align="left">
				<?php echo str_repeat( '|&mdash;' , $row->depth ); ?>
				<span><a href="<?php echo $link; ?>"><?php echo $row->title; ?></a></span>
			</td>
			<td align="center" class="center">
				<?php echo DiscussHelper::getHelper( 'HTML' )->get( 'grid.makeDefault', $row->default, $row->id, 'category', 'makeDefault', 'btn btn-micro jgrid' ); ?>
			</td>
			<td align="center" class="center">
				<?php echo DiscussHelper::getHelper( 'HTML' )->get( 'grid.published', $row, $i, 'btn btn-micro jgrid' ); ?>
			</td>
			<td class="order">
				<?php
					$condition_up = isset($this->ordering[$row->parent_id][$orderkey - 1]);
					$condition_down = (($i < $this->pagination->total - 1 || $i + $this->pagination->limitstart < $this->pagination->total - 1) && isset($this->ordering[$row->parent_id][$orderkey + 1]));

					echo DiscussHelper::getHelper( 'HTML' )->get( 'grid.order', $i, $orderkey, $condition_up, $condition_down, 'btn btn-micro jgrid' ); ?>

				<?php $originalOrders[] = $orderkey + 1; ?>
			</td>
			<td align="center" class="center">
				<?php echo $row->count;?>
			</td>
			<td align="center" class="center">
				<?php echo $row->child_count; ?>
			</td>
			<td align="center" class="center">
				<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo $user->name; ?></a>
			</td>
			<td align="center" class="center"><?php echo $row->id;?></td>
		</tr>
		<?php $k = 1 - $k; } ?>
	<?php
	}
	else
	{
	?>
		<tr>
			<td colspan="9" align="center">
				<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_NO_CATEGORY_CREATED_YET');?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="9">
				<div class="footer-pagination">
					<?php echo $this->pagination->getListFooter(); ?>
				</div>
			</td>
		</tr>
	</tfoot>

	</table>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="categories" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="category" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->orderDirection; ?>" />
<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
