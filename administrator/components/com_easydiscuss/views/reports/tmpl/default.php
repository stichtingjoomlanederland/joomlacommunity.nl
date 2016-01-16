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
JHTML::_('behavior.modal' , 'a.modal' );
?>
<script type="text/javascript">
function reportAction( id )
{
	var actionType  = EasyDiscuss.$('#report-action-' + id).val();

	switch (actionType)
	{
		case "E" :
			if(EasyDiscuss.$('#email-text-' + id).val().length <= 0)
			{
				alert( '<?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_ENTER_CONTENTS' );?>' );
				return false;
			}

			var inputs  = [];

			//post_id
			inputs.push( 'post_id=' + escape( id ) );

			//content
			val = EasyDiscuss.$('#email-text-' + id).val().replace(/"/g, "&quot;");
			val = encodeURIComponent(val);
			inputs.push( 'content=' + escape( val ) );

			disjax.load('Reports', 'ajaxSubmitEmail', inputs);
			break;

		case "D" :

			if( confirm( '<?php echo $this->escape( JText::_( 'COM_EASYDISCUSS_CONFIRM_DELETE_POST') );?>' ) )
			{
				EasyDiscuss.$('#post_id').val(id);
				EasyDiscuss.$('#task').val('deletePost');
				EasyDiscuss.$('#adminForm').submit();
			}

			break;

		case "C" :
			EasyDiscuss.$('#post_id').val(id);
			EasyDiscuss.$('#task').val('removeReports');
			EasyDiscuss.$('#adminForm').submit();
			break;

		case "P" :
			EasyDiscuss.$('#post_id').val(id);
			EasyDiscuss.$('#post_val').val('1');
			EasyDiscuss.$('#task').val('togglePublish');
			EasyDiscuss.$('#adminForm').submit();
			break;

		case "U" :
			EasyDiscuss.$('#post_id').val(id);
			EasyDiscuss.$('#post_val').val('0');
			EasyDiscuss.$('#task').val('togglePublish');
			EasyDiscuss.$('#adminForm').submit();
			break;

		default :
			break;
	}
}

EasyDiscuss(function($){
	$.Joomla( 'submitbutton' , function(action){
		$.Joomla( 'submitform' , [action] );
	});
});
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span12 panel-title">
			<h2><?php echo JText::_( 'COM_EASYDISCUSS_REPORTS_TITLE' );?></h2>
			<p style="margin: 0 0 15px;">
				<?php echo JText::_( 'COM_EASYDISCUSS_REPORTS_DESC' );?>
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
					<?php echo JText::_( 'COM_EASYDISCUSS_FILTER' ); ?>: <?php echo $this->state; ?>
				</div>
			</div>
		</div>
	</div>

		<table class="table table-striped table-discuss">
			<thead>
				<tr>
					<th width="5" class="center">
						<input type="checkbox" name="toggle" class="discussCheckAll" />
					</th>
					<th class="title" nowrap="nowrap" style="text-align:left;"><?php echo JText::_( 'COM_EASYDISCUSS_REPORTED_REASON' ); ?></th>
					<th width="15%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_LAST_REPORTED_BY' ); ?></th>
					<th width="5%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_NUM_REPORT' );?></th>
					<th width="10%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_LAST_REPORT_DATE' ); ?></th>
					<th width="1%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_REPORT_PUBLISHED' ); ?></th>
					<th width="30%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_ACTION' ); ?></th>
					<th width="1%" class="center"><?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ID'), 'a.id', $this->orderDirection, $this->order ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if( $this->reports )
			{
				$k = 0;
				$x = 0;
				$config	= DiscussHelper::getJConfig();
				for ($i=0, $n = count( $this->reports ); $i < $n; $i++)
				{
					$row 		= $this->reports[$i];

					$user		= JFactory::getUser( $row->reporter );
					$editLink	= JRoute::_('index.php?option=com_easydiscuss&controller=reports&task=edit&id='.$row->id);
					$published 	= JHTML::_('grid.published', $row, $i );

					$date = DiscussDateHelper::dateWithOffSet( $row->lastreport );

					$actions	= array();
					$actions[]	= JHTML::_('select.option',  '', '- '. JText::_( 'COM_EASYDISCUSS_SELECT_ACTION' ) .' -' );
					$actions[]	= JHTML::_('select.option',  'D', JText::_( 'COM_EASYDISCUSS_DELETE_POST' ) );
					$actions[]	= JHTML::_('select.option',  'C', JText::_( 'COM_EASYDISCUSS_REMOVE_REPORT' ) );
					$actions[]	= JHTML::_('select.option',  'P', JText::_( 'COM_EASYDISCUSS_REPORT_PUBLISHED' ) );
					$actions[]	= JHTML::_('select.option',  'U', JText::_( 'COM_EASYDISCUSS_REPORT_UNPUBLISHED' ) );

					if($row->user_id != 0)
					{
						$actions[] = JHTML::_('select.option',  'E', JText::_( 'COM_EASYDISCUSS_EMAIL_AUTHOR' ) );
					}
					$actionsDropdown	= JHTML::_('select.genericlist',   $actions, 'report-action-' . $row->id, ' style="width:250px;margin: 0;" size="1" onchange="admin.reports.change(\''. $row->id .'\');"', 'value', 'text', '*' );


					$viewLink = JURI::root() . 'index.php?option=com_easydiscuss&view=post&id=' . $row->id;

					if($row->parent_id != 0)
					{
						$viewLink = JURI::root() . 'index.php?option=com_easydiscuss&view=post&id=' . $row->parent_id . '#' . JText::_('COM_EASYDISCUSS_REPORT_REPLY_PERMALINK') . '-' . $row->id;
					}
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td width="7" class="center">
						<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
					</td>
					<td align="left">
						<?php echo $this->escape( $row->reason ); ?> [ <a href="<?php echo $viewLink;?>" target="_blank"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_POST' ); ?></a> ]
					</td>
					<td align="center" class="center">
						<?php if($row->user_id == 0) : ?>
							<?php echo JText::_('COM_EASYDISCUSS_GUEST'); ?>
						<?php else : ?>
							<?php echo $user->name; ?>
						<?php endif; ?>
					</td>
					<td align="center" class="center">
					<?php if( $row->reportCnt > 1 ) { ?>
						<a class="modal" rel="{handler: 'iframe', size: {x: 1024, y: 375}}" href="index.php?option=com_easydiscuss&view=reports&layout=reasons&id=<?php echo $row->id;?>&tmpl=component&browse=1">
							<?php echo $row->reportCnt; ?>
						</a>
					<?php } else { ?>
						<?php echo $row->reportCnt; ?>
					<?php } ?>
					</td>
					<td align="center" class="center">
						<?php echo DiscussDateHelper::toFormat( $date )?>
					</td>
					<td align="center" class="center">
						<?php echo $published; ?>
					</td>
					<td class="center">
						<div id="action-container-<?php echo $row->id;?>">
							<?php echo $actionsDropdown; ?>
							<input type="button" class="btn btn-medium" name="actions-btn-<?php echo $row->id;?>" id="actions-btn-<?php echo $row->id;?>" value="Submit" onclick="reportAction('<?php echo $row->id;?>'); return false;" />
							<div><span id="report-entry-msg-<?php echo $row->id;?>"></span></div>
							<div id="email-container-<?php echo $row->id;?>" style="display:none;">
								<br />
								<div><?php echo JText::_('COM_EASYDISCUSS_YOUR_TEXT'); ?> : </div>
								<textarea name="email_text" id="email-text-<?php echo $row->id;?>" class="inputbox textarea" style="width:300px;"></textarea>
							</div>
						</div>
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
					<td colspan="9" align="center" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_NO_REPORTS');?>
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
	<input type="hidden" name="view" value="reports" />
	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" id="post_id" name="post_id" value="" />
	<input type="hidden" id="post_val" name="post_val" value="" />
	<input type="hidden" name="controller" value="reports" />
	<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>
