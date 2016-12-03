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

<?php if(! empty($this->parentId)) : ?>
<div><h2><?php echo $this->parentTitle; ?></h2></div>
<?php endif;?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="adminform-head">
		<table class="adminform">
			<tr>
				<td width="40">
					<?php echo JText::_( 'SEARCH' ); ?>
				</td>
				<td width="200">
					<input type="text" name="search" id="search" value="<?php echo $this->search; ?>"  onchange="document.adminForm.submit();" style="width: 200px;" />
				</td>
				<td>
					<button onclick="this.form.submit();"><?php echo JText::_( 'SEARCH' ); ?></button>
					<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
				</td>
				<td width="200" nowrap="nowrap" style="text-align: right;">
				  <?php echo $this->state; ?>
				</td>
			</tr>
		</table>
	</div>

	<div class="adminform-body">
		<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="5">
						<?php echo JText::_( 'Num' ); ?>
					</th>
					<th width="5">
						<input type="checkbox" name="toggle" class="discussCheckAll" />
					</th>
					<th class="title"><?php echo JHTML::_('grid.sort', 'Title', 'a.title', $this->orderDirection, $this->order ); ?></th>
					<th width="1%"><?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' ); ?></th>
					<th width="1%" nowrap="nowrap"><?php echo JText::_( 'Published' ); ?></th>
					<th width="3%" nowrap="nowrap"><?php echo JText::_( 'Hits' );?></th>
					<th width="10%" nowrap="nowrap"><?php echo JText::_( 'User' ); ?></th>
					<th width="10%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('Date'), 'a.created', $this->orderDirection, $this->order ); ?></th>
					<th width="20" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if( $this->posts )
			{
				$k = 0;
				$x = 0;
				$config	= DiscussHelper::getJConfig();
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

					$editLink	= JRoute::_('index.php?option=com_easydiscuss&controller=replies&task=edit&id='.$row->id.$pid);

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
						<span class="editlinktip hasTip">
							<a href="<?php echo $editLink; ?>"><?php echo $row->title; ?></a>
						</span>
					</td>
					<td align="center">
						<?php if( DiscussHelper::getJoomlaVersion() <= '1.5' ){ ?>
							<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo ( $row->featured ) ? 'unfeature' : 'feature';?>')">
								<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/<?php echo ( $row->featured ) ? 'small_default.png' : 'small_default-x.png';?>" width="16" height="16" border="0" />
							</a>
						<?php } else { ?>
							<?php echo JHTML::_( 'grid.boolean' , $i , $row->featured , 'feature' , 'unfeature' ); ?>
						<?php } ?>
					</td>
					<td align="center">
						<?php if( $row->published == DISCUSS_ID_PENDING){ ?>
							<a href="javascript:void(0);" onclick="admin.post.moderate.dialog('<?php echo $row->id;?>');"><img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easydiscuss/themes/default/images/moderate.png" /></a>
						<?php } else { ?>
							<?php echo JHTML::_('grid.published', $row, $i ); ?>
						<?php } ?>
					</td>
					<td align="center">
						<?php echo $row->hits; ?>
					</td>
					<td align="center">
						<span class="editlinktip hasTip">
							<!-- a href="<?php echo JRoute::_('index.php?option=com_users&cid[]=' . $row->user_id . '&task=edit'); ?>"></a -->
							<?php echo $creatorName ?>
						</span>
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
					<td colspan="9" align="center">
						<?php echo JText::_('COM_EASYDISCUSS_NO_DISCUSSIONS_YET');?>
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
	</div>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="view" value="replies" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="replies" />
	<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
