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

if( !$prefix = JRequest::getCmd('prefix') ){
	$prefix = '';
}
?>
<script type="text/javascript">
EasyDiscuss(function($){
	$.Joomla( 'submitbutton' , function( action ){
		if ( action != 'remove' || confirm('<?php echo JText::_("COM_EASYDISCUSS_ARE_YOU_SURE_CONFIRM_DELETE", true); ?>'))
		{
			$.Joomla( 'submitform' , [action] );
		}
	});
})
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="row-fluid filter-bar">
		<div class="pa-10">
			<div class="span12">
				<div class="pull-left form-inline">
					<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="input-medium" onchange="document.adminForm.submit();" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_SEARCH' , true );?>"/>
					<button class="btn btn-success" type="submit" onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH' ); ?></button>
					<button class="btn" type="submit" onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_RESET' ); ?></button>
				</div>

				<?php if( !$this->browse ){ ?>
				<div class="pull-right">
					<?php echo JText::_( 'COM_EASYDISCUSS_FILTER' ); ?>: <?php echo $this->state; ?>
				</div>
				<?php }?>
			</div>
		</div>
	</div>

	<?php if( !$this->browse ){ ?>
	<div class="notice mb-10" style="text-align: left !important;">
		<span class="label label-important mr-5"><?php echo JText::_( 'COM_EASYDISCUSS_IMPORTANT' );?></span> <?php echo JText::_('COM_EASYDISCUSS_USERS_MANAGEMENT_DELETE_NOTICE');?></div>
	<?php } ?>

	<table class="table table-striped table-discuss">
	<thead>
		<tr>
			<?php if(empty($this->browse)) : ?>
			<th width="1%">
				<input type="checkbox" name="toggle" class="discussCheckAll" />
			</th>
			<?php endif; ?>
			<th style="text-align: left;"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_NAME'), 'a.name', $this->orderDirection, $this->order ); ?></th>
			<th style="text-align: left;"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_USERNAME'), 'a.username', $this->orderDirection, $this->order ); ?></th>

			<?php if( !$this->browse ) { ?>
			<th style="text-align: left;"><?php echo JText::_('COM_EASYDISCUSS_GROUP'); ?></th>

			<th style="text-align: left;"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_EMAIL'), 'a.email', $this->orderDirection, $this->order ); ?></th>
			<th width="50px" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_DISCUSSIONS' ); ?></th>
			<?php } ?>
			<th width="6%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', Jtext::_('COM_EASYDISCUSS_ID'), 'a.id', $this->orderDirection, $this->order ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( $this->users )
	{
		$k = 0;
		$x = 0;
		for ($i=0, $n=count($this->users); $i < $n; $i++)
		{
			$row = $this->users[$i];

			$img			= $row->block ? 'publish_x.png' : 'tick.png';
			$alt			= $row->block ? JText::_( 'Blocked' ) : JText::_( 'Enabled' );

			$editLink		= 'index.php?option=com_easydiscuss&amp;controller=blogs&amp;task=edit&amp;blogid=' . $row->id;
			$previewLink	= rtrim( JURI::root() , "/" ) . "/" . JRoute::_("index.php?option=com_easydiscuss&view=entry&id=" . $row->id);
			$preview		= '<a href="' . $previewLink .'" target="_blank"><img src="'.JURI::base().'/images/preview_f2.png"/ style="width:20px; height:20px; "></a>';
		?>
		<tr class="<?php echo "row$k"; ?>">
			<?php if(empty($this->browse)) { ?>
			<td width="7">
				<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
			</td>
			<?php } ?>
			<td>
			<?php if( $this->browse ) { ?>
				<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo addslashes($this->escape($row->name));?>','<?php echo $prefix; ?>');"><?php echo $row->name;?></a>
			<?php } else { ?>
				<a href="index.php?option=com_easydiscuss&controller=user&id=<?php echo $row->id;?>&task=edit"><?php echo $row->name;?></a>
			<?php } ?>
			</td>
			<td>
				<?php echo $row->username;?>
			</td>
			<?php if( !$this->browse ) { ?>
			<td>
				<?php echo (DiscussHelper::getJoomlaVersion() >= '1.6') ? $row->usergroups : $row->usertype;?>
			</td>
			
			<td>
				<?php echo $row->email;?>
			</td>
			<td class="center" style="text-align:center;">
				<?php echo $this->getTotalTopicCreated( $row->id );?>
			</td>
			<?php } ?>
			<td class="center" style="text-align:center;">
				<?php echo $row->id;?>
			</td>
		</tr>
		<?php $k = 1 - $k; } ?>
	<?php } else { ?>
		<tr>
			<td colspan="7" align="center">
				<?php echo JText::_('No user created yet.');?>
			</td>
		</tr>
	<?php } ?>
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
<input type="hidden" name="browsefunction" value="<?php echo $this->browsefunction; ?>" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="prefix" value="<?php echo $prefix; ?>" />
<?php } ?>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="users" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="user" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
