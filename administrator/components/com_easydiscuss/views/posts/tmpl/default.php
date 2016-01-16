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

	$( '#category_id' ).bind( 'change' , function(){

		submitform();
	});

	$.Joomla( 'submitbutton' , function(action)
	{
		if( action == 'showMove' )
		{
			disjax.loadingDialog();
			disjax.load( 'posts' , 'showMoveDialog' );
		}
		else
		{
			if( action == 'movePosts' )
			{
				var newCategory 	= $('#new_category' ).val();

				if( newCategory == 0 )
				{
					$( '#new_category_error' )
						.html( '<?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_SELECT_CATEGORY' );?>' )
						.show();
					return false;
				}

				$( '#adminForm input[name=move_category]' ).val( newCategory );
			}

			if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_POSTS', true); ?>'))
			{
				$.Joomla( 'submitform' , [action] )
			}
		}
	});
});
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span12 panel-title">
			<?php if(! empty($this->parentId)) { ?>
			<h2><?php echo JText::sprintf( 'COM_EASYDISCUSS_POSTS_PARENT_TITLE' , $this->parentTitle );?></h2>
			<p style="margin: 0 0 15px;">
				<?php echo JText::_( 'COM_EASYDISCUSS_POSTS_PARENT_DESC' );?>
			</p>
			<?php } else { ?>
			<h2><?php echo JText::_( 'COM_EASYDISCUSS_POSTS_TITLE' );?></h2>
			<p style="margin: 0 0 15px;">
				<?php echo JText::_( 'COM_EASYDISCUSS_POSTS_DESC' );?>
			</p>
			<?php } ?>
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
					<?php echo JText::_( 'COM_EASYDISCUSS_FILTER' ); ?>: <?php echo $this->state; ?> <?php echo $this->categoryFilter; ?>
				</div>
			</div>
		</div>
	</div>

	<table class="table table-striped table-discuss">
	<thead>
		<tr>
			<th width="1%"><input type="checkbox" name="toggle" class="discussCheckAll" /></th>
			<th style="text-align:left;"><?php echo JHTML::_('grid.sort', 'Title', 'a.title', $this->orderDirection, $this->order ); ?></th>
			<th width="10%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY' ); ?></th>
			<th width="1%"><?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' ); ?></th>
			<th width="1%" nowrap="nowrap" style="text-align:center;"><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHED' ); ?></th>

			<?php if(empty($this->parentId)) : ?>
			<th width="5%" nowrap="nowrap" class="center" style="text-align:center;"><?php echo JText::_( 'COM_EASYDISCUSS_REPLIES' ); ?></th>
			<?php endif; ?>

			<th width="10%" nowrap="nowrap" style="text-align:center;"><?php echo JText::_( 'COM_EASYDISCUSS_IP_ADDRESS' );?></th>
			<th width="3%" nowrap="nowrap" style="text-align:center;"><?php echo JText::_( 'COM_EASYDISCUSS_HITS' );?></th>
			<th width="1%" nowrap="nowrap" style="text-align:center;"><?php echo JText::_( 'COM_EASYDISCUSS_POSTS_VOTES' ); ?></th>
			<th width="10%" nowrap="nowrap" style="text-align:center;"><?php echo JText::_( 'COM_EASYDISCUSS_USER' ); ?></th>
			<th width="10%" nowrap="nowrap" style="text-align:center;"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_DATE'), 'a.created', $this->orderDirection, $this->order ); ?></th>
			<th width="20" nowrap="nowrap" style="text-align:center;"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( $this->posts )
	{
		$k = 0;
		$x = 0;
		$config	= JFactory::getConfig();
		for ($i=0, $n = count( $this->posts ); $i < $n; $i++)
		{
			$row			= $this->posts[$i];
			$creatorName	= '';

			if($row->user_id == '0')
			{
				$creatorName = $row->poster_name;
			}
			else
			{
				$user		 = JFactory::getUser( $row->user_id );
				$creatorName = $user->name;
			}

			$pid = '';
			if(!empty($this->parentId))
			{
				$pid = '&pid=' . $this->parentId;
			}

			// frontend link
			//$editLink	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=ask&id='.$row->id, false, true);
			$editLink	= JURI::root().'index.php?option=com_easydiscuss&view=ask&id='.$row->id;

			// backend link
			$editLink = 'index.php?option=com_easydiscuss&view=post&task=edit&id='.$row->id;

			$date = DiscussDateHelper::dateWithOffSet( $row->created );

			// display only safe content.
			$row->content = strip_tags( $row->content );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td width="7">
				<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
			</td>
			<td align="left">
				<?php if( empty( $this->parentId ) ) { ?>
					<a href="<?php echo $editLink; ?>"><?php echo $row->title; ?></a>
				<?php } else { ?>
					<?php echo $row->title; ?>
				<?php } ?>

				<?php if( $row->password ){ ?>
				<span rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_THIS_POST_PASSWORD_PROTECTED' , true );?>"><i class="icon-lock"></i></span>
				<?php } ?>

				<?php if( !empty( $this->parentId ) ) echo '<br/><br/>' . $row->content; ?>
			</td>
			<td class="center">
				<?php
				$category = DiscussHelper::getTable( 'Category' );
				$category->load( $row->category_id );
				?>
				<a href="index.php?option=com_easydiscuss&view=category&catid=<?php echo $category->id;?>"><?php echo $this->escape( $category->title );?></a>
			</td>
			<td class="center" style="text-align:center;">
				<?php if( DiscussHelper::getJoomlaVersion() <= '1.5' ){ ?>
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo ( $row->featured ) ? 'unfeature' : 'feature';?>')">
						<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/<?php echo ( $row->featured ) ? 'small_default.png' : 'small_default-x.png';?>" width="16" height="16" border="0" />
					</a>
				<?php } else { ?>
					<a class="btn btn-micro jgrid" title=""onclick="return listItemTask('cb<?php echo $i;?>','<?php echo ( $row->featured ) ? 'unfeature' : 'feature';?>')" href="#toggle">
						<?php if( $row->featured ) { ?>
							<i class="icon-star"></i>
						<?php } else { ?>
							<i class="icon-star-empty"></i>
						<?php } ?>
					</a>
				<?php } ?>
			</td>
			<td class="center" style="text-align:center;">

				<?php if( $row->published == DISCUSS_ID_PENDING){ ?>
					<a href="javascript:void(0);" onclick="admin.post.moderate.dialog('<?php echo $row->id;?>');"><img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easydiscuss/themes/default/images/moderate.png" /></a>
				<?php } else { ?>
					<a class="btn btn-micro jgrid" title=""onclick="return listItemTask('cb<?php echo $i;?>','<?php echo ( $row->published ) ? 'unpublish' : 'publish';?>')" href="#toggle">
						<i class="icon-<?php echo $row->published ? 'ok' : 'remove';?>"></i>
					</a>
				<?php } ?>
			</td>

			<?php if( !$this->parentId ){ ?>
			<td align="center" class="center" style="text-align:center;">
				<?php if( $row->cnt > 0 ){ ?>
					<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=posts&pid=' . $row->id ); ?>">
						<?php echo $row->cnt; ?>
						<?php if( $row->pendingcnt > 0) : ?>
							( <?php echo DiscussHelper::getHelper( 'String' )->getNoun( 'COM_EASYDISCUSS_POSTS_PENDING_REPLY' , $row->pendingcnt , true ); ?> )
						<?php endif; ?>
					</a>
				<?php } else { ?>
					<?php echo $row->cnt; ?>
				<?php } ?>
			</td>
			<?php } ?>
			<td class="center" style="text-align:center;">
				<?php if( $row->ip ){ ?>
					<?php echo $row->ip;?>
				<?php } else {  ?>
					<?php echo JText::_( 'COM_EASYDISCUSS_NOT_AVAILABLE' ); ?>
				<?php } ?>
			</td>
			<td class="center" style="text-align:center;">
				<?php echo $row->hits; ?>
			</td>
			<td class="center" style="text-align:center;">
				<?php echo $row->sum_totalvote;?>
			</td>

			<td class="center" style="text-align:center;">
				<?php if( $row->user_id ){ ?><a href="index.php?option=com_easydiscuss&amp;view=user&amp;task=edit"><?php } ?><?php echo $creatorName ?><?php if( $row->user_id ){ ?></a><?php } ?>

				<?php if( $row->user_id == 0){ ?>&lt;<a href="mailto:<?php echo $row->poster_email;?>" target="_blank"><?php echo $row->poster_email;?></a>&gt;<?php } ?>
			</td>

			<td align="center">
				<?php echo DiscussDateHelper::toFormat( $date );?>
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
			<td colspan="11" align="center">
				<?php echo JText::_('COM_EASYDISCUSS_NO_DISCUSSIONS_YET');?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="11">
				<div class="footer-pagination">
					<?php echo $this->pagination->getListFooter(); ?>
				</div>
			</td>
		</tr>
	</tfoot>
	</table>

	<input type="hidden" name="move_category" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="view" value="posts" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="posts" />
	<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->orderDirection; ?>" />
	<input type="hidden" name="pid" value="<?php echo $this->parentId; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
