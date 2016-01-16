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
		if ( action != 'remove' || confirm('<?php echo JText::_("COM_EASYDISCUSS_ARE_YOU_SURE_CONFIRM_DELETE_SUBSCRIBED_CATEGORIES", true); ?>') ) {
			$.Joomla( 'submitform' , [action] );
		}
	});
});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span12 panel-title">
			<h2><?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION_TITLE' );?></h2>
			<p style="margin: 0 0 15px;">
				<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION_DESC' );?>
			</p>
		</div>
	</div>

	<div class="row-fluid filter-bar">
		<div class="pa-10">
			<div class="span12">
				<div class="pull-left form-inline">
					<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="input-medium" onchange="document.adminForm.submit();" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_SEARCH' , true );?>"/>
					<button class="btn btn-success" type="submit" onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH' ); ?></button>
					<button class="btn" type="submit" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_RESET' ); ?></button>
				</div>

				<div class="pull-right">
					<?php echo JText::_( 'COM_EASYDISCUSS_FILTER' ); ?>: <?php echo $this->filterList; ?>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-striped table-discuss">
	<thead>
		<tr>
			<th width="1%" class="center" style="text-align:center;">
				<input type="checkbox" name="toggle" class="discussCheckAll" />
			</th>
			<?php if($this->filter == 'post') : ?>
				<th style="text-align: left;"><?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_DISCUSSION_TITLE', 'bname', $this->orderDirection, $this->order ); ?></th>
			<?php endif; ?>
			<?php if($this->filter == 'category' ) : ?>
				<th style="text-align: left;"><?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_CATEGORY_TITLE', 'c.title', $this->orderDirection, $this->order ); ?></th>
			<?php endif; ?>
			<th width="30%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBER_EMAIL' ); ?></th>
			<th width="30%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBER_NAME' ); ?></th>
			<th width="15%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION_DATE' ); ?></th>
			<th width="6%" class="center" style="text-align:center;"><?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( $this->subscriptions )
	{
		$k = 0;
		$x = 0;
		for ($i=0, $n=count($this->subscriptions); $i < $n; $i++)
		{
			$row = $this->subscriptions[$i];
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td style="text-align:center;" class="center"><?php echo JHTML::_('grid.id', $x++, $row->id); ?></td>
			<?php if($this->filter != 'site') : ?>
				<td><?php echo $row->bname;?></td>
			<?php endif;?>
			<td align="center"><?php echo $row->email;?></td>
			<td align="center"><?php echo (empty($row->name)) ? $row->fullname :  $row->name;?></td>
			<td align="center"><?php echo $row->created; ?></td>
			<td width="1%" class="center" style="text-align:center;"><?php echo $row->id;?></td>
		</tr>
		<?php $k = 1 - $k; } ?>
	<?php
	}
	else
	{
	?>
		<tr>
			<td colspan="<?php echo $this->filter != 'site' ? 6 : 5;?>" align="center">
				<?php echo JText::_('COM_EASYDISCUSS_NO_SUBSCRIPTION_FOUND');?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="<?php echo $this->filter != 'site' ? 6 : 5;?>">
				<div class="footer-pagination">
					<?php echo $this->pagination->getListFooter(); ?>
				</div>
			</td>
		</tr>
	</tfoot>
	</table>






<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="subscription" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="subscription" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>
