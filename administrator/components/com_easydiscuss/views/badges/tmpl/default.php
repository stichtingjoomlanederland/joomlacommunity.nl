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

if( !$prefix = JRequest::getCmd('prefix') ){
	$prefix = '';
}

?>
<script type="text/javascript">
EasyDiscuss.ready(function($){

	$.Joomla( 'submitbutton' , function( action ){

		if( action == 'rules' )
		{
			window.location.href	= 'index.php?option=com_easydiscuss&view=rules&from=badges';
			return;
		}

		$.Joomla( 'submitform' , [action] );

	});

});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<?php if( !$this->browse ){ ?>
	<div class="row-fluid">
		<div class="span12 panel-title">
			<h2><?php echo JText::_( 'COM_EASYDISCUSS_BADGES_TITLE' );?></h2>
			<p style="margin: 0 0 15px;">
				<?php echo JText::_( 'COM_EASYDISCUSS_BADGES_DESC' );?>
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
				<?php if( !$this->browse ){ ?>
				<th width="1%" class="center" style="text-align:center;">
					<input type="checkbox" name="toggle" class="discussCheckAll" />
				</th>
				<?php } ?>

				<th class="title" style="text-align: left;">
					<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_BADGE_TITLE'), 'a.title', $this->orderDirection, $this->order ); ?>
				</th>

				<?php if( !$this->browse ){ ?>
				<th width="1%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHED' ); ?></th>
				<th width="1%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_ACHIEVERS' ); ?></th>
				<?php } ?>

				<th width="10%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_THUMBNAIL' ); ?></th>

				<?php if( !$this->browse ){ ?>
				<th width="10%" nowrap="nowrap" style="text-align: center;">
					<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_DATE'), 'a.created', $this->orderDirection, $this->order ); ?>
				</th>
				<?php } ?>

				<th width="6%" class="center" style="text-align:center;">
					<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ID'), 'a.id', $this->orderDirection, $this->order ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if( $this->badges )
		{
			$k = 0;
			$x = 0;
			$config	= DiscussHelper::getJConfig();
			for ($i=0, $n = count( $this->badges ); $i < $n; $i++)
			{
				$row	= $this->badges[$i];
				$date	= DiscussHelper::getDate( $row->created , $config->get('offset') );

				$editLink	= $this->browseFunction ? 'javascript:parent.' . $this->browseFunction . '(' . $row->id . ')' : JRoute::_( 'index.php?option=com_easydiscuss&view=badge&id=' . $row->id );
			?>
			<tr class="<?php echo "row$k"; ?>">

				<?php if( !$this->browse ){ ?>
				<td class="center" style="text-align: center;">
					<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
				</td>
				<?php } ?>

				<td align="left">
					<a href="<?php echo $editLink; ?>"><?php echo $row->title; ?></a>
				</td>

				<?php if( !$this->browse ){ ?>
				<td style="text-align:center;" class="center">
					<?php echo JHTML::_('grid.published', $row, $i ); ?>
				</td>

				<td class="center">
					<?php echo $this->getTotalUsers( $row->id ); ?>
				</td>
				<?php } ?>

				<td class="center">
					<img src="<?php echo JURI::root();?>/media/com_easydiscuss/badges/<?php echo $row->avatar;?>" width="32" />
				</td>

				<?php if( !$this->browse ){ ?>
				<td class="center" style="text-align:center;">
					<?php echo $date->toMySQL( true );?>
				</td>
				<?php } ?>

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
					<?php echo JText::_('COM_EASYDISCUSS_NO_BADGES_YET');?>
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

<?php if( $this->browse ){ ?>
<input type="hidden" name="browse" value="1" />
<input type="hidden" name="browseFunction" value="<?php echo $this->browseFunction; ?>" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="prefix" value="<?php echo $prefix; ?>" />
<?php } ?>


<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="badges" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="badges" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
