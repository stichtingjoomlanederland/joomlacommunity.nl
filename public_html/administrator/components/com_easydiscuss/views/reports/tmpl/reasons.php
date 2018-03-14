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
		<table class="table table-striped table-discuss">
			<thead>
				<tr>
					<th width="5" class="center">#</th>
					<th class="title" nowrap="nowrap" style="text-align:left;"><?php echo JText::_( 'COM_EASYDISCUSS_REPORTED_REASON' ); ?></th>
					<th width="15%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_REPORTED_BY' ); ?></th>
					<th width="15%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_REPORT_DATE' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if( $this->reasons )
			{
				$k = 0;
				$x = 0;
				$config	= DiscussHelper::getJConfig();
				for ($i=0, $n = count( $this->reasons ); $i < $n; $i++)
				{
					$row 		= $this->reasons[$i];
					$user		= JFactory::getUser( $row->created_by );
					$date = DiscussDateHelper::dateWithOffSet( $row->created );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td width="7" class="center">
						<?php echo $i+1; ?>
					</td>
					<td align="left">
						<?php echo $this->escape( $row->reason ); ?>
					</td>
					<td align="center" class="center">
						<?php if($row->created_by == 0) : ?>
							<?php echo JText::_('COM_EASYDISCUSS_GUEST'); ?>
						<?php else : ?>
							<?php echo $user->name; ?>
						<?php endif; ?>
					</td>
					<td align="center" class="center">
						<?php echo DiscussDateHelper::toFormat( $date )?>
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
		</table>