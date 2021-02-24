<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="moderator" class="tab-pane <?php echo $active == 'moderator' ? 'active in' : '';?>">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_ED_CATEGORIES_ACL_ASSIGNMENT'); ?>

				<div class="panel-body">
					<div>
						<?php
							$moderateLink = '<a href="javascript:void(0)" btn-moderate-posts>' . JText::_('COM_ED_CATEGORY_ACL_MODERATE_POSTS') . '</a>';
							$assignedUserId = $category->getAssignedGroups('assignment', 'user'); 
							$assignedUserId = $assignedUserId ? $assignedUserId[0] : 0;
							echo $this->html('form.moderator', $category->id, 'acl_group_assignment', $assignedUserId); 
						?>
						<div style="margin-top: 10px">
							<?php echo JText::sprintf('COM_ED_CATEGORIES_ACL_ASSIGNMENT_HELP', $moderateLink); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>