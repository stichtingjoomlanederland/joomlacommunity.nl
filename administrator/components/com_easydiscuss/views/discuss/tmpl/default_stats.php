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
?>
<div class="widget">
	<div class="whead"><h6><?php echo JText::_( 'COM_EASYDISCUSS_STATISTICS' );?></h6>
	</div>
	<table cellpadding="0" cellspacing="0" width="100%" class="si-table">
		<tbody>
			<tr>
				<td><?php echo JText::_('COM_EASYDISCUSS_STATS_TOTAL_DISCUSSIONS'); ?></td>
				<td>
					<?php echo $this->getTotalPosts(); ?>
				</td>
			</tr>
			<?php if ($this->config->get('main_qna')) { ?>
			<tr>
				<td><?php echo JText::_('COM_EASYDISCUSS_STATS_TOTAL_RESOLVED'); ?></td>
				<td>
					<?php echo $this->getTotalSolved(); ?>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="no-borderB"><?php echo JText::_('COM_EASYDISCUSS_STATS_TOTAL_REPLIES'); ?></td>
				<td class="no-borderB">
					<?php echo $this->getTotalReplies(); ?>
				</td>
			</tr>

		</tbody>
	</table>
</div>