<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-content-table" data-maintenance-sync>
	<table class="app-table table">
			<tr>
				<td>
					<a href="javascript:void(0);" class="btn btn-success" data-start><?php echo JText::_('COM_ED_MAINTENANCE_SYNC_START'); ?></a>

					<div class="mt-20" data-progress style="display: none;">
						<div class="ed-progress-wrap">
							<div class="progress progress-info" data-progress-box>
								<div class="progress-bar" style="width: 0%" data-progress-bar></div>
								<div class="progress-result" data-progress-percentage >0%</div>
							</div>
						</div>

						<div class="mt-20" data-error style="display: none;">
							<h4><?php echo JText::_('COM_ED_MAINTENANCE_SYNC_ERROR_OCCURED'); ?></h4>
						</div>

						<div class="mt-20" data-success style="display: none;">
							<h4><?php echo JText::_('COM_ED_MAINTENANCE_SYNC_SUCCESS'); ?></h4>

							<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss');?>" class="btn btn-default">
								<?php echo JText::_('COM_ED_MAINTENANCE_BACK_BUTTON');?>
							</a>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
