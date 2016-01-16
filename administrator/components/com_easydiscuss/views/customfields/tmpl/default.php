<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

$headerSpan = ( DiscussHelper::getJoomlaVersion() >= '3.0' ) ? '11' : '12';

//$saveOrder	= $this->order == 'a.ordering';

$ordering		= ($this->order == 'a.ordering');
$saveOrder		= ($this->order == 'a.ordering' && $this->orderDirection == 'asc');
$originalOrders	= array();

?>
<script type="text/javascript">
EasyDiscuss(function($){
	$.Joomla( 'submitbutton' , function(action){
		if ( action != 'remove' || confirm('<?php echo JText::_("COM_EASYDISCUSS_CUSTOMFIELDS_DELETE_CONFIRM", true); ?>')) {
			$.Joomla( 'submitform' , [action] );
		}
	});
});
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_CUSTOM_FIELDS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_CUSTOM_FIELDS_DESC' );?>
		</p>
	</div>
</div>

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
			<th class="title" style="text-align: left;"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_TITLE') , 'title', $this->orderDirection, $this->order ); ?>
			</th>
			<th width="1%" nowrap="nowrap" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHED' ); ?></th>
			<th class="center" width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_TYPE' ) , 'user_id', $this->orderDirection, $this->order ); ?></th>
			<th width="5%" class="center">
				<?php echo JHtml::_('grid.sort', JText::_('COM_EASYDISCUSS_ORDERING'), 'a.ordering', $this->orderDirection, $this->order); ?>
				<?php if ($saveOrder){ ?>
					<?php echo JHtml::_('grid.order',  $this->customs); ?>
				<?php } ?>
			</th>
			<th width="1%"><?php echo JText::_( 'COM_EASYDISCUSS_ID' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( $this->customs )
	{

		$k = 0;
		$x = 0;
		for ($i=0, $n=count($this->customs); $i < $n; $i++)
		{
			$row 	= $this->customs[$i];

			$orderkey		= array_search($row->id, $this->ordering[0]);
		?>
		<tr>
			<td width="7">
				<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
			</td>
			<td align="left">
				<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&amp;view=customfields&amp;layout=form&amp;id='. $row->id); ?>"><?php echo $row->title; ?></a>
			</td>
			<td class="center">
				<?php echo JHTML::_('grid.published', $row, $i ); ?>
			</td>
			<td class="center">
				<?php echo $row->type; ?>
			</td>
			<td class="order center">
				<?php echo $row->ordering; ?>
				
				<?php if ($saveOrder) { ?>
				<?php
					$condition_up 	= isset($this->ordering[0][$orderkey - 1]);
					$condition_down = (($i < $this->pagination->total - 1 || $i + $this->pagination->limitstart < $this->pagination->total - 1) && isset($this->ordering[0][$orderkey + 1]));

					echo DiscussHelper::getHelper( 'HTML' )->get( 'grid.order', $i, $orderkey, $condition_up, $condition_down, 'btn btn-micro jgrid' );
				?>

				<?php $originalOrders[] = $orderkey + 1; ?>
				<?php } ?>
			</td>
			<td class="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php $k = 1 - $k; } ?>
	<?php
	}
	else
	{
	?>
		<tr>
			<td colspan="6" align="center">
				<?php echo JText::_('COM_EASYDISCUSS_NO_CUSTOM_FIELDS_CREATED_YET');?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6">
				<div class="footer-pagination">
					<div class="container"><?php echo $this->pagination->getListFooter(); ?></div>
				</div>
			</td>
		</tr>
	</tfoot>
	</table>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="customfields" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="customfields" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->orderDirection; ?>" />
<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
<?php echo JHtml::_('form.token'); ?>
</form>
