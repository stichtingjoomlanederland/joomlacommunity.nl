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

$ordering		= ($this->order == 'lft');
$saveOrder		= ($this->order == 'lft' && $this->orderDirection == 'asc');
$originalOrders	= array();
?>

<script type="text/javascript">
EasyDiscuss.ready(function($){


	window.deleteConfirm	= function()
	{
		if( confirm( '<?php echo JText::_( 'COM_EASYDISCUSS_SPOOLS_CONFIRM_DELETE');?>' ) )
		{
			return true;
		}
		return false;
	}

	window.purgeConfirm	= function(){
		if( confirm( '<?php echo JText::_( 'COM_EASYDISCUSS_SPOOLS_CONFIRM_PURGE');?>' ) )
		{
			return true;
		}
		return false;
	}

	$.Joomla( 'submitbutton' , function(action){

		if( action == 'purge')
		{
			if( !purgeConfirm() )
			{
				return false;
			}
		}

		if( action == 'remove' )
		{
			if( !deleteConfirm() )
			{
				return false;
			}
		}

		$.Joomla( 'submitform' , [action] );

	});

});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SPOOLS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SPOOLS_DESC' ); ?>
		</p>
		<a href="http://stackideas.com/docs/easydiscuss/cronjobs" target="_blank" class="btn btn-success"><?php echo JText::_( 'COM_EASYDISCUSS_DOCUMENTATION_BUTTON' );?></a>
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
					<?php echo JText::_( 'COM_EASYDISCUSS_FILTER' ); ?>: <?php echo $this->state; ?>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-striped table-discuss">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="toggle" class="discussCheckAll" />
				</th>
				<th class="title" style="text-align: left;" width="10%"><?php echo JText::_( 'COM_EASYDISCUSS_RECIPIENT' ); ?></th>
				<th><?php echo JText::_( 'COM_EASYDISCUSS_SUBJECT' ); ?></th>
				<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_STATE' ); ?></th>
				<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_CREATED' ); ?></th>
				<th width="1%"><?php echo JText::_( 'COM_EASYDISCUSS_ID' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if( $this->mails )
		{

			$k = 0;
			$x = 0;
			for ($i=0, $n=count($this->mails); $i < $n; $i++)
			{
				$row	= $this->mails[$i];
				$date	= DiscussDateHelper::getDate( $row->created );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo JHTML::_('grid.id', $x++, $row->id); ?></td>
				<td><?php echo $row->recipient;?></td>
				<td>
					<?php echo $row->subject;?>
				</td>
				<td style="text-align:center;">
					<?php if( $row->status ){ ?>
						<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/favicons/easydiscuss-tick.png" title="<?php echo JText::_( 'COM_EASYDISCUSS_SENT' );?>">
					<?php } else { ?>
						<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/favicons/easydiscuss-schedule.png" title="<?php echo JText::_( 'COM_EASYDISCUSS_PENDING' );?>">
					<?php } ?>
				</td>
				<td style="text-align:center;"><?php echo $date->toMySQL(true); ?></td>
				<td align="center"><?php echo $row->id;?></td>
			</tr>
			<?php $k = 1 - $k; } ?>
		<?php
		}
		else
		{
		?>
			<tr>
				<td colspan="6" align="center">
					<?php echo JText::_('COM_EASYDISCUSS_NO_MAILS');?>
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
						<?php echo $this->pagination->getListFooter(); ?>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="spools" />
<input type="hidden" name="controller" value="spools" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
