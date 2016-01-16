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
EasyDiscuss.ready(function($){

	$.Joomla( 'submitbutton' , function( action ){

		$.Joomla( 'submitform' , [action] );

	});

});
</script>


<form action="index.php" method="post" name="adminForm" id="adminForm">

	<?php if( !$this->browse ){ ?>
	<div class="row-fluid">
		<div class="span12 panel-title">
			<h2><?php echo JText::_( 'COM_EASYDISCUSS_POST_TYPES_TITLE' );?></h2>
			<p style="margin: 0 0 15px;">
				<?php echo JText::_( 'COM_EASYDISCUSS_POST_TYPES_TITLE_DESC' );?>
			</p>
		</div>
	</div>
	<?php } ?>
		<?php if( !$this->browse ){ ?>

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
		<?php } ?>

		<table class="table table-striped table-discuss">
		<thead>
			<tr>
				<th width="1%" class="center">
					<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->postTypes ); ?>);" />
				</th>
				<th class="title"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ADMIN_POST_TYPES_TITLE'), 'a.title', $this->orderDirection, $this->order ); ?></th>
				<th width="1%" class="center"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ADMIN_POST_TYPES_PUBLISHED'), 'a.published', $this->orderDirection, $this->order ); ?></th>
				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ADMIN_POST_TYPES_SUFFIX'), 'a.suffix', $this->orderDirection, $this->order ); ?></th>
				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ADMIN_POST_TYPES_CREATED'), 'a.created', $this->orderDirection, $this->order ); ?></th>
				<th width="10%" class="center"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ADMIN_POST_TYPES_ALIAS'), 'a.alias', $this->orderDirection, $this->order ); ?></th>
				<th width="1%" class="center"><?php echo JHTML::_('grid.sort', JText::_('Id'), 'a.id', $this->orderDirection, $this->order ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if( $this->postTypes )
		{
			$k = 0;
			$x = 0;
			$config	= DiscussHelper::getJConfig();
			for ($i=0, $n = count( $this->postTypes ); $i < $n; $i++)
			{
				$row	= $this->postTypes[$i];
				$date	= DiscussHelper::getDate( $row->created , $config->get('offset') );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td class="center" style="text-align: center;">
					<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
				</td>

				<td align="left">
					<a href="<?php echo 'index.php?option=com_easydiscuss&view=post_types&layout=form&id=' . $row->id; ?>"><?php echo $row->title; ?></a>
				</td>

				<td class="center">
					<?php echo JHTML::_('grid.published', $row, $i ); ?>
				</td>

				<td class="center">
					<?php if( !empty( $suffix ) ){ ?>
						<?php echo $row->suffix; ?>
					<?php } else { ?>
						<?php echo JText::_( 'COM_EASYDISCUSS_NOT_AVAILABLE' ); ?>
					<?php } ?>
				</td>

				<td class="center">
					<?php echo $row->created; ?>
				</td>

				<td class="center">
					<?php echo $row->alias; ?>
				</td>
				<td class="center" style="text-align:center;">
					<?php echo $row->id;?>
				</td>


			</tr>
			<?php $k = 1 - $k; } ?>
		<?php
		}
		else
		{
		?>
			<tr>
			<td colspan="7" align="center">
					<?php echo JText::_('COM_EASYDISCUSS_NO_POST_TYPES_YET');?>
				</td>
			</tr>
		<?php
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7">

					<div class="footer-pagination">
						<?php echo $this->pagination->getListFooter(); ?>
					</div>
				</td>
			</tr>
		</tfoot>
		</table>





<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="post_types" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="post_types" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
