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
?>
<script type="text/javascript">
EasyDiscuss(function($){
	$.Joomla( 'submitbutton' , function(action){
		$.Joomla( 'submitform' , [action] );
	});
});
</script>
<div class="adminform-body">
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="row-fluid">
		<div class="span12 panel-title">
			<h2><?php echo JText::_( 'COM_EASYDISCUSS_MANAGE_RULES' );?></h2>
			<p style="margin: 0 0 15px;">
				<?php echo JText::_( 'COM_EASYDISCUSS_MANAGE_RULES_DESC' );?>
			</p>
		</div>
	</div>

	<table class="table table-striped table-discuss" cellspacing="1">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th width="5">
				<input type="checkbox" name="toggle" class="discussCheckAll" />
			</th>
			<th class="title" style="text-align:left;"><?php echo JHTML::_('grid.sort', 'Title', 'a.title', $this->orderDirection, $this->order ); ?></th>
			<th width="1%"><?php echo JText::_( 'Command' ); ?></th>
			<th width="10%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('Date'), 'a.created', $this->orderDirection, $this->order ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( $this->rules )
	{
		$k = 0;
		$x = 0;
		$config	= DiscussHelper::getJConfig();
		for ($i=0, $n = count( $this->rules ); $i < $n; $i++)
		{
			$row 		= $this->rules[$i];
			$date		= DiscussHelper::getDate( $row->created );

			$date->setOffset(  $config->get('offset')  );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td width="7">
				<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
			</td>
			<td align="left">
				<?php echo $row->title; ?>
			</td>
			<td align="center">
				<?php echo $row->command;?>
			</td>
			<td align="center">
				<?php echo $date->toMySQL( true );?>
			</td>
			<td align="center">
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
				<?php echo JText::_('No rules created yet');?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="10">
				<div class="footer-pagination">
					<?php echo $this->pagination->getListFooter(); ?>
				</div>
			</td>
		</tr>
	</tfoot>
	</table>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="rules" />
<input type="hidden" name="view" value="rules" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
