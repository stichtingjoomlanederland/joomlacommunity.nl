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
<div id="toolbar-actions" class="btn-wrapper t-hidden" data-ed-admin-actions>
	<div class="dropdown">
		<button type="button" class="btn btn-small dropdown-toggle" data-toggle="dropdown">
			<span class="icon-cog"></span> <?php echo JText::_('Post Actions');?> &nbsp;<span class="caret"></span>
		</button>

		<ul class="dropdown-menu">
			<li>
				<a href="javascript:void(0);" data-ed-action="posts.delete">
					<?php echo JText::_('COM_EASYDISCUSS_DELETE_POST'); ?>
				</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="javascript:void(0);" data-ed-action="publish">
					<?php echo JText::_('COM_EASYDISCUSS_REPORT_PUBLISHED'); ?>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" data-ed-action="unpublish">
					<?php echo JText::_('COM_EASYDISCUSS_REPORT_UNPUBLISHED'); ?>
				</a>
			</li>
		</ul>
	</div>
</div>